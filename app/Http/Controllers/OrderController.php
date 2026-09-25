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
        $user = $request->user();
        $tenantId = $user->tenant_id;

        // Kasir bekerja dari shell POS, bukan back-office.
        if ($user->role === 'kasir') {
            return redirect()->route('pos.index', ['panel' => 'history']);
        }

        // Ambil order terbaru milik tenant yang sedang login
        $orders = Order::with(['customer', 'user'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function print(Request $request, $id)
    {
        $order = Order::with(['items', 'customer', 'user'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        // Kasir hanya boleh mencetak transaksi yang ia proses sendiri.
        abort_if($request->user()->role === 'kasir' && $order->user_id !== $request->user()->id, 403);

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

        // Kasir hanya dapat membuka detail transaksi miliknya sendiri.
        if (auth()->user()->role === 'kasir' && $order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi kasir lain.');
        }

        $order->load(['customer', 'items.product', 'user']);
        $returns = DB::table('order_returns')->where('tenant_id', $order->tenant_id)->where('order_id', $order->id)->orderBy('id')->get();

        if (auth()->user()->role === 'kasir') {
            return view('pos.order-show', compact('order'));
        }

        return view('orders.show', compact('order', 'returns'));
    }
}
