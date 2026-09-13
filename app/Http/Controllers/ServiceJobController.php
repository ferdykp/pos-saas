<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceJobController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['status' => 'nullable|in:queued,working,ready,completed']);
        $status = $data['status'] ?? 'queued';
        $orders = Order::with(['items', 'customer', 'assignedUser'])->where('service_status', $status)
            ->where('order_status', '!=', 'cancelled')->oldest()->paginate(20)->withQueryString();
        $staff = User::where('tenant_id', $request->user()->tenant_id)->orderBy('name')->get(['id', 'name']);

        return view('services.index', compact('orders', 'staff', 'status'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:queued,working,ready,completed',
            'service_due_at' => 'nullable|date_format:Y-m-d\\TH:i',
            'assigned_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('tenant_id', $request->user()->tenant_id)],
        ]);
        DB::transaction(function () use ($order, $data) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            abort_unless($order->service_status && $order->order_status !== 'cancelled', 422);
            $next = ['queued' => 'working', 'working' => 'ready', 'ready' => 'completed'];
            abort_unless($order->service_status === $data['status'] || ($next[$order->service_status] ?? null) === $data['status'], 409, 'Status pengerjaan sudah berubah atau langkah dilewati.');
            $order->update(['service_status' => $data['status'], 'assigned_user_id' => $data['assigned_user_id'] ?? null, 'service_due_at' => $data['service_due_at'] ?? null]);
        });

        return back()->with('success', 'Pengerjaan diperbarui. Status pembayaran tidak berubah.');
    }
}
