<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handleNotification(Request $request)
    {
        try {
            // 1. Ambil data JSON mentah dari Midtrans
            $orderId           = $request->input('order_id');
            $transactionStatus = $request->input('transaction_status');
            $statusCode        = $request->input('status_code');
            $grossAmount       = $request->input('gross_amount');
            $inputSignature    = $request->input('signature_key');
            $paymentType       = $request->input('payment_type');

            Log::info("Webhook Masuk - Order ID: {$orderId} | Status: {$transactionStatus} | Code: {$statusCode}");

            // 2. PROTEKSI UTAMA: Loloskan data pengetesan sandbox Midtrans
            if (!$orderId || str_contains($orderId, 'payment_notif_test')) {
                Log::info("Data uji coba Midtrans berhasil dilewati dengan aman.");
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sandbox test handled successfully.'
                ], 200);
            }

            // 3. KEAMANAN KRITIS: Verifikasi Signature Key SHA512 dari Midtrans
            $serverKey = config('services.midtrans.server_key');
            $signatureString = $orderId . $statusCode . $grossAmount . $serverKey;
            $calculatedSignature = hash('sha512', $signatureString);

            if ($inputSignature !== $calculatedSignature) {
                Log::warning("Midtrans Webhook: Signature Key TIDAK VALID! Potensi manipulasi data.", [
                    'order_id' => $orderId,
                    'ip'       => $request->ip(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid signature key'
                ], 403);
            }

            // 4. BUNGKUS TRANSAKSI DATABASE (Cegah Race Condition)
            $response = DB::transaction(function () use ($orderId, $transactionStatus, $grossAmount, $paymentType) {

                // -------------------------------------------------------------------------
                // A. SKENARIO 1: PEMBAYARAN ORDER TRANSAKSI POS KASIR
                // -------------------------------------------------------------------------
                $order = Order::where('invoice_number', $orderId)->lockForUpdate()->first();

                if ($order) {
                    if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                        if ($order->payment_status !== 'paid') {

                            // Update Status Order Jadi Lunas
                            $order->update([
                                'payment_status'    => 'paid',
                                'order_status'      => 'completed',
                                'paid_amount'       => $order->grand_total,
                                'withdrawal_status' => 'pending' // Siap ditarik dana oleh tenant
                            ]);

                            // Hitung komisi platform (diambil dari config/platform.php atau fallback 1.5%)
                            $platformFeePercent = config('platform.commission_rate', 0.015) * 100;
                            $totalFee           = ($order->grand_total * $platformFeePercent) / 100;
                            $netIncomeForTenant = $order->grand_total - $totalFee;

                            // Tambahkan saldo bersih ke wallet tenant secara atomic
                            DB::table('tenant_wallets')->updateOrInsert(
                                ['tenant_id' => $order->tenant_id],
                                [
                                    'balance'    => DB::raw("balance + $netIncomeForTenant"),
                                    'updated_at' => now()
                                ]
                            );

                            Log::info("SaaS Dompet Tenant ID {$order->tenant_id} ditambah Rp {$netIncomeForTenant} (Komisi Platform: Rp {$totalFee})");

                            // Poin Customer otomatis
                            if ($order->customer_id) {
                                $customer = Customer::where('tenant_id', $order->tenant_id)->lockForUpdate()->find($order->customer_id);
                                if ($customer) {
                                    $settings     = Setting::where('tenant_id', $order->tenant_id)->pluck('value', 'key');
                                    $pointMode    = $settings['point_mode'] ?? 'disabled';
                                    $ruleValue    = (float) ($settings['point_rule_value'] ?? 0);
                                    $isMemberOnly = filter_var($settings['point_member_only'] ?? false, FILTER_VALIDATE_BOOLEAN);

                                    if ($pointMode !== 'disabled' && ($isMemberOnly ? $customer->is_member : true) && $ruleValue > 0) {
                                        $pointsEarned = 0;
                                        switch ($pointMode) {
                                            case 'per_investment':
                                                $pointsEarned = floor($order->grand_total / $ruleValue);
                                                break;
                                            case 'flat':
                                                $pointsEarned = $ruleValue;
                                                break;
                                            case 'percentage':
                                                $pointsEarned = floor($order->grand_total * ($ruleValue / 100));
                                                break;
                                        }
                                        if ($pointsEarned > 0) {
                                            $customer->increment('points', $pointsEarned);
                                        }
                                    }
                                }
                            }

                            Log::info("Invoice POS {$orderId} BERHASIL DIUPDATE MENJADI PAID VIA WEBHOOK.");
                        }
                    } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                        if ($order->payment_status !== 'paid') {
                            $order->update([
                                'payment_status' => 'unpaid',
                                'order_status'   => 'cancelled'
                            ]);
                            Log::info("Invoice POS {$orderId} dibatalkan/expired.");
                        }
                    }

                    return response()->json(['status' => 'success', 'message' => 'Callback POS Order diproses'], 200);
                }

                // -------------------------------------------------------------------------
                // B. SKENARIO 2: PEMBAYARAN INVOICE BILLING SAAS (LANGGANAN TENANT)
                // -------------------------------------------------------------------------
                $invoice = Invoice::where('invoice_number', $orderId)->lockForUpdate()->first();

                if ($invoice) {
                    if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                        if ($invoice->status !== 'paid') {
                            $invoice->update([
                                'status'         => 'paid',
                                'paid_at'        => now(),
                                'payment_method' => $paymentType,
                            ]);

                            // Aktifkan / Perpanjang Subscription Tenant
                            $subscription = Subscription::where('id', $invoice->subscription_id)->first();
                            if ($subscription) {
                                $plan      = $subscription->plan;
                                $startDate = now();
                                $endDate   = now()->addDays($plan->duration_days ?? 30);

                                $subscription->update([
                                    'status'     => 'active',
                                    'start_date' => $startDate,
                                    'end_date'   => $endDate,
                                ]);

                                // Aktifkan tenant
                                if ($subscription->tenant) {
                                    $subscription->tenant->update(['status' => 'active']);
                                }
                            }

                            Log::info("Invoice SaaS Billing {$orderId} BERHASIL LUNAS. Langganan aktif hingga " . ($endDate ?? ''));
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
                    'status'  => 'ignored',
                    'message' => 'Order/Invoice tidak ditemukan di lokal, namun callback sukses diterima.'
                ], 200);
            });

            return $response;
        } catch (\Exception $e) {
            Log::error('Crash internal pada Webhook Midtrans: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error_caught',
                'message' => $e->getMessage()
            ], 200); // Tetap 200 OK agar Midtrans tidak melakukan retry berulang
        }
    }
}
