<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar pelanggan milik tenant yang sedang login.
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $customers = Customer::where('tenant_id', $tenantId)
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Menyimpan data pelanggan baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi diperbaiki (is_member tidak wajib dikirim sebagai boolean murni di sini)
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // 2. Simpan Data
            $customer = Customer::create([
                'tenant_id' => auth()->user()->tenant_id,
                'name'      => $request->name,
                'phone'     => $request->phone,
                'is_member' => $request->has('is_member'), // Menghasilkan true/false
                'points'    => 0,
                'total_debt' => 0,
            ]);

            // 3. Logic Redirect
            if ($request->redirect_to === 'pos') {
                return redirect()->route('pos.index')->with('success', 'Pelanggan ' . $customer->name . ' berhasil ditambahkan.');
            }

            return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil didaftarkan.');
        } catch (\Exception $e) {
            // Jika gagal, kembali dengan pesan error asli agar bisa di-debug
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Menampilkan detail pelanggan (untuk riwayat belanja & piutang).
     */
    public function show(Customer $customer)
    {
        // Proteksi Multi-Tenant
        if ($customer->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Nantinya di sini kita akan memuat riwayat pesanan
        // $customer->load('orders');

        return view('customers.show', compact('customer'));
    }

    /**
     * Menghapus data pelanggan.
     */
    public function destroy(Customer $customer)
    {
        // Proteksi Multi-Tenant
        if ($customer->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Cek jika pelanggan masih punya hutang
        if ($customer->total_debt > 0) {
            return redirect()->back()->with('error', 'Pelanggan tidak bisa dihapus karena masih memiliki tanggungan hutang.');
        }

        $customer->delete();
        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
