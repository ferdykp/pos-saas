<x-app-layout>
    @section('title', 'Produk')

    {{-- <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8"> --}}
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="flex flex-col justify-between gap-4 mb-8 md:flex-row md:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Inventaris Produk</h1>
                <p class="font-medium text-gray-500">Kelola stok dan harga jual barang Anda.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('products.create') }}"
                    class="px-6 py-3 font-bold text-white transition bg-blue-600 shadow-lg rounded-2xl shadow-blue-100 hover:bg-blue-700">
                    + Tambah Produk
                </a>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Produk</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Harga Jual</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Stok</th>
                        <th class="px-6 py-4 text-xs font-black text-right text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($products as $product)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 font-bold text-blue-600 bg-blue-50 rounded-xl">
                                        {{ substr($product->product_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $product->product_name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-black text-gray-900">Rp
                                {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg text-xs font-bold {{ $product->stock <= $product->min_stock ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                    {{ $product->stock }} Unit
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="inline-block text-gray-400 transition hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                {{-- Form Delete bisa ditambahkan di sini jika perlu --}}
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 transition hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
