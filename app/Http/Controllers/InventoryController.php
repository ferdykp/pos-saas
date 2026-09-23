<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockAdjustment;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('variants')->where('type', 'product')->where('manage_stock', true)->latest()->paginate(10);

        return view('inventory.index', compact('products'));
    }

    public function adjust(Request $request, StockAdjustment $stock)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|numeric|decimal:0,3|min:0|max:1000000000',
            'note' => 'required|string|max:255',
        ]);
        $stock->product($request->user(), $data);

        return back()->with('success', 'Stok kasir dan inventori berhasil diperbarui.');
    }

    public function history()
    {
        $movements = StockMovement::with(['product', 'material', 'user'])->latest()->paginate(20);

        return view('inventory.history', compact('movements'));
    }
}
