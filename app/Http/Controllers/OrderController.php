<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function print(Request $request, $id)
    {
        $order = Order::with(['items', 'customer', 'user'])->findOrFail($id);

        $data = $request->validate(['paper' => 'nullable|in:58,80']);
        $paper = (int) ($data['paper'] ?? 58);

        return view('orders.print', compact('order', 'paper'));
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
        $returns = DB::table('order_returns')->where('tenant_id', $order->tenant_id)->where('order_id', $order->id)->orderBy('id')->get();

        return view('orders.show', compact('order', 'returns'));
        // return view('orders.print', compact('order'));
    }
}
