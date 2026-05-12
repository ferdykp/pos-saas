<x-app-layout>
    {{-- <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8"> --}}
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-black tracking-tight text-gray-900">Kategori Produk</h1>
            <p class="font-medium text-gray-500">Kelola grup produk untuk memudahkan pencarian di kasir.</p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm sticky top-6">
                    <h3 class="mb-6 text-lg font-bold text-gray-900">Tambah Kategori</h3>
                    <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Nama Kategori</label>
                            <input type="text" name="name" placeholder="Contoh: Minuman Dingin" required
                                class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit"
                            class="w-full py-4 font-black text-white transition bg-blue-600 shadow-lg rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                            Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Nama
                                    Kategori</th>
                                <th
                                    class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                                    Jumlah Produk</th>
                                <th
                                    class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($categories as $category)
                                <tr class="transition hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-900">{{ $category->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $category->slug }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 text-xs font-bold text-blue-600 rounded-lg bg-blue-50">
                                            {{ $category->products_count }} Item
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                            onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button class="p-2 text-gray-400 transition hover:text-red-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-sm italic text-center text-gray-400">Belum
                                        ada kategori.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
