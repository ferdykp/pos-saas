<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class PosController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()?->tenant_id;
        $userId = auth()->id();

        // 1. BUAT KUNCI CACHE UNIK PER TENANT
        $cacheKeyProducts   = "tenant_{$tenantId}_products_pos";
        $cacheKeyCategories = "tenant_{$tenantId}_categories";
        $cacheKeySettings   = "tenant_{$tenantId}_settings";

        // 2. AMBIL/SIMPAN CACHE KATEGORI
        $categoriesRaw = Cache::remember($cacheKeyCategories, 86400, function () use ($tenantId) {
            return Category::where('tenant_id', $tenantId)->get()->toArray();
        });
        $categories = collect(json_decode(json_encode($categoriesRaw)));

        // 3. AMBIL/SIMPAN CACHE SETTING TOKO
        $settings = Cache::remember($cacheKeySettings, 86400, function () use ($tenantId) {
            return Setting::where('tenant_id', $tenantId)->pluck('value', 'key')->toArray();
        });

        // 4. AMBIL/SIMPAN CACHE PRODUK BESERTA DISKONNYA
        $productsRawCache = Cache::remember($cacheKeyProducts, 86400, function () use ($tenantId) {
            $productsRaw = Product::where('tenant_id', $tenantId)->with(['discounts', 'category'])->get();
            return $productsRaw->map(function ($product) {
                $activeDiscount = $product->discounts->filter(function ($discount) {
                    return $discount->isValidNow();
                })->first();

                $finalPrice = $product->sell_price;
                $discountAmount = 0;

                if ($activeDiscount) {
                    if ($activeDiscount->type === 'percentage') {
                        $discountAmount = $product->sell_price * ($activeDiscount->value / 100);
                    } else {
                        $discountAmount = $activeDiscount->value;
                    }
                    $finalPrice = max(0, $product->sell_price - $discountAmount);
                }

                $product->final_price = $finalPrice;
                $product->discount_applied = $discountAmount;
                $product->discount_name = $activeDiscount ? $activeDiscount->name : null;

                return $product;
            })->toArray();
        });

        $products = collect(json_decode(json_encode($productsRawCache)));

        $customers = Customer::where('tenant_id', $tenantId)->get();

        $activeShift = Shift::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($activeShift instanceof Shift) {
            /** @var \App\Models\Shift $activeShift */
            session(['active_shift_id' => $activeShift->id]);
            $hasShift = true;
        } else {
            session()->forget('active_shift_id');
            $hasShift = false;
        }

        return view('pos.index', compact('customers', 'categories', 'products', 'settings', 'hasShift'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant;

        // -------------------------------------------------------------------------
        // VALIDASI PROTEKSI PAKET LANGGANAN
        // -------------------------------------------------------------------------

        // 1. Cek Batas Transaksi Bulanan (Paket Starter: Max 100)
        if ($tenant && $tenant->isTransactionLimitReached()) {
            return response()->json([
                'success' => false,
                'message' => 'Limit 100 transaksi/bulan untuk Paket Starter telah tercapai. Silakan upgrade ke Paket Growth untuk transaksi tanpa batas!'
            ], 422);
        }

        // 2. Cek Akses Pembayaran Digital (QRIS Midtrans)
        if ($request->payment_method === 'midtrans' && Gate::denies('feature-qris')) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran QRIS Midtrans hanya tersedia pada Paket Growth & Scale.'
            ], 422);
        }

        // 3. Cek Akses CRM / Poin Member (Reset jika di Paket Starter)
        $customerId = $request->customer_id;
        if ($customerId && Gate::denies('feature-crm')) {
            $customerId = null; // Abaikan pencatatan customer/poin jika di paket Starter
        }

        return DB::transaction(function () use ($request, $tenant, $customerId) {
            $tenantId = $tenant?->id;

            // FORMAT INVOICE AMAN
            $invoice = 'INV-' . now()->format('YmdHis') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            $grandTotal = (int) $request->grand_total;
            $paidAmount = $request->paid_amount ?? 0;
            $changeAmount = 0;

            // Cek apakah metode yang dipilih adalah digital/payment gateway QRIS
            $isDigitalPayment = $request->payment_method === 'midtrans';

            if ($isDigitalPayment) {
                $paymentStatus = 'unpaid';
                $orderStatus   = 'pending';
                $paidAmount    = 0;
            } else if ($request->payment_method === 'cash' && $request->payment_status === 'paid') {
                $paymentStatus = 'paid';
                $orderStatus   = 'completed';
                $changeAmount  = $paidAmount - $grandTotal;
                if ($changeAmount < 0) {
                    return response()->json(['success' => false, 'message' => 'Uang tunai kurang!'], 422);
                }
            } else {
                $paymentStatus = 'unpaid';
                $orderStatus   = 'pending';
            }

            // Simpan ke Tabel Orders
            $order = Order::create([
                'tenant_id'      => $tenantId,
                'customer_id'    => $customerId,
                'user_id'        => auth()->id(),
                'invoice_number' => $invoice,
                'order_type'     => $request->order_type ?? 'dine_in',
                'table_number'   => $request->table_number,
                'subtotal'       => $request->subtotal,
                'discount'       => $request->discount ?? 0,
                'tax'            => $request->tax ?? 0,
                'grand_total'    => $grandTotal,
                'payment_method' => $request->payment_method,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_status' => $paymentStatus,
                'order_status'   => $orderStatus,
                'note'           => $request->note,
            ]);

            // Simpan Detail Item & Potong Stok
            foreach ($request->items as $item) {
                $product = Product::where('tenant_id', $tenantId)->lockForUpdate()->find($item['id']);

                if ($product && $product->type === 'product' && $product->manage_stock == 1) {
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok untuk produk '{$product->product_name}' tidak mencukupi!");
                    }
                    $product->decrement('stock', $item['quantity']);
                }

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                ]);
            }

            // Hutang Customer jika BON (Hanya jika fitur CRM aktif)
            if ($request->payment_status === 'unpaid' && $customerId && !$isDigitalPayment && Gate::allows('feature-crm')) {
                $customer = Customer::where('tenant_id', $tenantId)->lockForUpdate()->find($customerId);
                if ($customer) {
                    $customer->increment('total_debt', $grandTotal);
                }
            }

            // PROSES INTEGRASI MIDTRANS CORE API (QRIS ONLY)
            $qrUrl = null;

            if ($isDigitalPayment) {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized', true);
                \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds', true);
                $params = [
                    'payment_type' => 'qris',
                    'transaction_details' => [
                        'order_id' => $invoice,
                        'gross_amount' => (int) $grandTotal,
                    ],
                    'qris' => [
                        'acquirer' => 'gopay'
                    ]
                ];

                try {
                    /** @var \stdClass $response */
                    $response = \Midtrans\CoreApi::charge($params);
                    $qrUrl = isset($response->actions[0]->url) ? $response->actions[0]->url : null;
                } catch (\Exception $e) {
                    throw new \Exception("Gagal menghasilkan kode QRIS: " . $e->getMessage());
                }
            }

            // Logika Poin Customer (Hanya jika fitur CRM aktif)
            if ($paymentStatus === 'paid' && $customerId && Gate::allows('feature-crm')) {
                $customer = Customer::where('tenant_id', $tenantId)->lockForUpdate()->find($customerId);
                if ($customer) {
                    $settings = Setting::where('tenant_id', $tenantId)->pluck('value', 'key');
                    $pointMode    = $settings['point_mode'] ?? 'disabled';
                    $ruleValue    = (float) ($settings['point_rule_value'] ?? 0);
                    $isMemberOnly = filter_var($settings['point_member_only'] ?? false, FILTER_VALIDATE_BOOLEAN);

                    if ($pointMode !== 'disabled' && ($isMemberOnly ? $customer->is_member : true) && $ruleValue > 0) {
                        $pointsEarned = 0;
                        switch ($pointMode) {
                            case 'per_investment':
                                $pointsEarned = floor($grandTotal / $ruleValue);
                                break;
                            case 'flat':
                                $pointsEarned = $ruleValue;
                                break;
                            case 'percentage':
                                $pointsEarned = floor($grandTotal * ($ruleValue / 100));
                                break;
                        }
                        if ($pointsEarned > 0) {
                            $customer->increment('points', $pointsEarned);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil Dibuat!',
                'order_id' => $order->id,
                'invoice_number' => $invoice,
                'payment_method' => $request->payment_method,
                'qr_url' => $qrUrl
            ]);
        });
    }

    public function checkStatus(int $id)
    {
        $tenantId = auth()->user()?->tenant_id;

        $order = Order::where('tenant_id', $tenantId)->lockForUpdate()->findOrFail($id);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'status' => 'paid'
            ]);
        }

        if ($order->payment_method === 'midtrans') {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
            try {
                /** @var object|array $midtransStatus */
                $midtransStatus = \Midtrans\Transaction::status($order->invoice_number);
                $transactionStatus = $midtransStatus->transaction_status;

                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {

                    $order->update([
                        'payment_status'    => 'paid',
                        'order_status'      => 'completed',
                        'withdrawal_status' => 'pending'
                    ]);

                    $platformFeePercent = 1.5;
                    $totalFee = ($order->grand_total * $platformFeePercent) / 100;
                    $netIncomeForTenant = $order->grand_total - $totalFee;

                    DB::table('tenant_wallets')->updateOrInsert(
                        ['tenant_id' => $tenantId],
                        [
                            'balance'    => DB::raw("balance + $netIncomeForTenant"),
                            'updated_at' => now()
                        ]
                    );

                    if ($order->customer_id && Gate::allows('feature-crm')) {
                        $customer = Customer::where('tenant_id', $tenantId)->lockForUpdate()->find($order->customer_id);
                        if ($customer) {
                            $settings = Setting::where('tenant_id', $tenantId)->pluck('value', 'key');
                            $pointMode    = $settings['point_mode'] ?? 'disabled';
                            $ruleValue    = (float) ($settings['point_rule_value'] ?? 0);
                            $isMemberOnly = filter_var($settings['point_member_only'] ?? false, FILTER_VALIDATE_BOOLEAN);

                            if ($pointMode !== 'disabled' && ($isMemberOnly ? $customer->is_member : true) && $ruleValue > 0) {
                                $pointsEarned = 0;
                                switch ($pointMode) {
                                    case 'per_investment':
                                        $pointsEarned = floor($order->grand_total / $ruleValue);
                                        break;
                                    case 'flat':
                                        $pointsEarned = $ruleValue;
                                        break;
                                    case 'percentage':
                                        $pointsEarned = floor($order->grand_total * ($ruleValue / 100));
                                        break;
                                }
                                if ($pointsEarned > 0) {
                                    $customer->increment('points', $pointsEarned);
                                }
                            }
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'status' => 'paid'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Gagal cek status Midtrans untuk ID {$id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'status' => 'unpaid'
        ]);
    }
}
