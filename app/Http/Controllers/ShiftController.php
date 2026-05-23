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

    // Buka Shift Baru
    public function open(Request $request)
    {
        $request->validate([
            'cash_start' => 'required|numeric|min:0',
        ]);

        // Cek apakah ada shift yang masih open untuk user ini
        $activeShift = Shift::where('tenant_id', Auth::user()->tenant_id)
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

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
        $shiftId = session('active_shift_id');
        $shift = Shift::findOrFail($shiftId);

        // Hitung total penjualan tunai (cash) selama shift ini berlangsung
        $totalCashSales = Order::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('grand_total'); // Sesuaikan nama kolom grand_total Anda

        $cashExpected = $shift->cash_start + $totalCashSales;

        return response()->json([
            'success' => true,
            'cash_start' => $shift->cash_start,
            'cash_sales' => $totalCashSales,
            'cash_expected' => $cashExpected
        ]);
    }

    // Tutup Shift
    public function close(Request $request)
    {
        $request->validate([
            'cash_actual' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $shiftId = session('active_shift_id');
        $shift = Shift::findOrFail($shiftId);

        $totalCashSales = Order::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
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
