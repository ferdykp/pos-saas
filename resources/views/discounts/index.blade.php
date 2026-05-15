<x-app-layout>
    <div class="p-6 mx-auto max-w-7xl">
        <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
            <div>
                <h2 class="text-3xl font-black text-gray-900">Manajemen Diskon</h2>
                <p class="mt-1 text-sm font-medium text-gray-500">Kelola promo event, morning discount, dan potongan
                    harga menu.</p>
            </div>
            <a href="{{ route('discounts.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 font-black text-white transition-all bg-blue-600 shadow-lg rounded-2xl hover:bg-blue-700 active:scale-95">
                <i class="fa-solid fa-plus"></i>
                Buat Diskon Baru
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-3">
            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <p class="text-xs font-bold tracking-wider text-gray-400 uppercase">Total Event</p>
                <p class="text-2xl font-black text-gray-900">{{ $discounts->count() }}</p>
            </div>
            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <p class="text-xs font-bold tracking-wider text-green-500 uppercase">Aktif Sekarang</p>
                <p class="text-2xl font-black text-gray-900">
                    {{ $discounts->filter(fn($d) => $d->isValidNow())->count() }}
                </p>
            </div>
            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <p class="text-xs font-bold tracking-wider text-blue-500 uppercase">Tipe Terbanyak</p>
                <p class="text-2xl font-black text-gray-900">Persentase (%)</p>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-100 shadow-xl rounded-3xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Nama Promo</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Nilai Diskon</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Periode & Waktu</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Menu Terikat</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-black text-right text-gray-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($discounts as $discount)
                            <tr class="transition-colors hover:bg-gray-50/80">
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-gray-900">{{ $discount->name }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium">ID:
                                        #DSC-{{ $discount->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($discount->type === 'percentage')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-black rounded-lg text-rose-600 bg-rose-50">
                                            {{ number_format($discount->value, 0) }}% OFF
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-black text-blue-600 rounded-lg bg-blue-50">
                                            -Rp {{ number_format($discount->value, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col gap-1 font-medium text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="text-gray-400 fa-regular fa-calendar"></i>
                                            {{ $discount->start_date ? \Carbon\Carbon::parse($discount->start_date)->format('d M') : 'Selamanya' }}
                                            -
                                            {{ $discount->end_date ? \Carbon\Carbon::parse($discount->end_date)->format('d M Y') : '∞' }}
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <i class="text-gray-400 fa-regular fa-clock"></i>
                                            {{ $discount->start_time ? \Carbon\Carbon::parse($discount->start_time)->format('H:i') : '24 Jam' }}
                                            -
                                            {{ $discount->end_time ? \Carbon\Carbon::parse($discount->end_time)->format('H:i') : '' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @foreach ($discount->products->take(2) as $product)
                                            <span
                                                class="px-2 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-md">
                                                {{ $product->product_name }}
                                            </span>
                                        @endforeach
                                        @if ($discount->products->count() > 2)
                                            <span
                                                class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-500 rounded-md">
                                                +{{ $discount->products->count() - 2 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($discount->isValidNow())
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                            Berjalan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('discounts.edit', $discount->id) }}"
                                            class="p-2 text-gray-400 transition hover:text-blue-600 hover:bg-blue-50 rounded-xl">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus promo ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-400 transition hover:text-rose-600 hover:bg-rose-50 rounded-xl">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="mb-4 text-5xl text-gray-100 fa-solid fa-tags"></i>
                                        <p class="text-lg font-bold text-gray-400">Belum ada skema diskon</p>
                                        <p class="text-sm text-gray-300">Klik tombol di pojok kanan atas untuk mulai
                                            membuat promo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
