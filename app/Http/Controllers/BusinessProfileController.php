<?php

namespace App\Http\Controllers;

use App\Support\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        return view('business.edit', ['tenant' => auth()->user()->tenant, 'types' => BusinessProfile::TYPES]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_type' => ['required', Rule::in(array_keys(BusinessProfile::TYPES))],
            'business_modules' => 'required|array|min:1',
            'business_modules.*' => ['required', 'distinct', Rule::in(['goods', 'services', 'food'])],
        ]);
        $request->user()->tenant->update($data);

        return back()->with('success', 'Jenis usaha dan fitur tersimpan. Produk, stok, serta transaksi lama tetap tersedia.');
    }
}
