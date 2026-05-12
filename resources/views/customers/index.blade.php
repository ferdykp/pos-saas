<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 mb-8 md:flex-row md:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Database Pelanggan</h1>
                <p class="font-medium text-gray-500">Pantau loyalitas dan riwayat "Bon" pelanggan Anda.</p>
            </div>
            <button onclick="document.getElementById('addCustomerModal').classList.remove('hidden')"
                class="px-6 py-3 font-bold text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                + Registrasi Pelanggan
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
            <div class="bg-blue-50 p-6 rounded-[2rem] border border-blue-100">
                <p class="text-xs font-black tracking-widest text-blue-400 uppercase">Total Pelanggan</p>
                <p class="text-2xl font-black text-blue-900">{{ $customers->total() }}</p>
            </div>
            <div class="bg-amber-50 p-6 rounded-[2rem] border border-amber-100">
                <p class="text-xs font-black tracking-widest uppercase text-amber-400">Total Piutang (Bon)</p>
                <p class="text-2xl font-black text-amber-900">Rp
                    {{ number_format($customers->sum('total_debt'), 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2.5rem]">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Status</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Poin</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">
                            Total Hutang</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $c)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $c->name }}</p>
                                <p class="text-[10px] font-medium text-gray-400">{{ $c->phone ?? 'Tidak ada nomor' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($c->is_member)
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-blue-600 bg-blue-100 rounded-lg uppercase">Member</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-gray-400 bg-gray-100 rounded-lg uppercase">Reguler</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-black text-center text-gray-700">
                                {{ $c->points }} <span class="text-[10px] text-gray-400">pts</span>
                            </td>
                            <td
                                class="px-6 py-4 text-right font-black {{ $c->total_debt > 0 ? 'text-red-500' : 'text-gray-900' }}">
                                Rp {{ number_format($c->total_debt, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="text-[10px] font-black text-blue-600 uppercase">Detail</button>
                                    <form action="{{ route('customers.destroy', $c->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-[10px] font-black text-red-400 uppercase">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 italic text-center text-gray-400">Belum ada pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $customers->links() }}</div>
    </div>

    <div id="addCustomerModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <h3 class="mb-6 text-2xl font-black text-gray-900">Pelanggan Baru</h3>
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Nama Lengkap</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">No.
                            WhatsApp (Untuk Struk Digital)</label>
                        <input type="text" name="phone" placeholder="08..."
                            class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center p-4 bg-blue-50 rounded-2xl">
                        <input type="checkbox" name="is_member" id="is_member_check" value="1"
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_member_check" class="ml-3 text-sm font-bold text-blue-900">Daftarkan sebagai
                            Member Aktif</label>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')"
                        class="flex-1 py-4 font-bold text-gray-500 bg-gray-100 rounded-2xl">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 font-black text-white bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
