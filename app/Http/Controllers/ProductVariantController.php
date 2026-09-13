<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate(['name' => 'required|string|max:100', 'sku' => ['required', 'string', 'max:255', Rule::unique('product_variants')->ignore($id)], 'price' => 'required|numeric|min:0|max:100000000', 'stock' => 'required|integer|min:0|max:1000000']);
    }

    public function store(Request $request, Product $product)
    {
        return $product->variants()->create($this->validated($request));
    }

    public function update(Request $request, Product $product, int $variant)
    {
        $variant = $product->variants()->findOrFail($variant);
        $variant->update($this->validated($request, $variant->id));

        return $variant;
    }

    public function destroy(Product $product, int $variant)
    {
        abort_if($product->orderItems()->where('variant_id', $variant)->exists(), 422, 'Varian memiliki riwayat transaksi.');
        $product->variants()->findOrFail($variant)->delete();

        return response()->json(['success' => true]);
    }
}
