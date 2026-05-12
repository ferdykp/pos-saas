<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    // Gunakan relasi agar otomatis mengisi user_id
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        /**
         * Menggunakan auth()->user()->tenants()->create(...) 
         * secara otomatis akan mengisi kolom 'user_id' di tabel tenants
         * sesuai dengan ID user yang sedang login.
         */
        $tenant = auth()->user()->tenants()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(100, 999),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'active',
        ]);

        // Langsung set tenant ini sebagai tenant aktif untuk user ini
        auth()->user()->update([
            'tenant_id' => $tenant->id
        ]);

        return redirect()->route('dashboard')->with('success', 'Bisnis berhasil didaftarkan!');
    }

    public function create()
    {
        return view('tenants.create');
    }

    // Perbaikan Method Update (Gunakan Validasi)
    public function update(Request $request, Tenant $tenant)
    {
        // Pastikan hanya pemilik yang bisa update
        if ($tenant->user_id !== auth()->id()) {
            abort(403);
        }

        $tenant->update($request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'phone' => 'sometimes',
            'address' => 'sometimes',
            'status' => 'sometimes',
        ]));

        return redirect()->back()->with('success', 'Data bisnis diperbarui');
    }

    // Perbaikan Method Destroy
    public function destroy(Tenant $tenant)
    {
        if ($tenant->user_id !== auth()->id()) {
            abort(403);
        }

        $tenant->delete();

        return redirect()->route('profile.index')->with('success', 'Bisnis berhasil dihapus');
    }
}
