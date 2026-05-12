<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $categories = Category::where('tenant_id', $tenantId)->get();
        $customers = Customer::where('tenant_id', $tenantId)->get();

        // Ambil produk yang aktif saja
        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with('category')
            ->get();

        return view('pos.index', compact('categories', 'customers', 'products'));
    }
    // app/Http/Controllers/PosController.php

    // public function store(Request $request)
    // {
    //     // Validasi dasar
    //     $request->validate([
    //         'customer_id' => 'required',
    //         'items' => 'required|array|min:1',
    //         'payment_status' => 'required'
    //     ]);

    //     return DB::transaction(function () use ($request) {
    //         $tenantId = auth()->user()->tenant_id;

    //         // 1. Buat Invoice Number Otomatis (Contoh: INV-20260512-0001)
    //         $datePart = now()->format('Ymd');
    //         $lastOrder = Order::where('tenant_id', $tenantId)->latest()->first();
    //         $nextNumber = $lastOrder ? (int)substr($lastOrder->invoice_number, -4) + 1 : 1;
    //         $invoiceNumber = 'INV-' . $datePart . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    //         // 2. Simpan Header Order
    //         $order = Order::create([
    //             'tenant_id'      => $tenantId,
    //             'customer_id'    => $request->customer_id,
    //             'user_id'        => auth()->id(),
    //             'invoice_number' => $invoiceNumber,
    //             'subtotal'       => $request->subtotal,
    //             'discount'       => $request->discount ?? 0,
    //             'tax'            => $request->tax ?? 0,
    //             'grand_total'    => $request->grand_total,
    //             'payment_status' => $request->payment_status,
    //             'order_status'   => $request->order_status ?? 'completed',
    //             'note'           => $request->note,
    //         ]);

    //         // 3. Simpan Detail Item & Update Stok
    //         foreach ($request->items as $item) {
    //             $product = Product::find($item['id']);

    //             OrderItem::create([
    //                 'order_id'     => $order->id,
    //                 'product_id'   => $item['id'],
    //                 'product_name' => $item['name'],
    //                 'quantity'     => $item['quantity'],
    //                 'price'        => $item['price'],
    //                 'subtotal'     => $item['quantity'] * $item['price'],
    //             ]);

    //             // Hanya kurangi stok jika tipe produk adalah 'product' (bukan jasa)
    //             if ($product->type === 'product') {
    //                 $product->decrement('stock', $item['quantity']);
    //             }
    //         }

    //         // 4. Update Piutang Customer jika payment_status = 'unpaid' (Fitur Bon)
    //         if ($request->payment_status === 'unpaid') {
    //             $customer = Customer::find($request->customer_id);
    //             $customer->increment('total_debt', $request->grand_total);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Transaksi berhasil!',
    //             'order_id' => $order->id
    //         ]);
    //     });
    // }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $tenantId = auth()->user()->tenant_id;

            // Generate Invoice: INV-20260512-0001
            $invoice = 'INV-' . now()->format('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'tenant_id' => $tenantId,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'invoice_number' => $invoice,
                'subtotal' => $request->subtotal,
                'grand_total' => $request->grand_total,
                'payment_status' => $request->payment_status, // 'paid' atau 'unpaid'
                'order_status' => $request->payment_status === 'paid' ? 'completed' : 'pending',
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Potong stok otomatis
                $product = Product::find($item['id']);
                if ($product->type === 'product') {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // LOGIC UNGGULAN: Jika belum bayar, tambahkan ke total hutang customer
            if ($request->payment_status === 'unpaid') {
                $customer = Customer::find($request->customer_id);
                $customer->increment('total_debt', $request->grand_total);
            }

            return response()->json(['success' => true, 'message' => 'Transaksi Berhasil!']);
        });
    }
}
