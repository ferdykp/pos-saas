<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller

{
    public function index()
    {
        $discounts = Discount::where('tenant_id', auth()->user()->tenant_id)->with('products')->latest()->get();
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        // Ambil semua produk milik tenant untuk dipilih di view
        $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('discounts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'product_ids' => 'required|array', // Harus memilih minimal 1 menu
        ]);

        $discount = Discount::create([
            'tenant_id'  => auth()->user()->tenant_id,
            'name'       => $request->name,
            'type'       => $request->type,
            'value'      => $request->value,
            'start_date' => $request->start_date ?: null,
            'end_date'   => $request->end_date ?: null,
            'start_time' => $request->start_time ?: null,
            'end_time'   => $request->end_time ?: null,
            'days'       => $request->days ?: null, // Array hari
            'is_active'  => $request->has('is_active')
        ]);

        // Hubungkan ke menu yang dipilih
        $discount->products()->sync($request->product_ids);

        return redirect()->route('discounts.index')->with('success', 'Skema diskon berhasil dibuat.');
    }

    // Pastikan relasi products di-load agar checkbox di view tercentang otomatis
    public function edit(Discount $discount)
    {
        $discount->load('products');
        $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();

        // Ambil ID produk yang sudah terpilih untuk mempermudah pengecekan di blade
        $selectedProductIds = $discount->products->pluck('id')->toArray();

        return view('discounts.edit', compact('discount', 'products', 'selectedProductIds'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'product_ids' => 'required|array',
        ]);

        $discount->update([
            'name'       => $request->name,
            'type'       => $request->type,
            'value'      => $request->value,
            'start_date' => $request->start_date ?: null,
            'end_date'   => $request->end_date ?: null,
            'start_time' => $request->start_time ?: null,
            'end_time'   => $request->end_time ?: null,
            'days'       => $request->days ?: null,
            'is_active'  => $request->has('is_active')
        ]);

        // Update relasi tabel pivot
        $discount->products()->sync($request->product_ids);

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        // Hapus relasi di tabel pivot terlebih dahulu (Opsional jika migration pakai cascade)
        $discount->products()->detach();
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dihapus.');
    }
}
