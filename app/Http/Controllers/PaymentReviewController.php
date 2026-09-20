<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransGateway;
use App\Services\OrderPaymentService;

class PaymentReviewController extends Controller
{
    public function index()
    {
        $orders = Order::where('payment_method', 'midtrans')->where('payment_status', 'unpaid')
            ->where(fn ($query) => $query->where('payment_attention', true)->orWhere('order_status', 'pending'))
            ->oldest()->paginate(25);

        return view('payments.review', compact('orders'));
    }

    public function check(Order $order, MidtransGateway $gateway, OrderPaymentService $payments)
    {
        abort_unless($order->tenant_id === auth()->user()->tenant_id && $order->payment_method === 'midtrans', 404);
        try {
            $payments->apply($order->id, $gateway->status($order->invoice_number));
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors(['payment' => 'Status belum dapat dikonfirmasi. Jangan membuat tagihan pengganti sebelum memeriksa transaksi di penyedia pembayaran.']);
        }

        return back()->with('success', 'Status nota diperiksa melalui penyedia pembayaran.');
    }
}
