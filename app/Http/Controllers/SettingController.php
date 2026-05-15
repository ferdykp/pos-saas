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

    public function store(Request $request)
    {
        $data = $request->except('_token');

        // Pastikan tax_active tetap tersimpan sebagai 0 jika tidak dicentang
        if (!$request->has('tax_active')) {
            $data['tax_active'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['tenant_id' => auth()->user()->tenant_id, 'key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Pengaturan berhasil disimpan');
    }
}
