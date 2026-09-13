<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Subscription;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentCallbackController extends Controller
{
    public function handleNotification(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|string|max:255',
                'transaction_status' => 'required|string|max:40',
                'status_code' => 'required|string|max:3',
                'gross_amount' => 'required|numeric|min:0',
                'signature_key' => 'required|string',
                'payment_type' => 'nullable|string|max:50',
                'fraud_status' => 'nullable|string|max:30',
            ]);
            // 1. Ambil data JSON mentah dari Midtrans
            $orderId = $request->input('order_id');
            $transactionStatus = $request->input('transaction_status');
            $statusCode = $request->input('status_code');
            $grossAmount = $request->input('gross_amount');
            $inputSignature = $request->input('signature_key');
            $paymentType = $request->input('payment_type');

            Log::info("Webhook Masuk - Order ID: {$orderId} | Status: {$transactionStatus} | Code: {$statusCode}");

            // 3. KEAMANAN KRITIS: Verifikasi Signature Key SHA512 dari Midtrans
            $serverKey = config('services.midtrans.server_key');
            $signatureString = $orderId.$statusCode.$grossAmount.$serverKey;
            $calculatedSignature = hash('sha512', $signatureString);

            if (! $serverKey || ! is_string($inputSignature) || ! hash_equals($calculatedSignature, $inputSignature)) {
                Log::warning('Midtrans Webhook: Signature Key TIDAK VALID! Potensi manipulasi data.', [
                    'order_id' => $orderId,
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature key',
                ], 403);
            }

            // 4. BUNGKUS TRANSAKSI DATABASE (Cegah Race Condition)
            $response = DB::transaction(function () use ($orderId, $transactionStatus, $grossAmount, $paymentType, $request) {

                // -------------------------------------------------------------------------
                // A. SKENARIO 1: PEMBAYARAN ORDER TRANSAKSI POS KASIR
                // -------------------------------------------------------------------------
                $order = Order::withoutGlobalScopes()->where('invoice_number', $orderId)->lockForUpdate()->first();

                if ($order) {
                    app(OrderPaymentService::class)->apply($order->id, $request->all());

                    return response()->json(['status' => 'success', 'message' => 'Callback POS Order diproses'], 200);
                }

                // -------------------------------------------------------------------------
                // B. SKENARIO 2: PEMBAYARAN INVOICE BILLING SAAS (LANGGANAN TENANT)
                // -------------------------------------------------------------------------
                $invoice = Invoice::withoutGlobalScopes()->where('invoice_number', $orderId)->lockForUpdate()->first();

                if ($invoice) {
                    if (! is_numeric($grossAmount) || round((float) $grossAmount, 2) !== round((float) $invoice->amount, 2)) {
                        throw ValidationException::withMessages(['payment' => 'Nominal invoice tidak sesuai.']);
                    }
                    if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $request->input('fraud_status') === 'accept')) {
                        if ($invoice->status !== 'paid') {
                            $invoice->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                                'payment_method' => $paymentType,
                            ]);

                            // Aktifkan / Perpanjang Subscription Tenant
                            $subscription = Subscription::withoutGlobalScopes()->where('id', $invoice->subscription_id)->first();
                            if ($subscription) {
                                $plan = $subscription->plan;
                                $startDate = now();
                                $endDate = now()->addDays($plan->duration_days ?? 30);

                                $subscription->update([
                                    'status' => 'active',
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                ]);

                                // Aktifkan tenant
                                if ($subscription->tenant) {
                                    $subscription->tenant->update(['status' => 'active']);
                                }
                            }

                            Log::info("Invoice SaaS Billing {$orderId} BERHASIL LUNAS. Langganan aktif hingga ".($endDate ?? ''));
                        }
                    } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                        if ($invoice->status !== 'paid') {
                            $invoice->update(['status' => 'failed']);
                            Log::info("Invoice SaaS Billing {$orderId} dibatalkan/expired.");
                        }
                    }

                    return response()->json(['status' => 'success', 'message' => 'Callback SaaS Billing diproses'], 200);
                }

                // Jika Invoice tidak ditemukan baik di tabel Order maupun Invoice
                Log::warning("Invoice {$orderId} tidak ditemukan di tabel Order maupun Invoice Billing.");

                return response()->json([
                    'status' => 'retry',
                    'message' => 'Order/Invoice belum tersedia. Kirim ulang notifikasi.',
                ], 503);
            }, 3);

            return $response;
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Data pembayaran tidak sesuai.'], 422);
        } catch (\Throwable $e) {
            Log::error('Crash internal pada Webhook Midtrans: '.$e->getMessage());

            return response()->json([
                'status' => 'error_caught',
                'message' => 'Pemrosesan pembayaran gagal. Silakan kirim ulang notifikasi.',
            ], 503); // Failure must remain retryable; do not acknowledge an uncommitted payment.
        }
    }
}
