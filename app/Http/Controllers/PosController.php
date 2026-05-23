<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PosController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $userId = auth()->id();

        // 1. BUAT KUNCI CACHE UNIK PER TENANT
        $cacheKeyProducts   = "tenant_{$tenantId}_products_pos";
        $cacheKeyCategories = "tenant_{$tenantId}_categories";
        $cacheKeySettings   = "tenant_{$tenantId}_settings";

        // 2. AMBIL/SIMPAN CACHE KATEGORI (Aman dari __PHP_Incomplete_Class)
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
            // $productsRaw = Product::where('tenant_id', $tenantId)->with('discounts')->get();
            // Tambahkan 'category' di dalam fungsi with()
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
                // Diubah ke array murni agar tersimpan rapi sebagai JSON string di dalam Redis
            })->toArray();
        });

        // Konversi ke koleksi objek standar agar Blade bisa membaca properti seperti $product->product_name
        $products = collect(json_decode(json_encode($productsRawCache)));

        // Data dinamis tidak boleh masuk Cache demi akurasi real-time harian
        $customers = Customer::where('tenant_id', $tenantId)->get();

        $activeShift = Shift::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            session(['active_shift_id' => $activeShift->id]);
        } else {
            session()->forget('active_shift_id');
        }

        $hasShift = $activeShift ? true : false;

        return view('pos.index', compact('customers', 'categories', 'products', 'settings', 'hasShift'));
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $tenantId = auth()->user()->tenant_id;

            // Hitung order berdasarkan tenant_id agar nomor invoice tidak melompat karena toko lain
            $orderCount = Order::where('tenant_id', $tenantId)->count();
            $invoice = 'INV-' . now()->format('Ymd') . '-' . str_pad($orderCount + 1, 4, '0', STR_PAD_LEFT);

            // Hitung Kembalian di sisi server untuk validasi keamanan
            $grandTotal = $request->grand_total;
            $paidAmount = $request->paid_amount ?? 0;
            $changeAmount = 0;

            if ($request->payment_method === 'cash' && $request->payment_status === 'paid') {
                $changeAmount = $paidAmount - $grandTotal;
                if ($changeAmount < 0) {
                    return response()->json(['success' => false, 'message' => 'Uang yang diterima kurang!'], 422);
                }
            } else {
                $paidAmount = $request->payment_status === 'paid' ? $grandTotal : 0;
            }

            // Simpan ke Table Orders
            $order = Order::create([
                'tenant_id'      => $tenantId,
                'customer_id'    => $request->customer_id,
                'user_id'        => auth()->id(),
                'invoice_number' => $invoice,
                'table_number'   => $request->table_number,
                'subtotal'       => $request->subtotal,
                'discount'       => $request->discount ?? 0,
                'tax'            => $request->tax ?? 0,
                'grand_total'    => $grandTotal,
                'payment_method' => $request->payment_method,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_status' => $request->payment_status,
                'order_status'   => $request->payment_status === 'paid' ? 'completed' : 'pending',
                'note'           => $request->note,
            ]);

            // Simpan Detail Item & Potong Stok dengan Pessimistic Locking
            foreach ($request->items as $item) {

                // KUNCI BARIS DATA: Ambil data langsung dari DB (Bukan dari Cache) khusus untuk manipulasi stok krusial
                $product = Product::where('tenant_id', $tenantId)
                    ->lockForUpdate()
                    ->find($item['id']);

                // VALIDASI STOK: Hanya diproses jika produk dilacak (manage_stock == 1)
                if ($product && $product->type === 'product' && $product->manage_stock == 1) {

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Transaksi dibatalkan. Stok untuk produk '{$product->product_name}' tidak mencukupi!");
                    }

                    // Potong stok produk asli di database
                    $product->decrement('stock', $item['quantity']);
                }

                // Catat detail item order
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                ]);
            }

            // Tambah ke total piutang/hutang customer jika memilih BON/PIUTANG
            if ($request->payment_status === 'unpaid' && $request->customer_id) {
                $customer = Customer::where('tenant_id', $tenantId)->lockForUpdate()->find($request->customer_id);
                if ($customer) {
                    $customer->increment('total_debt', $grandTotal);
                }
            }

            // Logika Pemberian Poin Dinamis Berdasarkan Setting User
            if ($request->payment_status === 'paid' && $request->customer_id) {
                $customer = Customer::where('tenant_id', $tenantId)->lockForUpdate()->find($request->customer_id);

                if ($customer) {
                    $settings = Setting::where('tenant_id', $tenantId)->pluck('value', 'key');

                    $pointMode    = $settings['point_mode'] ?? 'disabled';
                    $ruleValue    = (float) ($settings['point_rule_value'] ?? 0);
                    $isMemberOnly = filter_var($settings['point_member_only'] ?? false, FILTER_VALIDATE_BOOLEAN);

                    $canGetPoint = true;
                    if ($isMemberOnly && !$customer->is_member) {
                        $canGetPoint = false;
                    }

                    if ($pointMode !== 'disabled' && $canGetPoint && $ruleValue > 0) {
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
                'message' => 'Transaksi Berhasil!',
                'order_id' => $order->id
            ]);
        });
    }
}
