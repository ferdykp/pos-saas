<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class MaterialController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        // Ambil data material
        $materials = \App\Models\Material::where('tenant_id', $tenantId)
            ->latest()
            ->paginate(10);

        // Ambil semua supplier untuk dropdown di modal
        $suppliers = \App\Models\Supplier::where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();

        return view('material.index', compact('materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string',
            'min_stock' => 'required|integer',
            'stock' => 'required|integer|min:0'
        ]);

        DB::transaction(function () use ($request) {
            $material = Material::create([
                'tenant_id' => auth()->user()->tenant_id,
                'name' => $request->name,
                'sku' => 'MAT-' . strtoupper(Str::random(5)),
                'unit' => $request->unit,
                'min_stock' => $request->min_stock,
                'stock' => $request->stock,
            ]);

            if ($request->stock > 0) {
                StockMovement::create([
                    'tenant_id'    => auth()->user()->tenant_id,
                    'product_id'   => null, // WAJIB NULL
                    'material_id'  => $material->id,
                    'user_id'      => auth()->id(),
                    'type'         => 'stock_in',
                    'quantity'     => $request->stock,
                    'before_stock' => 0,
                    'after_stock'  => $request->stock,
                    'note'         => 'Stok awal pendaftaran bahan baku',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function getHistory($id)
    {
        $movements = StockMovement::with(['user', 'supplier'])
            ->where('material_id', $id)
            ->latest()
            ->get();

        return response()->json($movements);
    }
    // public function updateStock(Request $request)
    // {
    //     $request->validate([
    //         'material_id' => 'required|exists:materials,id',
    //         'type' => 'required|in:stock_in,stock_out,adjustment',
    //         'quantity' => 'required|integer|min:1',
    //         'note' => 'nullable|string|max:255'
    //     ]);

    //     DB::transaction(function () use ($request) {
    //         $material = Material::findOrFail($request->material_id);
    //         $beforeStock = $material->stock;
    //         $qty = $request->quantity;

    //         // Tentukan stok akhir berdasarkan tipe
    //         if ($request->type === 'stock_in') {
    //             $afterStock = $beforeStock + $qty;
    //         } else {
    //             // stock_out atau adjustment mengurangi stok
    //             $afterStock = $beforeStock - $qty;
    //         }

    //         // 1. Update stok di tabel materials
    //         $material->update(['stock' => $afterStock]);

    //         // 2. Catat riwayat di stock_movements
    //         StockMovement::create([
    //             'tenant_id' => auth()->user()->tenant_id,
    //             'material_id' => $material->id,
    //             'product_id' => null,
    //             'user_id' => auth()->id(),
    //             'type' => $request->type,
    //             'quantity' => $qty,
    //             'before_stock' => $beforeStock,
    //             'after_stock' => $afterStock,
    //             'note' => $request->note,
    //         ]);
    //     });

    //     return redirect()->back()->with('success', 'Stok bahan baku berhasil diperbarui!');
    // }

    // app/Http/Controllers/MaterialController.php

    public function updateStock(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id', // Opsional tapi disarankan
            'purchase_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            $material = Material::findOrFail($request->material_id);
            $before = $material->stock;

            $after = ($request->type === 'stock_in')
                ? $before + $request->quantity
                : $before - $request->quantity;

            $material->update(['stock' => $after]);

            StockMovement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'material_id' => $material->id,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'purchase_price' => $request->purchase_price ?? 0,
                'before_stock' => $before,
                'after_stock' => $after,
                'note' => $request->note,
            ]);

            return redirect()->back()->with('success', 'Stok berhasil diperbarui dan tercatat di riwayat supplier.');
        });
    }
}
