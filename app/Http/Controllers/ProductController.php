<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// <--- PASTIKAN IMPORT INI ADA DI ATAS

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name', 'asc')
            ->get();

        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $tenant = auth()->user()->tenant;
        $plan = $tenant?->currentPlan();

        if (! $plan) {
            return back()->with('error', 'Masa langganan Anda telah habis.');
        }

        // Cek Batas Maksimal Produk
        $currentProductCount = Product::where('tenant_id', $tenant->id)->count();

        if ($currentProductCount >= $plan->max_products) {
            return back()->with('error', "Gagal menambah produk! Paket {$plan->name} dibatasi maksimal {$plan->max_products} produk. Silakan upgrade paket Anda di menu Billing.");
        }
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        Product::create(array_merge($request->catalogData(), [
            'tenant_id' => $tenant->id, 'image' => $imagePath,
        ]));

        $tenantId = auth()->user()->tenant_id;
        Cache::forget("tenant_{$tenantId}_products_pos");

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        // Security check
        if ($product->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $categories = Category::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        if ($product->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $data = $request->catalogData();

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage jika ada produk mengunggah gambar baru
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        DB::transaction(function () use ($product, $data) {
            Tenant::whereKey($product->tenant_id)->lockForUpdate()->firstOrFail();
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $before = (int) $locked->stock;
            $locked->update($data);
            if ($before !== (int) $locked->stock) {
                StockMovement::create([
                    'tenant_id' => $locked->tenant_id, 'product_id' => $locked->id, 'user_id' => auth()->id(),
                    'type' => 'adjustment', 'quantity' => abs($locked->stock - $before),
                    'before_stock' => $before, 'after_stock' => $locked->stock, 'note' => 'Penyesuaian melalui edit katalog',
                ]);
            }
        }, 3);
        Cache::forget("tenant_{$product->tenant_id}_products_pos");

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Tambahan: Fungsi Delete
     */
    public function destroy(Product $product)
    {
        // Security check
        if ($product->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($product->orderItems()->exists() || $product->stockMovements()->exists()) {
            return back()->withErrors(['product' => 'Produk memiliki riwayat transaksi/stok. Nonaktifkan produk agar riwayat tetap utuh.']);
        }

        // 1. Hapus gambar dari storage agar tidak memenuhi server
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // 2. Hapus data dari database
        $product->delete();
        Cache::forget("tenant_{$product->tenant_id}_products_pos");

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }
}
