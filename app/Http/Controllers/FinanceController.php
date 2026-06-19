<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order; // Opsional jika ingin ditarik ke laporan detail kelak

class FinanceController extends Controller
{
    /**
     * Menampilkan Halaman Utama Keuangan Tenant
     */
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        // 1. Ambil data dompet/wallet tenant saat ini
        $wallet = DB::table('tenant_wallets')
            ->where('tenant_id', $tenantId)
            ->first();

        // 2. Jika tenant baru pertama kali buka menu ini dan dompet belum terdaftar di DB, buatkan otomatis
        if (!$wallet) {
            DB::table('tenant_wallets')->insert([
                'tenant_id' => $tenantId,
                'balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wallet = DB::table('tenant_wallets')->where('tenant_id', $tenantId)->first();
        }

        // 3. Ambil riwayat pengajuan penarikan dana dari yang terbaru
        $withdrawals = DB::table('withdrawal_requests')
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->get();

        return view('finance.index', compact('wallet', 'withdrawals'));
    }

    /**
     * Memproses Pengajuan Penarikan Dana (Withdrawal Request) via AJAX
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000', // Batas minimal penarikan misalnya Rp 10.000
        ]);

        $tenantId = Auth::user()->tenant_id;
        $amountToWithdraw = (int) $request->amount;

        // Gunakan DB Transaction & Pessimistic Locking untuk mencegah fraud / request ganda dalam waktu bersamaan
        return DB::transaction(function () use ($tenantId, $amountToWithdraw) {

            // Ambil data wallet terbaru dan kunci barisnya
            $wallet = DB::table('tenant_wallets')
                ->where('tenant_id', $tenantId)
                ->lockForUpdate()
                ->first();

            // Proteksi 1: Pastikan data wallet ada
            if (!$wallet) {
                return response()->json(['success' => false, 'message' => 'Dompet toko tidak ditemukan.'], 404);
            }

            // Proteksi 2: Pastikan informasi rekening bank tujuan sudah diisi
            if (empty($wallet->account_number)) {
                return response()->json(['success' => false, 'message' => 'Informasi rekening bank tujuan belum dilengkapi.'], 422);
            }

            // Proteksi 3: Pastikan saldo mencukupi
            if ($wallet->balance < $amountToWithdraw) {
                return response()->json(['success' => false, 'message' => 'Saldo Anda tidak mencukupi untuk melakukan penarikan ini.'], 422);
            }

            // Generate nomor referensi pengajuan unik: WDR-YYYYMMDD-RANDOM
            $referenceNumber = 'WDR-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            // LOGIKA PENTING: Potong langsung saldo utama tenant (Saldo ditahan dalam sistem antrean)
            DB::table('tenant_wallets')
                ->where('tenant_id', $tenantId)
                ->decrement('balance', $amountToWithdraw);

            // Masukkan data ke antrean tabel withdrawal_requests
            DB::table('withdrawal_requests')->insert([
                'tenant_id'        => $tenantId,
                'reference_number' => $referenceNumber,
                'bank_name'        => $wallet->bank_name,
                'account_number'   => $wallet->account_number,
                'account_name'     => $wallet->account_name,
                'amount'           => $amountToWithdraw,
                'platform_fee'     => 0, // Bisa diisi nominal biaya transfer antar-bank jika diperlukan
                'status'           => 'pending',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan penarikan berhasil didaftarkan.'
            ]);
        });
    }
    /**
     * Menyimpan atau Memperbarui Pengaturan Rekening Bank Tenant
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string|max:50',
            'account_number' => 'required|string|max:30',
            'account_name'   => 'required|string|max:100',
        ]);

        $tenantId = Auth::user()->tenant_id;

        try {
            DB::table('tenant_wallets')->updateOrInsert(
                ['tenant_id' => $tenantId],
                [
                    'bank_name'      => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name'   => $request->account_name,
                    'updated_at'     => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan rekening bank berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
