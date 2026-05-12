{{-- resources/views/products/edit.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="max-w-3xl p-6 bg-white rounded-xl shadow">

        <h1 class="mb-6 text-2xl font-bold">
            Edit Product
        </h1>

        <form action="/products/{{ $product->id }}" method="POST">

            @csrf
            @method('PUT')

            <div class="grid gap-6">

                <div>
                    <label>Name</label>

                    <input type="text" name="name" value="{{ $product->name }}" class="w-full mt-2 border rounded-lg">
                </div>

                <div>
                    <label>SKU</label>

                    <input type="text" name="sku" value="{{ $product->sku }}" class="w-full mt-2 border rounded-lg">
                </div>

                <div>
                    <label>Price</label>

                    <input type="number" name="sell_price" value="{{ $product->sell_price }}"
                        class="w-full mt-2 border rounded-lg">
                </div>

                <div>
                    <label>Stock</label>

                    <input type="number" name="stock" value="{{ $product->stock }}"
                        class="w-full mt-2 border rounded-lg">
                </div>

                <button class="px-4 py-3 text-white bg-blue-600 rounded-lg">
                    Update Product
                </button>

            </div>

        </form>

    </div>
@endsection
