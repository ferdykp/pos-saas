<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    // public function handleNotification(Request $request)
    // {
    //     try {
    //         // 1. Ambil data JSON mentah yang dikirim oleh Midtrans
    //         $orderId           = $request->input('order_id');
    //         $transactionStatus = $request->input('transaction_status');
    //         $statusCode        = $request->input('status_code');

    //         Log::info("Webhook Masuk - Order ID: {$orderId} | Status: {$transactionStatus} | Code: {$statusCode}");

    //         // 2. PROTEKSI UTAMA: Jika ini data pengetesan sistem Midtrans, lewati dengan aman
    //         if (!$orderId || str_contains($orderId, 'payment_notif_test')) {
    //             Log::info("Data uji coba Midtrans berhasil dilewati dengan aman.");
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Sandbox test handled successfully.'
    //             ], 200);
    //         }

    //         // 3. Cari data nota asli di database kasir Anda
    //         $order = Order::where('invoice_number', $orderId)->first();

    //         if (!$order) {
    //             Log::warning("Invoice {$orderId} tidak ditemukan di database lokal POS.");
    //             return response()->json([
    //                 'status' => 'ignored',
    //                 'message' => 'Order tidak ditemukan di lokal, namun callback sukses diterima.'
    //             ], 200);
    //         }

    //         // 4. Proses update status jika transaksi sukses (settlement atau capture)
    //         if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
    //             if ($order->payment_status !== 'paid') {

    //                 // Update Status Order Jadi Lunas
    //                 $order->update([
    //                     'payment_status' => 'paid',
    //                     'order_status'   => 'completed',
    //                     'paid_amount'    => $order->grand_total,
    //                 ]);

    //                 // Pemicu Poin Customer otomatis via Webhook
    //                 if ($order->customer_id) {
    //                     $customer = Customer::where('tenant_id', $order->tenant_id)->lockForUpdate()->find($order->customer_id);
    //                     if ($customer) {
    //                         $settings = Setting::where('tenant_id', $order->tenant_id)->pluck('value', 'key');
    //                         $pointMode    = $settings['point_mode'] ?? 'disabled';
    //                         $ruleValue    = (float) ($settings['point_rule_value'] ?? 0);
    //                         $isMemberOnly = filter_var($settings['point_member_only'] ?? false, FILTER_VALIDATE_BOOLEAN);

    //                         if ($pointMode !== 'disabled' && ($isMemberOnly ? $customer->is_member : true) && $ruleValue > 0) {
    //                             $pointsEarned = 0;
    //                             switch ($pointMode) {
    //                                 case 'per_investment':
    //                                     $pointsEarned = floor($order->grand_total / $ruleValue);
    //                                     break;
    //                                 case 'flat':
    //                                     $pointsEarned = $ruleValue;
    //                                     break;
    //                                 case 'percentage':
    //                                     $pointsEarned = floor($order->grand_total * ($ruleValue / 100));
    //                                     break;
    //                             }
    //                             if ($pointsEarned > 0) {
    //                                 $customer->increment('points', $pointsEarned);
    //                             }
    //                         }
    //                     }
    //                 }

    //                 Log::info("Invoice {$orderId} BERHASIL DIUPDATE MENJADI PAID VIA WEBHOOK.");
    //             }
    //         } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
    //             $order->update([
    //                 'payment_status' => 'unpaid',
    //                 'order_status'   => 'cancelled'
    //             ]);
    //             Log::info("Invoice {$orderId} dibatalkan/expired.");
    //         }

    //         return response()->json(['status' => 'success', 'message' => 'Callback diproses'], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Crash internal pada Webhook Midtrans: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => 'error_caught',
    //             'message' => $e->getMessage()
    //         ], 200);
    //     }
    // }

    public function handleNotification(Request $request)
    {
        try {
            // 1. Ambil data JSON mentah yang dikirim oleh Midtrans
            $orderId           = $request->input('order_id');
            $transactionStatus = $request->input('transaction_status');
            $statusCode        = $request->input('status_code');

            Log::info("Webhook Masuk - Order ID: {$orderId} | Status: {$transactionStatus} | Code: {$statusCode}");

            // 2. PROTEKSI UTAMA: Jika ini data pengetesan sistem Midtrans, lewati dengan aman
            if (!$orderId || str_contains($orderId, 'payment_notif_test')) {
                Log::info("Data uji coba Midtrans berhasil dilewati dengan aman.");
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sandbox test handled successfully.'
                ], 200);
            }

            // Gunakan Database Transaction untuk membungkus operasi baca & tulis saldo demi menghindari Race Condition
            $response = DB::transaction(function () use ($orderId, $transactionStatus) {

                // 3. Cari data nota asli di database kasir Anda menggunakan lockForUpdate
                $order = Order::where('invoice_number', $orderId)->lockForUpdate()->first();

                if (!$order) {
                    Log::warning("Invoice {$orderId} tidak ditemukan di database lokal POS.");
                    return response()->json([
                        'status' => 'ignored',
                        'message' => 'Order tidak ditemukan di lokal, namun callback sukses diterima.'
                    ], 200);
                }

                // 4. Proses update status jika transaksi sukses (settlement atau capture)
                if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                    if ($order->payment_status !== 'paid') {

                        // Update Status Order Jadi Lunas
                        $order->update([
                            'payment_status'    => 'paid',
                            'order_status'      => 'completed',
                            'paid_amount'       => $order->grand_total,
                            'withdrawal_status' => 'pending' // Siap ditarik dana oleh tenant
                        ]);

                        // =========================================================================
                        // LOGIKA AGREGATOR SAAS: HITUNG KOMISI & UPDATE DOMPET TENANT
                        // =========================================================================
                        // Dipastikan nilainya sama persis dengan yang ada di PosController (Contoh: 1.5%)
                        $platformFeePercent = 1.5;
                        $totalFee = ($order->grand_total * $platformFeePercent) / 100;
                        $netIncomeForTenant = $order->grand_total - $totalFee;

                        // Tambahkan saldo bersih ke wallet tenant dengan proteksi updateOrInsert
                        DB::table('tenant_wallets')->updateOrInsert(
                            ['tenant_id' => $order->tenant_id],
                            [
                                'balance'    => DB::raw("balance + $netIncomeForTenant"),
                                'updated_at' => now()
                            ]
                        );
                        Log::info("SaaS Dompet Tenant ID {$order->tenant_id} berhasil ditambah sebesar Rp {$netIncomeForTenant} (Potongan Komisi Platform: Rp {$totalFee})");
                        // =========================================================================

                        // Pemicu Poin Customer otomatis via Webhook (Kode bawaan Anda)
                        if ($order->customer_id) {
                            $customer = Customer::where('tenant_id', $order->tenant_id)->lockForUpdate()->find($order->customer_id);
                            if ($customer) {
                                $settings = Setting::where('tenant_id', $order->tenant_id)->pluck('value', 'key');
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

                        Log::info("Invoice {$orderId} BERHASIL DIUPDATE MENJADI PAID VIA WEBHOOK.");
                    }
                } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    // Jika sebelumnya sudah dibayar (mencegah rollback status jika polling sudah mendahului settlement)
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'unpaid',
                            'order_status'   => 'cancelled'
                        ]);
                        Log::info("Invoice {$orderId} dibatalkan/expired.");
                    }
                }

                return response()->json(['status' => 'success', 'message' => 'Callback diproses'], 200);
            });

            return $response;
        } catch (\Exception $e) {
            Log::error('Crash internal pada Webhook Midtrans: ' . $e->getMessage());
            return response()->json([
                'status' => 'error_caught',
                'message' => $e->getMessage()
            ], 200); // Tetap kembalikan 200 ke Midtrans agar tidak dikirimi email error terus-menerus
        }
    }
}
