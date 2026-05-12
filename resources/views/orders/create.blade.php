@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6 h-[calc(100vh-180px)]">
        <div class="col-span-12 overflow-y-auto lg:col-span-8">
            <div class="flex gap-4 mb-6">
                <x-input class="flex-1" placeholder="Cari barcode atau nama produk..." />
                <x-select class="w-48">
                    <option>Semua Kategori</option>
                </x-select>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($products as $product)
                    <div class="p-4 transition bg-white border shadow-sm cursor-pointer rounded-xl hover:border-blue-500">
                        <div class="flex items-center justify-center h-32 mb-3 bg-gray-100 rounded-lg">
                            <span class="italic text-gray-400">No Image</span>
                        </div>
                        <h4 class="font-bold text-gray-800 truncate">{{ $product->name }}</h4>
                        <p class="font-bold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-400">Stok: {{ $product->stock }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col h-full col-span-12 lg:col-span-4">
            <x-card class="flex flex-col flex-1">
                <x-slot name="title text-lg">Pesanan Saat Ini</x-slot>

                <div class="flex-1 overflow-y-auto">
                    @include('orders.partials.items')
                </div>

                <div class="pt-4 space-y-2 border-t">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-blue-600">
                        <span>Total</span>
                        <span>Rp 0</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <x-button variant="secondary">Hold</x-button>
                        <x-button variant="success" class="py-3 font-bold">PEMBAYARAN</x-button>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
@endsection
