<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache; // <--- PASTIKAN IMPORT INI ADA DI ATAS


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

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $plan = $tenant?->currentPlan();

        if (!$plan) {
            return back()->with('error', 'Masa langganan Anda telah habis.');
        }

        // Cek Batas Maksimal Produk
        $currentProductCount = \App\Models\Product::where('tenant_id', $tenant->id)->count();

        if ($currentProductCount >= $plan->max_products) {
            return back()->with('error', "Gagal menambah produk! Paket {$plan->name} dibatasi maksimal {$plan->max_products} produk. Silakan upgrade paket Anda di menu Billing.");
        }
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|unique:products,sku',
            'product_name' => 'required|string|max:255',
            'type' => 'required|in:product,service',
            'sell_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        // Ambil status manage_stock (1 = lacak, 0 = tidak dilacak)
        $manageStock = $request->has('manage_stock') ? 1 : 0;

        Product::create([
            'tenant_id' => auth()->user()->tenant_id,
            'category_id' => $request->category_id,
            'product_name' => $request->product_name,
            'type' => $request->type,
            'sku' => $request->sku,
            'image' => $imagePath,
            'cost_price' => $request->cost_price ?? 0,
            'sell_price' => $request->sell_price,
            // 'stock' => $request->stock ?? 0,
            // 'min_stock' => $request->min_stock ?? 0,
            'stock' => $manageStock === 1 ? ($request->stock ?? 0) : 0, // Jika tidak dilacak, paksa simpan 0
            'min_stock' => $manageStock === 1 ? ($request->min_stock ?? 0) : 0,
            'manage_stock' => $manageStock,
            // 'manage_stock' => $request->has('manage_stock') ? 1 : 0, // Ditambahkan
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

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

    public function update(Request $request, Product $product)
    {
        if ($product->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'product_name' => 'required|string|max:255',
            'sell_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'product_name' => $request->product_name,
            'type' => $request->type,
            'sku' => $request->sku,
            'cost_price' => $request->cost_price ?? 0,
            'sell_price' => $request->sell_price,
            'stock' => $request->stock ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'manage_stock' => $request->has('manage_stock') ? 1 : 0, // Ditambahkan
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage jika ada produk mengunggah gambar baru
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
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
