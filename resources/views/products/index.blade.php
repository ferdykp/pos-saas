<x-app-layout>
    @section('title', 'Inventaris Produk')

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-desktop" x-data="{ showDeleteModal: false, deleteUrl: '', productName: '' }">

        <!-- Header Halaman & Action Button -->
        <div
            class="flex flex-col justify-between gap-4 pb-6 mb-8 border-b sm:flex-row sm:items-center border-border-200">
            <div>
                <h1 class="font-heading font-bold text-2xl md:text-[28px] text-ink-900 leading-tight">
                    Inventaris Produk
                </h1>
                <p class="mt-1 text-xs font-body md:text-sm text-ink-700">
                    Kelola ketersediaan stok, harga modal, harga jual, dan varian kategori menu usaha Anda.
                </p>
            </div>

            <!-- Primary Action Button (Height 44px, Emerald Green) -->
            <a href="{{ route('products.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 active:bg-primary-900 font-body md:text-sm shrink-0">
                <i class="text-xs fa-solid fa-plus"></i>
                <span>Tambah Produk Baru</span>
            </a>
        </div>

        <!-- Metric Stat Cards Produk & Stok -->
        <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-3 md:gap-6">
            <div
                class="flex items-center justify-between p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Total Item
                        Produk</span>
                    <p class="mt-2 font-mono text-2xl font-semibold md:text-3xl text-ink-900">
                        {{ $products->count() }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center text-lg rounded-md w-11 h-11 bg-primary-50 text-primary-600 shrink-0">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
            </div>

            <div
                class="flex items-center justify-between p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Stok Aman</span>
                    <p class="mt-2 font-mono text-2xl font-semibold md:text-3xl text-primary-600">
                        {{ $products->filter(fn($p) => $p->stock > $p->min_stock)->count() }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center text-lg rounded-md w-11 h-11 bg-primary-100 text-primary-700 shrink-0">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <div
                class="flex items-center justify-between p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Stok Menipis /
                        Habis</span>
                    <p class="mt-2 font-mono text-2xl font-semibold md:text-3xl text-semantic-danger">
                        {{ $products->filter(fn($p) => $p->stock <= $p->min_stock)->count() }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center text-lg rounded-md w-11 h-11 bg-red-50 text-semantic-danger shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>

        <!-- Table Container (Spesifikasi: Row Height 48px, bg surface-100 header) -->
        <div class="mb-6 overflow-hidden border rounded-lg shadow-sm bg-surface-0 border-border-200">
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr
                            class="h-12 text-xs font-semibold tracking-wider uppercase border-b bg-surface-100 border-border-200 font-heading text-ink-700">
                            <th class="px-5 py-3">Detail Produk & SKU</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3 text-right">Harga Jual</th>
                            <th class="px-5 py-3 text-center">Status Stok</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y font-body md:text-sm text-ink-900 divide-border-200">
                        @forelse ($products as $product)
                            <tr class="h-12 transition-colors hover:bg-surface-100/60">

                                <!-- Product Avatar/Initial, Name & SKU -->
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->product_name }}"
                                                class="object-cover border rounded-md w-9 h-9 border-border-200 shrink-0">
                                        @else
                                            <div
                                                class="flex items-center justify-center text-xs font-bold rounded-md w-9 h-9 bg-primary-100 text-primary-700 font-heading shrink-0">
                                                {{ substr($product->product_name, 0, 2) }}
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <span class="block font-semibold leading-tight truncate text-ink-900">
                                                {{ $product->product_name }}
                                            </span>
                                            <span
                                                class="font-mono text-[11px] font-normal text-ink-400 mt-0.5 block truncate">
                                                SKU: {{ $product->sku }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-surface-100 border border-border-200 text-ink-700">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>

                                <!-- Sell Price (Monospace) -->
                                <td class="px-5 py-3 font-mono font-semibold text-right text-ink-900">
                                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                </td>

                                <!-- Stock Status Badge (Pill Shape: radius-full) -->
                                <td class="px-5 py-3 text-center">
                                    @if ($product->type === 'service')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold text-accent-700 bg-accent-100 rounded-full">
                                            Layanan / Jasa
                                        </span>
                                    @elseif ($product->stock <= $product->min_stock)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold text-semantic-danger bg-red-50 rounded-full">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            {{ $product->stock }} Unit (Menipis)
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold text-primary-700 bg-primary-100 rounded-full">
                                            {{ $product->stock }} Unit
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Column -->
                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('products.edit', $product->id) }}"
                                            class="p-2 transition-colors rounded-md text-ink-700 hover:text-primary-600 bg-surface-100 hover:bg-primary-50"
                                            title="Edit Produk">
                                            <i class="text-xs fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <button type="button"
                                            @click="showDeleteModal = true; deleteUrl = '{{ route('products.destroy', $product->id) }}'; productName = '{{ addslashes($product->product_name) }}'"
                                            class="p-2 transition-colors rounded-md text-ink-700 hover:text-semantic-danger bg-surface-100 hover:bg-red-50"
                                            title="Hapus Produk">
                                            <i class="text-xs fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <!-- Friendly Empty State -->
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="flex items-center justify-center w-12 h-12 mb-2 rounded-full bg-primary-50 text-primary-600">
                                            <i class="text-xl fa-solid fa-boxes-packing"></i>
                                        </div>
                                        <p class="text-sm font-semibold font-heading text-ink-900">Belum ada produk
                                            dalam katalog</p>
                                        <p class="font-body text-xs text-ink-700 mt-0.5 max-w-xs">
                                            Mulai tambahkan daftar produk atau layanan jasa yang dijual di toko Anda.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Confirm Hapus Produk -->
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/40 backdrop-blur-[2px]">

            <div class="w-full p-6 border rounded-lg shadow-lg max-w-modal-sm bg-surface-0 border-border-200"
                @click.away="showDeleteModal = false">

                <div
                    class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-50 text-semantic-danger">
                    <i class="text-xl fa-solid fa-trash-can"></i>
                </div>

                <div class="mb-6 text-center">
                    <h3 class="text-lg font-semibold font-heading text-ink-900">Hapus Produk Ini?</h3>
                    <p class="mt-2 text-xs leading-relaxed font-body text-ink-700">
                        Apakah Anda yakin ingin menghapus produk <span class="font-semibold text-ink-900"
                            x-text="productName"></span>? Produk ini tidak akan muncul lagi di POS Terminal.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="flex-1 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                        Batal
                    </button>

                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full text-xs font-semibold text-white transition-colors rounded-md h-11 bg-semantic-danger hover:bg-red-700 font-body">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
