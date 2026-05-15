<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    // public function index()
    // {
    //     $tenantId = auth()->user()->tenant_id;

    //     $categories = Category::where('tenant_id', $tenantId)->get();
    //     $customers = Customer::where('tenant_id', $tenantId)->get();

    //     // Ambil produk yang aktif saja
    //     $products = Product::where('tenant_id', $tenantId)
    //         ->where('is_active', true)
    //         ->with('category')
    //         ->get();

    //     return view('pos.index', compact('categories', 'customers', 'products'));
    // }

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $customers = Customer::where('tenant_id', $tenantId)->get();
        $categories = Category::where('tenant_id', $tenantId)->get();

        // Ambil produk beserta relasi diskon aktifnya
        $productsRaw = Product::where('tenant_id', $tenantId)->with('discounts')->get();

        $products = $productsRaw->map(function ($product) {
            $activeDiscount = $product->discounts->filter(function ($discount) {
                return $discount->isValidNow(); // Validasi filter runtime jam/tanggal
            })->first();

            // Tentukan harga coret & nilai potongan jika diskon ada
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

            // Properti dinamis yang disuntikkan ke objek product
            $product->final_price = $finalPrice;
            $product->discount_applied = $discountAmount;
            $product->discount_name = $activeDiscount ? $activeDiscount->name : null;

            return $product;
        });

        $settings = Setting::where('tenant_id', auth()->user()->tenant_id)->pluck('value', 'key');


        return view('pos.index', compact('customers', 'categories', 'products', 'settings'));
    }


    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $tenantId = auth()->user()->tenant_id;

            // Generate Invoice otomatis: INV-YYYYMMDD-0001
            $invoice = 'INV-' . now()->format('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

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
                // Jika transfer/QRIS atau Piutang, otomatis uang diterima disamakan/nol
                $paidAmount = $request->payment_status === 'paid' ? $grandTotal : 0;
            }

            // Simpan ke Table Orders
            $order = Order::create([
                'tenant_id'      => $tenantId,
                'customer_id'    => $request->customer_id,
                'user_id'        => auth()->id(),
                'invoice_number' => $invoice,
                'table_number'   => $request->table_number,
                'subtotal'       => $request->subtotal,          // Diambil dari JS
                'discount'       => $request->discount ?? 0,
                'tax'            => $request->tax ?? 0,               // MENYIMPAN NILAI PPN 11%
                'grand_total'    => $grandTotal,                 // Diambil dari JS (Subtotal + PPN)
                'payment_method' => $request->payment_method,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_status' => $request->payment_status,
                'order_status'   => $request->payment_status === 'paid' ? 'completed' : 'pending',
                'note'           => $request->note,
            ]);
            // Simpan Detail Item & Potong Stok
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                ]);

                $product = Product::find($item['id']);
                if ($product && $product->type === 'product') {
                    // Catatan: Jika Anda menerapkan logika "Lacak Stok" di produk sebelumnya, 
                    // pastikan produk non-lacak stok diisi nilai besar (999999) agar aman dikurangi.
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Tambah ke total piutang/hutang customer jika memilih BON/PIUTANG
            if ($request->payment_status === 'unpaid' && $request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer->increment('total_debt', $grandTotal);
                }
            }

            // return response()->json(['success' => true, 'message' => 'Transaksi Berhasil disimpan!']);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil!',
                'order_id' => $order->id // <-- Sangat penting
            ]);
        });
    }
}
