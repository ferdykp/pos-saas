<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['search' => 'nullable|string|max:100']);
        $search = trim($data['search'] ?? '');
        $products = Product::with(['variants', 'addons', 'materials'])
            ->when($search !== '', fn ($query) => $query->where('product_name', 'like', '%'.$search.'%'))
            ->orderBy('product_name')->orderBy('id')->paginate(24)->withQueryString();
        $materials = Material::orderBy('name')->get();

        return view('menu.configure', compact('products', 'materials', 'search'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'kind' => 'required|in:variant,addon,recipe',
            'name' => 'required_if:kind,variant,addon|nullable|string|max:100',
            'price' => 'required_if:kind,variant,addon|nullable|integer|min:0|max:100000000',
            'cost' => 'nullable|integer|min:0|max:100000000',
            'stock' => 'nullable|integer|min:0|max:1000000',
            'material_id' => ['required_if:kind,recipe', 'nullable', Rule::exists('materials', 'id')->where('tenant_id', auth()->user()->tenant_id)],
            'quantity' => 'required_if:kind,recipe|nullable|integer|min:1|max:1000000',
        ]);
        abort_if($data['kind'] === 'recipe' && $product->type === 'service', 422, 'Layanan jasa tidak memakai resep stok barang.');
        DB::transaction(function () use ($product, $data) {
            $product = Product::lockForUpdate()->findOrFail($product->id);
            if ($data['kind'] === 'variant') {
                $product->variants()->create(['name' => $data['name'], 'price' => $data['price'], 'stock' => $data['stock'] ?? 0, 'sku' => 'VAR-'.Str::uuid()]);
            } elseif ($data['kind'] === 'addon') {
                $product->addons()->create(['name' => $data['name'], 'price' => $data['price'], 'cost' => $data['cost'] ?? null]);
            } else {
                $product->materials()->syncWithoutDetaching([$data['material_id'] => ['quantity' => $data['quantity']]]);
            }
        });

        return back()->with('success', 'Konfigurasi menu tersimpan.');
    }

    public function destroy(Request $request, Product $product)
    {
        $data = $request->validate(['kind' => 'required|in:variant,addon,recipe', 'id' => 'required|integer']);
        DB::transaction(function () use ($data, $product) {
            $product = Product::lockForUpdate()->findOrFail($product->id);
            if ($data['kind'] === 'recipe') {
                $product->materials()->detach($data['id']);
            } elseif ($data['kind'] === 'addon') {
                $product->addons()->findOrFail($data['id'])->delete();
            } else {
                abort_if($product->orderItems()->where('variant_id', $data['id'])->exists(), 422, 'Varian sudah memiliki riwayat transaksi dan tidak dapat dihapus.');
                $product->variants()->findOrFail($data['id'])->delete();
            }
        });

        return back()->with('success', 'Konfigurasi dihapus.');
    }
}
