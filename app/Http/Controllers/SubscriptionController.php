<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Tampilkan halaman daftar paket & status langganan saat ini
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $plans = Plan::where('is_active', true)->where('is_public', true)->get();
        $currentSubscription = $tenant ? $tenant->subscriptions()->latest()->first() : null;

        return view('billing.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Proses checkout pembuatan invoice & pembuatan token Midtrans Snap
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = auth()->user();
        $tenant = $user->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        // BILA PAKET GRATIS (Rp 0 / Starter)
        if ($plan->price == 0) {
            $subscription = Subscription::create([
                'tenant_id'  => $tenant->id,
                'plan_id'    => $plan->id,
                'status'     => 'active',
                'start_date' => now(),
                'end_date'   => now()->addDays($plan->duration_days ?? 30),
            ]);

            $tenant->update(['status' => 'active']);

            return redirect()->route('dashboard')
                ->with('success', 'Paket Starter berhasil diaktifkan!');
        }

        // BILA PAKET BERBAYAR (Growth / Scale via Midtrans Snap)
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id'   => $plan->id,
            'status'    => 'pending',
        ]);

        $invoiceNumber = 'INV-SAAS-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5));

        $invoice = Invoice::create([
            'tenant_id'       => $tenant->id,
            'subscription_id' => $subscription->id,
            'invoice_number'  => $invoiceNumber,
            'amount'          => $plan->price,
            'status'          => 'pending',
        ]);

        // Setup Midtrans Snap Parameter...
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $invoice->invoice_number,
                'gross_amount' => (int) $invoice->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
            'item_details' => [
                [
                    'id'       => 'PLAN-' . $plan->id,
                    'price'    => (int) $plan->price,
                    'quantity' => 1,
                    'name'     => 'Langganan GrowPOS - ' . $plan->name,
                ]
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $invoice->update(['snap_token' => $snapToken]);

        return redirect()->route('billing.invoice', $invoice->id);
    }
    /**
     * Tampilkan Halaman Pembayaran Invoice dengan Pop-up Midtrans Snap
     */
    public function showInvoice(Invoice $invoice)
    {
        // Pastikan invoice milik tenant yang sedang login
        if ($invoice->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        return view('billing.invoice', compact('invoice'));
    }
}
