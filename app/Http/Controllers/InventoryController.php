<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Menampilkan produk beserta jumlah stoknya dari tabel inventories
        $products = Product::with('inventory')->latest()->paginate(10);
        return view('inventory.index', compact('products'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($request) {
            $tenantId = auth()->user()->tenant_id;

            // 1. Ambil atau buat record inventory
            $inventory = Inventory::firstOrCreate(
                ['tenant_id' => $tenantId, 'product_id' => $request->product_id],
                ['quantity' => 0]
            );

            $beforeStock = $inventory->quantity;
            $qty = $request->quantity;

            // 2. Hitung stok baru
            if ($request->type === 'stock_in') {
                $afterStock = $beforeStock + $qty;
            } elseif ($request->type === 'stock_out' || $request->type === 'adjustment') {
                // Adjustment diasumsikan mengurangi jika diinput manual di sini, 
                // atau Anda bisa modifikasi sesuai kebutuhan
                $afterStock = $beforeStock - $qty;
            }

            // 3. Update Tabel Inventory
            $inventory->update(['quantity' => $afterStock]);

            // 4. Catat Mutasi ke StockMovement
            StockMovement::create([
                'tenant_id' => $tenantId,
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $qty,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'note' => $request->note,
            ]);
        });

        return redirect()->back()->with('success', 'Stok berhasil diperbarui');
    }

    public function history()
    {
        $movements = StockMovement::with(['product', 'material', 'user']) // Load semua relasi
            ->latest()
            ->paginate(20);
        return view('inventory.history', compact('movements'));
    }
}
