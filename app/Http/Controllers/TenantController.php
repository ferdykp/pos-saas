<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = auth()->user()->tenants()
            ->withCount('users')
            ->orderBy('id', 'desc')
            ->get();

        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'img_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('img_logo')) {
            $file = $request->file('img_logo');
            $fileName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $logoPath = $file->storeAs('logos', $fileName, 'public');
        }

        $tenant = auth()->user()->tenants()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(100, 999),
            'business_type' => $request->business_type,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'img_logo' => $logoPath,
            'status' => 'active',
        ]);

        auth()->user()->update([
            'tenant_id' => $tenant->id
        ]);

        return redirect()->route('tenants.index')->with('success', 'Bisnis berhasil didaftarkan!');
    }

    public function edit(Tenant $tenant)
    {
        if ($tenant->user_id !== auth()->id()) {
            abort(403);
        }
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        if ($tenant->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'img_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'business_type' => $request->business_type,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('img_logo')) {
            // Hapus logo lama jika ada
            if ($tenant->img_logo && Storage::disk('public')->exists($tenant->img_logo)) {
                Storage::disk('public')->delete($tenant->img_logo);
            }

            $file = $request->file('img_logo');
            $fileName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $data['img_logo'] = $file->storeAs('logos', $fileName, 'public');
        }

        $tenant->update($data);

        return redirect()->route('tenants.index')->with('success', 'Data bisnis berhasil diperbarui!');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->user_id !== auth()->id()) {
            abort(403);
        }

        // Hapus file logo dari storage sebelum menghapus data
        if ($tenant->img_logo && Storage::disk('public')->exists($tenant->img_logo)) {
            Storage::disk('public')->delete($tenant->img_logo);
        }

        $tenant->delete();

        // Jika tenant yang dihapus adalah tenant aktif, set jadi null atau ganti ke tenant lain
        if (auth()->user()->tenant_id == $tenant->id) {
            $nextTenant = auth()->user()->tenants()->first();
            auth()->user()->update([
                'tenant_id' => $nextTenant ? $nextTenant->id : null
            ]);
        }

        return redirect()->route('tenants.index')->with('success', 'Bisnis berhasil dihapus!');
    }

    // METHOD BARU UNTUK SWITCH / PINDAH TOKO AKTIF
    // public function switchTenant(Tenant $tenant)
    // {
    //     if ($tenant->user_id !== auth()->id()) {
    //         abort(403);
    //     }

    //     auth()->user()->update([
    //         'tenant_id' => $tenant->id
    //     ]);

    //     return redirect()->back()->with('success', "Berhasil pindah ke toko: {$tenant->name}");
    // }
}
