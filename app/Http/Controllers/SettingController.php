<?php

namespace App\Http\Controllers;

use App\Models\Setting;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua setting untuk tenant ini dan ubah jadi key-value pair
        $settings = Setting::where('tenant_id', auth()->user()->tenant_id)
            ->pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    // public function store(Request $request)
    // {
    //     $data = $request->except('_token');

    //     // Pastikan tax_active tetap tersimpan sebagai 0 jika tidak dicentang
    //     if (!$request->has('tax_active')) {
    //         $data['tax_active'] = '0';
    //     }

    //     foreach ($data as $key => $value) {
    //         Setting::updateOrCreate(
    //             ['tenant_id' => auth()->user()->tenant_id, 'key' => $key],
    //             ['value' => $value]
    //         );
    //     }

    //     return back()->with('success', 'Pengaturan berhasil disimpan');
    // }
    public function store(Request $request)
    {
        $data = $request->except('_token');

        // Pastikan tax_active tetap tersimpan sebagai 0 jika tidak dicentang
        if (!$request->has('tax_active')) {
            $data['tax_active'] = '0';
        }

        // FIX: Pastikan point_member_only tetap tersimpan sebagai 0 jika tidak dicentang
        if (!$request->has('point_member_only')) {
            $data['point_member_only'] = '0';
        }

        // Jika point_mode diset ke disabled, otomatis nol-kan nilai aturan agar database bersih
        if ($request->point_mode === 'disabled') {
            $data['point_rule_value'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['tenant_id' => auth()->user()->tenant_id, 'key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Semua pengaturan berhasil disimpan');
    }

    public function updatePoints(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $configs = [
            'point_mode'         => $request->point_mode,
            'point_rule_value'   => $request->point_rule_value ?? 0,
            'point_member_only'  => $request->has('point_member_only') ? '1' : '0',
        ];

        foreach ($configs as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['tenant_id' => $tenantId, 'key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan poin berhasil diperbarui!');
    }
}
