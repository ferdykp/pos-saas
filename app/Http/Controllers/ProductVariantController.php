<?php

// app/Http/Controllers/ProductVariantController.php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index()
    {
        return ProductVariant::with('product')->latest()->get();
    }

    public function store(Request $request)
    {
        return ProductVariant::create($request->all());
    }

    public function show(ProductVariant $productVariant)
    {
        return $productVariant->load('product');
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $productVariant->update($request->all());

        return $productVariant;
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
