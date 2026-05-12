<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Daftar Supplier</h1>
                <p class="font-medium text-gray-500">Kelola hubungan dan termin pembayaran dengan pemasok.</p>
            </div>
            <button onclick="document.getElementById('addSupplierModal').classList.remove('hidden')"
                class="px-6 py-3 font-bold text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                + Tambah Supplier
            </button>
        </div>

        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2.5rem]">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Nama Supplier
                        </th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Termin (TOP)</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Kontak & Bank
                        </th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($suppliers as $item)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $item->name }}</p>
                                <p class="text-[10px] font-bold text-blue-500 uppercase">
                                    SUP-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->term_of_payment > 0)
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-amber-600 bg-amber-100 rounded-lg uppercase">
                                        {{ $item->term_of_payment }} Hari
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-gray-400 bg-gray-100 rounded-lg uppercase">
                                        Cash
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col text-sm">
                                    <span class="font-bold text-gray-700">{{ $item->phone ?? '-' }}</span>
                                    @if ($item->bank_name)
                                        <span class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">
                                            {{ $item->bank_name }} : {{ $item->bank_account_number }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <form action="{{ route('suppliers.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus supplier ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-[10px] font-black tracking-widest text-red-400 uppercase hover:text-red-600 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 italic text-center text-gray-400">Belum ada data
                                supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $suppliers->links() }}</div>
    </div>

    <div id="addSupplierModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-lg shadow-2xl overflow-y-auto max-h-[90vh]">
            <h3 class="mb-6 text-2xl font-black text-gray-900">Tambah Supplier</h3>
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Nama
                                Perusahaan/Orang</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">No.
                                WhatsApp</label>
                            <input type="text" name="phone" placeholder="08..."
                                class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Termin
                                (TOP Hari)</label>
                            <input type="number" name="term_of_payment" value="0"
                                class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="p-5 border border-gray-50 rounded-3xl bg-gray-50/50">
                        <p class="mb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Informasi
                            Pembayaran (Bank)</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Nama Bank</label>
                                <input type="text" name="bank_name" placeholder="BCA/Mandiri"
                                    class="w-full px-4 py-3 font-bold bg-white border-gray-100 rounded-2xl focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-black text-gray-400 uppercase">No.
                                    Rekening</label>
                                <input type="text" name="bank_account_number"
                                    class="w-full px-4 py-3 font-bold bg-white border-gray-100 rounded-2xl focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Alamat
                            Lengkap</label>
                        <textarea name="address" rows="2"
                            class="w-full px-4 py-3 text-sm font-medium border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="document.getElementById('addSupplierModal').classList.add('hidden')"
                        class="flex-1 py-4 font-bold text-gray-500 transition bg-gray-100 rounded-2xl hover:bg-gray-200">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
