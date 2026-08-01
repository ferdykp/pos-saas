<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::where('tenant_id', auth()->user()->tenant_id)->with('user');

        // Filter Nama Staf / Kasir
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Status (open / closed)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Berdasarkan Tanggal Mulai Shift
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        // Ambil data dengan Pagination (10 data per halaman)
        $shifts = $query->orderBy('id', 'desc')->paginate(10);

        return view('shifts.index', compact('shifts'));
    }

    // Helper untuk mengambil shift aktif kasir yang sedang login
    private function getActiveShift()
    {
        $shiftId = session('active_shift_id');

        if ($shiftId) {
            $shift = Shift::where('tenant_id', Auth::user()->tenant_id)
                ->where('id', $shiftId)
                ->where('status', 'open')
                ->first();
            if ($shift) return $shift;
        }

        // Fallback jika session terhapus/expired: Cari shift open di DB
        return Shift::where('tenant_id', Auth::user()->tenant_id)
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();
    }

    // Buka Shift Baru
    public function open(Request $request)
    {
        $request->validate([
            'cash_start' => 'required|numeric|min:0',
        ]);

        // Cek apakah ada shift yang masih open untuk user ini
        $activeShift = $this->getActiveShift();

        if ($activeShift) {
            return response()->json(['success' => false, 'message' => 'Anda masih memiliki shift yang aktif!']);
        }

        $shift = Shift::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'start_time' => Carbon::now(),
            'cash_start' => $request->cash_start,
            'cash_expected' => $request->cash_start,
            'status' => 'open'
        ]);

        // Simpan ID shift ke dalam session kasir
        session(['active_shift_id' => $shift->id]);

        return response()->json(['success' => true, 'message' => 'Shift berhasil dibuka!']);
    }

    // Mendapatkan summary berjalan sebelum tutup shift (Dipanggil via AJAX)
    public function summary()
    {
        $shift = $this->getActiveShift();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif yang ditemukan. Silakan buka shift terlebih dahulu.'
            ], 404);
        }

        // Refresh session jika sempat hilang
        session(['active_shift_id' => $shift->id]);

        // Hitung total penjualan tunai (cash) selama shift ini berlangsung
        // Bisa dihitung berdasarkan shift_id OR rentang waktu jika order belum mencatat shift_id
        $totalCashSales = Order::where('tenant_id', Auth::user()->tenant_id)
            ->where(function ($query) use ($shift) {
                $query->where('shift_id', $shift->id)
                    ->orWhereBetween('created_at', [$shift->start_time, now()]);
            })
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $cashExpected = $shift->cash_start + $totalCashSales;

        return response()->json([
            'success' => true,
            'cash_start' => (float) $shift->cash_start,
            'cash_sales' => (float) $totalCashSales,
            'cash_expected' => (float) $cashExpected
        ]);
    }

    // Tutup Shift
    public function close(Request $request)
    {
        $request->validate([
            'cash_actual' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $shift = $this->getActiveShift();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup shift: Tidak ada shift aktif!'
            ], 404);
        }

        $totalCashSales = Order::where('tenant_id', Auth::user()->tenant_id)
            ->where(function ($query) use ($shift) {
                $query->where('shift_id', $shift->id)
                    ->orWhereBetween('created_at', [$shift->start_time, now()]);
            })
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $cashExpected = $shift->cash_start + $totalCashSales;
        $cashActual = $request->cash_actual;
        $difference = $cashActual - $cashExpected;

        $shift->update([
            'end_time' => Carbon::now(),
            'cash_expected' => $cashExpected,
            'cash_actual' => $cashActual,
            'cash_difference' => $difference,
            'status' => 'closed',
            'notes' => $request->notes
        ]);

        // Hapus session shift aktif
        session()->forget('active_shift_id');

        return response()->json(['success' => true, 'message' => 'Shift berhasil ditutup!']);
    }
}
