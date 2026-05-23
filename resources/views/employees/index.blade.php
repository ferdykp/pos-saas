<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8" x-data="{ showAddModal: false }">

        @if (session('success'))
            <div
                class="flex items-center gap-2 p-4 mb-6 text-sm text-green-800 border border-green-100 bg-green-50 rounded-xl">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="flex items-center gap-2 p-4 mb-6 text-sm text-red-800 border border-red-100 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div
            class="flex flex-col items-start justify-between gap-4 pb-6 mb-8 border-b border-gray-100 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Pegawai</h1>
                <p class="mt-1.5 text-sm text-gray-500">Kelola hak akses tim toko, tambah kasir baru, atau manajer
                    operasional.</p>
            </div>

            <button @click="showAddModal = true"
                class="inline-flex justify-center items-center py-2.5 px-4 text-sm font-semibold text-white bg-blue-600 rounded-xl shadow-sm transition-all hover:bg-blue-700 active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tambah Pegawai Baru
            </button>
        </div>

        <div class="bg-white border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] rounded-2xl overflow-hidden">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr
                            class="text-xs font-bold tracking-wider text-gray-400 uppercase border-b border-gray-100 bg-gray-50/70">
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">Email Login</th>
                            <th class="px-6 py-4 text-center">Hak Akses (Role)</th>
                            <th class="px-6 py-4">Bergabung Pada</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium text-gray-600 divide-y divide-gray-50">
                        @foreach ($employees as $emp)
                            <tr class="transition-colors hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 text-xs font-bold text-blue-600 uppercase rounded-full bg-blue-50">
                                            {{ substr($emp->name, 0, 2) }}
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $emp->name }}</span>
                                        @if ($emp->id === auth()->id())
                                            <span
                                                class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded font-normal">Anda</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $emp->email }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($emp->role === 'admin')
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-bold text-red-700 bg-red-50 rounded-md uppercase">Owner
                                            / Admin</span>
                                    @elseif($emp->role === 'manager')
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-bold text-purple-700 bg-purple-50 rounded-md uppercase">Manager</span>
                                    @else
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-bold text-blue-700 bg-blue-50 rounded-md uppercase">Kasir</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $emp->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($emp->id !== auth()->id())
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pegawai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex py-1.5 px-3 text-xs font-bold text-red-600 bg-red-50 rounded-lg transition-colors hover:bg-red-100">
                                                Hapus Akses
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs italic text-gray-400">Tidak ada aksi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="addEmployeeModal" x-show="showAddModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div @click.away="showAddModal = false"
                class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Akun Pegawai</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Nama Lengkap
                            Pegawai</label>
                        <input type="text" name="name" required placeholder="Contoh: Andi Wijaya"
                            class="py-2.5 px-4 w-full text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    </div>

                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Email Login</label>
                        <input type="email" name="email" required placeholder="andi@toko.com"
                            class="py-2.5 px-4 w-full text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    </div>

                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Tingkat Hak Akses
                            (Role)</label>
                        <select name="role" required
                            class="py-2.5 px-4 w-full text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-700">
                            <option value="kasir">Kasir (Hanya Penjualan POS & Shift)</option>
                            <option value="manager">Manager (POS + Kelola Stok & Laporan)</option>
                            <option value="admin">Admin / Partner Toko (Akses Penuh)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Kata Sandi</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="py-2.5 px-4 w-full text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                        </div>
                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Konfirmasi</label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi sandi"
                                class="py-2.5 px-4 w-full text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showAddModal = false"
                            class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200">Batal</button>
                        <button type="submit"
                            class="flex-1 py-3 text-sm font-black text-white bg-blue-600 shadow-lg shadow-blue-200 rounded-xl hover:bg-blue-700">
                            Daftarkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
