<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->where('payment_status', 'paid')->where('order_status', 'completed')->whereIn('kitchen_status', ['queued', 'preparing', 'ready'])->oldest()->get();

        return view('kitchen.index', compact('orders'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:preparing,ready,served']);
        DB::transaction(function () use ($data, $order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            abort_unless($order->payment_status === 'paid' && $order->order_status === 'completed', 422);
            $next = ['queued' => 'preparing', 'preparing' => 'ready', 'ready' => 'served'];
            if ($order->kitchen_status === $data['status']) {
                return;
            }
            abort_unless(($next[$order->kitchen_status] ?? null) === $data['status'], 409, 'Status pesanan sudah berubah. Muat ulang antrean.');
            $order->update(['kitchen_status' => $data['status']]);
        });

        return back()->with('success', 'Status dapur diperbarui.');
    }
}
