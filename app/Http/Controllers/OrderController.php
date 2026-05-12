<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        // Ambil order terbaru milik tenant yang sedang login
        $orders = Order::with(['customer', 'user'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail transaksi dan struk.
     */
    public function show(Order $order)
    {
        // Proteksi Multi-Tenant
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        $order->load(['customer', 'items.product', 'user']);

        return view('orders.show', compact('order'));
    }
}
