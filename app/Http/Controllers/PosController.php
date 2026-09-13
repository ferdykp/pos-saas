<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Services\CashLedger;
use App\Services\CustomerPoints;
use App\Services\MidtransGateway;
use App\Services\OrderPaymentService;
use App\Services\OrderPricing;
use App\Services\RecipeStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()?->tenant_id;
        $userId = auth()->id();

        $categories = Category::where('tenant_id', $tenantId)->get();
        $settings = Setting::where('tenant_id', $tenantId)->pluck('value', 'key')->toArray();
        $pricing = app(OrderPricing::class);
        $products = Product::where('tenant_id', $tenantId)->where('is_active', true)
            ->with(['discounts', 'variants', 'addons'])->get()->map(function ($product) use ($pricing) {
                $unit = $pricing->unitPrice($product);
                $product->sell_price = $unit['price'];
                $product->final_price = $unit['final'];
                $product->discount_applied = $unit['discount'];
                $product->discount_name = $unit['discount_name'];
                foreach ($product->variants as $variant) {
                    $variantUnit = $pricing->unitPrice($product, (float) $variant->price);
                    $variant->discount = $variantUnit['discount'];
                }

                return $product;
            });

        $customers = Customer::where('tenant_id', $tenantId)->get();

        $activeShift = Shift::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($activeShift instanceof Shift) {
            /** @var Shift $activeShift */
            session(['active_shift_id' => $activeShift->id]);
            $hasShift = true;
        } else {
            session()->forget('active_shift_id');
            $hasShift = false;
        }

        $pendingPayments = Order::where('tenant_id', $tenantId)->where('user_id', $userId)
            ->where('payment_method', 'midtrans')->where('payment_status', 'unpaid')
            ->where('order_status', '!=', 'cancelled')->whereNotNull('qr_url')->latest()->get()
            ->map(fn (Order $order) => ['order_id' => $order->id, 'invoice_number' => $order->invoice_number,
                'grand_total' => (float) $order->grand_total, 'qr_url' => $order->qr_url]);

        return view('pos.index', compact('customers', 'categories', 'products', 'settings', 'hasShift', 'activeShift', 'pendingPayments'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $data = $request->validate([
            'items' => 'required|array|min:1|max:200',
            'items.*.id' => 'required|integer',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.addon_ids' => 'nullable|array|max:20',
            'items.*.addon_ids.*' => 'integer',
            'items.*.note' => 'nullable|string|max:300',
            'checkout_key' => 'nullable|uuid',
            'shift_id' => 'nullable|integer',
            'sold_at' => 'nullable|date|before_or_equal:now',
            'items.*.quantity' => 'required|integer|min:1|max:100000',
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')->where('tenant_id', $tenantId)],
            'payment_method' => 'required|in:cash,midtrans',
            'payment_status' => 'required|in:paid,unpaid',
            'paid_amount' => 'required|numeric|min:0|max:999999999999',
            'grand_total' => 'nullable|numeric|min:0|max:999999999999',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'table_number' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:2000',
        ]);
        abort_if($data['payment_method'] === 'midtrans' && Gate::denies('feature-qris'), 403, 'Paket Anda tidak mendukung QRIS.');
        abort_if(! empty($data['customer_id']) && Gate::denies('feature-crm'), 403, 'Paket Anda tidak mendukung CRM.');

        $hash = hash('sha256', json_encode($data));

        return DB::transaction(function () use ($data, $tenantId, $hash) {
            // Serializes the monthly quota and checkout/shift close for this tenant/user.
            $tenant = Tenant::lockForUpdate()->findOrFail($tenantId);
            if (! empty($data['checkout_key'])) {
                $previous = Order::where('tenant_id', $tenantId)->where('checkout_key', $data['checkout_key'])->first();
                if ($previous) {
                    abort_unless($previous->user_id === auth()->id() && $previous->request_hash === $hash, 409, 'Referensi transaksi sudah digunakan dengan isi berbeda.');

                    return $this->receiptResponse($previous);
                }
            }
            if ($tenant->isTransactionLimitReached()) {
                throw ValidationException::withMessages(['items' => 'Batas transaksi paket telah tercapai.']);
            }
            $shift = Shift::where('tenant_id', $tenantId)->where('user_id', auth()->id())->where('status', 'open')->lockForUpdate()->first();
            if (! $shift || (isset($data['shift_id']) && (int) $data['shift_id'] !== $shift->id)) {
                throw ValidationException::withMessages(['shift' => 'Buka shift sebelum melakukan transaksi.']);
            }

            if (! empty($data['sold_at']) && Carbon::parse($data['sold_at'])->lt(Carbon::parse($shift->start_time, config('app.timezone')))) {
                throw ValidationException::withMessages(['sold_at' => 'Waktu transaksi berada di luar shift aktif.']);
            }
            $products = Product::where('tenant_id', $tenantId)->where('is_active', true)
                ->whereIn('id', array_column($data['items'], 'id'))->orderBy('id')->lockForUpdate()->with(['discounts', 'materials'])->get()->keyBy('id');
            $subtotal = $discount = 0;
            $lines = [];
            $materialTotals = [];
            $stockTotals = [];
            foreach ($data['items'] as $item) {
                $product = $products->get($item['id']);
                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak tersedia di toko ini.']);
                }
                $variant = ! empty($item['variant_id']) ? $product->variants()->lockForUpdate()->find($item['variant_id']) : null;
                if (! empty($item['variant_id']) && ! $variant) {
                    throw ValidationException::withMessages(['items' => 'Varian tidak tersedia untuk menu ini.']);
                }
                $addonIds = $item['addon_ids'] ?? [];
                $addons = $product->addons()->whereIn('id', $addonIds)->get();
                if ($addons->count() !== count($addonIds)) {
                    throw ValidationException::withMessages(['items' => 'Tambahan menu tidak valid.']);
                }
                $stockModel = $variant ?? $product;
                $stockKey = ($variant ? 'variant-' : 'product-').$stockModel->id;
                $tracked = $product->type === 'product' && $product->manage_stock;
                $stockTotals[$stockKey] = ($stockTotals[$stockKey] ?? 0) + ($tracked ? $item['quantity'] : 0);
                if ($tracked && $stockModel->stock < $stockTotals[$stockKey]) {
                    throw ValidationException::withMessages(['items' => "Stok {$product->product_name} tidak mencukupi."]);
                }
                $unit = app(OrderPricing::class)->unitPrice($product, $variant ? (float) $variant->price : null);
                $price = $unit['price'] + $addons->sum('price');
                $subtotal += $price * $item['quantity'];
                $discount += $unit['discount'] * $item['quantity'];
                $reserved = [];
                foreach ($product->type === 'service' ? [] : $product->materials as $material) {
                    $reserved[$material->id] = $material->pivot->quantity * $item['quantity'];
                    $materialTotals[$material->id] = ($materialTotals[$material->id] ?? 0) + $reserved[$material->id];
                }
                $costKnown = ! $variant && $product->cost_price > 0 && $addons->every(fn ($addon) => $addon->cost !== null);
                $lines[] = ['requires_preparation' => $product->type === 'product' && (bool) ($product->requires_preparation ?? (auth()->user()->tenant->businessType() === 'food')), 'discount_amount' => $unit['discount'] * $item['quantity'], 'product_id' => $product->id, 'variant_id' => $variant?->id,
                    'product_name' => $product->product_name.($variant ? ' · '.$variant->name : ''),
                    'quantity' => $item['quantity'], 'price' => $price, 'subtotal' => $price * $item['quantity'],
                    'reserved_stock' => $tracked ? $item['quantity'] : 0, 'reserved_materials' => $reserved,
                    'addons' => $addons->map->only(['id', 'name', 'price'])->values()->all(), 'note' => $item['note'] ?? null,
                    'unit_cost' => $costKnown ? $product->cost_price + $addons->sum('cost') : null];
            }
            $settings = Setting::where('tenant_id', $tenantId)->pluck('value', 'key');
            $taxRate = min(100, max(0, (float) ($settings['tax_percentage'] ?? 0)));
            $tax = ($settings['tax_active'] ?? '0') === '1' ? (int) round(($subtotal - $discount) * $taxRate / 100) : 0;
            $total = $subtotal - $discount + $tax;
            if ($total > 999999999999 || (isset($data['grand_total']) && (float) $data['grand_total'] !== (float) $total)) {
                throw ValidationException::withMessages(['grand_total' => 'Total berubah. Muat ulang kasir dan periksa harga terbaru.']);
            }
            $digital = $data['payment_method'] === 'midtrans';
            $paid = ! $digital && $data['payment_status'] === 'paid';
            if ($paid && $data['paid_amount'] < $total) {
                throw ValidationException::withMessages(['paid_amount' => 'Uang tunai kurang.']);
            }
            if ($digital && $total < 1) {
                throw ValidationException::withMessages(['grand_total' => 'Nominal QRIS harus lebih dari nol.']);
            }
            if (! $digital && ! $paid && empty($data['customer_id'])) {
                throw ValidationException::withMessages(['customer_id' => 'Pilih pelanggan untuk transaksi bon.']);
            }

            $order = Order::create([
                'checkout_key' => $data['checkout_key'] ?? null, 'request_hash' => $hash,
                'service_status' => $products->contains(fn ($product) => $product->type === 'service') ? 'queued' : null,
                'kitchen_status' => auth()->user()->tenant->hasBusinessModule('food') && collect($lines)->contains(fn ($line) => $line['requires_preparation']) ? 'queued' : null, 'sold_at' => isset($data['sold_at']) ? Carbon::parse($data['sold_at'])->setTimezone(config('app.timezone')) : now(),
                'tenant_id' => $tenantId, 'user_id' => auth()->id(), 'shift_id' => $shift->id,
                'customer_id' => $data['customer_id'] ?? null,
                'invoice_number' => 'INV-'.($data['checkout_key'] ?? Str::uuid()),
                'order_type' => $data['order_type'], 'table_number' => $data['table_number'] ?? null,
                'subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax, 'grand_total' => $total,
                'payment_method' => $data['payment_method'], 'payment_status' => $paid ? 'paid' : 'unpaid',
                'paid_amount' => $paid ? $data['paid_amount'] : 0,
                'change_amount' => $paid ? $data['paid_amount'] - $total : 0,
                'order_status' => $paid ? 'completed' : 'pending', 'note' => $data['note'] ?? null,
            ]);
            app(CashLedger::class)->checkout($order);
            foreach ($lines as $line) {
                $orderItem = $order->items()->create($line);
                if ($line['reserved_stock']) {
                    $stockModel = $line['variant_id'] ? $products[$line['product_id']]->variants()->findOrFail($line['variant_id']) : $products[$line['product_id']];
                    $beforeStock = $stockModel->stock;
                    $stockModel->decrement('stock', $line['reserved_stock']);
                    StockMovement::create(['tenant_id' => $tenantId, 'product_id' => $line['product_id'], 'user_id' => auth()->id(), 'type' => 'sales', 'quantity' => $line['reserved_stock'], 'before_stock' => $beforeStock, 'after_stock' => $stockModel->stock, 'note' => 'Penjualan '.$order->invoice_number, 'reference_type' => 'order_item', 'reference_id' => $orderItem->id]);
                }
            }
            app(RecipeStock::class)->deduct($tenantId, $materialTotals, $order);
            if ($paid) {
                app(CustomerPoints::class)->award($order);
            } elseif (! $digital) {
                Customer::where('tenant_id', $tenantId)->whereKey($order->customer_id)->increment('total_debt', $total);
            }

            $qrUrl = $digital ? app(MidtransGateway::class)->charge($order->invoice_number, $total) : null;
            if ($digital && ! $qrUrl) {
                throw new \RuntimeException('Gateway tidak mengembalikan kode QR.');
            }

            $order->update(['qr_url' => $qrUrl]);

            return $this->receiptResponse($order);
        });
    }

    private function receiptResponse(Order $order)
    {
        return response()->json(['success' => true, 'order_id' => $order->id, 'invoice_number' => $order->invoice_number, 'payment_method' => $order->payment_method, 'payment_status' => $order->payment_status, 'order_status' => $order->order_status, 'grand_total' => (float) $order->grand_total, 'qr_url' => $order->qr_url]);
    }

    public function checkStatus(int $id)
    {
        $order = Order::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        if ($order->payment_status !== 'paid' && $order->payment_method === 'midtrans') {
            try {
                $status = app(MidtransGateway::class)->status($order->invoice_number);
                app(OrderPaymentService::class)->apply($order->id, $status);
                $order->refresh();
            } catch (\Throwable $e) {
                report($e);

                return response()->json(['success' => false, 'message' => 'Status pembayaran belum dapat diperiksa. Silakan coba lagi.'], 503);
            }
        }

        return response()->json(['success' => true, 'status' => $order->payment_status === 'paid' ? 'paid' : ($order->order_status === 'cancelled' ? 'cancelled' : 'unpaid')]);
    }
}
