<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        // Ambil data material
        $materials = Material::where('tenant_id', $tenantId)
            ->latest()
            ->paginate(10);

        // Ambil semua supplier untuk dropdown di modal
        $suppliers = Supplier::where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();

        return view('material.index', compact('materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:30',
            'min_stock' => 'required|integer|min:0|max:1000000000',
            'stock' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $material = Material::create([
                'tenant_id' => auth()->user()->tenant_id,
                'name' => $request->name,
                'sku' => 'MAT-'.strtoupper(Str::random(5)),
                'unit' => $request->unit,
                'min_stock' => $request->min_stock,
                'stock' => $request->stock,
            ]);

            if ($request->stock > 0) {
                StockMovement::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'product_id' => null, // WAJIB NULL
                    'material_id' => $material->id,
                    'user_id' => auth()->id(),
                    'type' => 'stock_in',
                    'quantity' => $request->stock,
                    'before_stock' => 0,
                    'after_stock' => $request->stock,
                    'note' => 'Stok awal pendaftaran bahan baku',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function getHistory($id)
    {
        Material::findOrFail($id);
        $movements = StockMovement::with(['user', 'supplier'])
            ->where('material_id', $id)
            ->latest()
            ->get();

        return response()->json($movements);
    }

    public function updateStock(Request $request, StockAdjustment $stock)
    {
        $data = $request->validate([
            'material_id' => 'required|integer|min:0|max:1000000000',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:0|max:1000000000',
            'supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where('tenant_id', $request->user()->tenant_id)],
            'purchase_price' => 'nullable|numeric|min:0|max:999999999999',
            'note' => 'required|string|max:255',
        ]);
        $stock->material($request->user(), $data);

        return back()->with('success', 'Stok bahan dan riwayat berhasil diperbarui.');
    }
}
