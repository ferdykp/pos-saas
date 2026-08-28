<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $plans = Plan::where('is_active', true)->where('is_public', true)->get();
        $currentSubscription = $tenant ? $tenant->subscriptions()->latest()->first() : null;

        return view('billing.index', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = auth()->user();
        $tenant = $user->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        if ($plan->price == 0) {
            Subscription::create([
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

        $subscription = Subscription::create([
            'tenant_id'  => $tenant->id,
            'plan_id'    => $plan->id,
            'status'     => 'pending',
            'start_date' => now(),
            'end_date'   => now()->addDays($plan->duration_days ?? 30),
        ]);

        $invoiceNumber = 'INV-SAAS-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $invoice = Invoice::create([
            'tenant_id'       => $tenant->id,
            'subscription_id' => $subscription->id,
            'invoice_number'  => $invoiceNumber,
            'amount'          => $plan->price,
            'status'          => 'pending',
            'due_date'        => now()->addDays(1),
        ]);

        $this->createSnapTransaction($invoice, $user);

        return redirect()->route('billing.invoice', $invoice->id);
    }

    public function showInvoice(Invoice $invoice)
    {
        if ($invoice->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Snap token belum ada (percobaan sebelumnya gagal) -> coba buat ulang
        // Aman untuk di-retry karena order_id = invoice_number, dibuat sekali per invoice.
        if ($invoice->status === 'pending' && empty($invoice->snap_token)) {
            $this->createSnapTransaction($invoice, auth()->user());
            $invoice->refresh();
        }

        return view('billing.invoice', compact('invoice'));
    }

    public function checkStatus(Invoice $invoice)
    {
        if ($invoice->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return response()->json(['success' => true, 'status' => 'paid']);
        }

        // Ini cuma fallback UX (biar user gak nunggu delay webhook di UI).
        // Sumber kebenaran utama tetap method notification() di bawah.
        $this->configureMidtrans();

        try {
            $midtransStatus = \Midtrans\Transaction::status($invoice->invoice_number);
            $this->applyMidtransStatus($invoice, $midtransStatus);
        } catch (\Exception $e) {
            Log::error("Billing Check Status Error [{$invoice->invoice_number}]: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'status' => $invoice->fresh()->status]);
    }

    /**
     * Dipanggil server Midtrans (bukan browser user) setiap ada perubahan status transaksi.
     * Route ini publik & dikecualikan dari CSRF — lihat bootstrap/app.php.
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        $orderId      = $payload['order_id'] ?? null;
        $statusCode   = $payload['status_code'] ?? null;
        $grossAmount  = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            Log::warning('Midtrans notification: payload tidak lengkap', $payload);
            return response()->json(['message' => 'invalid payload'], 400);
        }

        $serverKey = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (!hash_equals($expectedSignature, $signatureKey)) {
            Log::warning("Midtrans notification: signature tidak valid untuk order_id {$orderId}");
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $invoice = Invoice::where('invoice_number', $orderId)->first();

        if (!$invoice) {
            Log::warning("Midtrans notification: invoice tidak ditemukan untuk order_id {$orderId}");
            return response()->json(['message' => 'invoice not found'], 404);
        }

        // Jangan percaya body notifikasi mentah-mentah — tarik ulang status resmi dari Midtrans.
        $this->configureMidtrans();

        try {
            $midtransStatus = \Midtrans\Transaction::status($orderId);
            $this->applyMidtransStatus($invoice, $midtransStatus);
        } catch (\Exception $e) {
            Log::error("Midtrans notification: gagal fetch status [{$orderId}]: " . $e->getMessage());
            return response()->json(['message' => 'error checking status'], 500);
        }

        return response()->json(['message' => 'ok']);
    }

    private function createSnapTransaction(Invoice $invoice, $user): void
    {
        $this->configureMidtrans();

        $params = [
            'transaction_details' => [
                'order_id'     => $invoice->invoice_number,
                'gross_amount' => (int) round($invoice->amount),
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
            'enabled_payments' => ['qris', 'bank_transfer', 'gopay', 'shopeepay', 'other_qris'],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            \Log::info("Snap token fetched OK [{$invoice->invoice_number}]: " . $snapToken);

            $result = $invoice->update(['snap_token' => $snapToken]);

            \Log::info("Invoice update result: " . var_export($result, true) . " | saved value: " . $invoice->fresh()->snap_token);
        } catch (\Exception $e) {
            Log::error("Midtrans Snap Error [{$invoice->invoice_number}]: " . $e->getMessage());
        }
    }

    private function applyMidtransStatus(Invoice $invoice, $midtransStatus): void
    {
        $status = $midtransStatus->transaction_status ?? '';
        $paymentType = $midtransStatus->payment_type ?? null;

        if (in_array($status, ['settlement', 'capture']) && $invoice->status !== 'paid') {
            $invoice->update([
                'status'         => 'paid',
                'paid_at'        => now(),
                'payment_method' => $paymentType,
            ]);

            if ($invoice->subscription) {
                $invoice->subscription->update([
                    'status'     => 'active',
                    'start_date' => now(),
                    'end_date'   => now()->addDays($invoice->subscription->plan->duration_days ?? 30),
                ]);

                if ($invoice->subscription->tenant) {
                    $invoice->subscription->tenant->update(['status' => 'active']);
                }
            }
        } elseif (in_array($status, ['expire', 'cancel', 'deny']) && $invoice->status === 'pending') {
            $invoice->update(['status' => $status === 'expire' ? 'expired' : 'failed']);
        }
    }

    private function configureMidtrans(): void
    {
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }
}
