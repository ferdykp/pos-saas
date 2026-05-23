<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8" x-data="{ showDeleteModal: false, deleteUrl: '', tenantName: '' }">

        @if (session('status'))
            <div
                class="flex items-center gap-2 p-4 mb-6 text-sm text-blue-800 border border-blue-100 bg-blue-50 rounded-xl">
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <div
            class="flex flex-col items-start justify-between gap-4 pb-6 mb-8 border-b border-gray-100 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Tenant (Toko/Bisnis)</h1>
                <p class="mt-1.5 text-sm text-gray-500">Kelola bisnis Anda atau buat cabang baru untuk operasional POS
                    Kasir.</p>
            </div>

            <a href="{{ route('tenants.create') }}"
                class="inline-flex justify-center items-center py-2.5 px-4 text-sm font-semibold text-white bg-blue-600 rounded-xl shadow-sm transition-all hover:bg-blue-700 active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tambah Tenant Baru
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-3">
            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Total Tenant Bisnis</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $tenants->count() }}</p>
            </div>
            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Total Pengguna Aktif</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Pertumbuhan Mitra</p>
                <p class="mt-2 text-3xl font-bold text-green-600">
                    {{ $tenants->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
            </div>
        </div>

        <div class="bg-white border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] rounded-2xl overflow-hidden">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr
                            class="text-xs font-bold tracking-wider text-gray-400 uppercase border-b border-gray-100 bg-gray-50/70">
                            <th class="px-6 py-4">Nama Perusahaan / Toko</th>
                            <th class="px-6 py-4">Tipe Bisnis</th>
                            <th class="px-6 py-4 text-center">Jumlah Pegawai</th>
                            <th class="px-6 py-4 text-center">Status Aktif</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium text-gray-600 divide-y divide-gray-50">
                        @forelse($tenants as $tenant)
                            <tr
                                class="transition-colors {{ auth()->user()->tenant_id == $tenant->id ? 'bg-blue-50/30' : 'hover:bg-gray-50/50' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($tenant->img_logo)
                                            <img src="{{ asset('storage/' . $tenant->img_logo) }}"
                                                class="object-cover w-8 h-8 border border-gray-100 rounded-lg">
                                        @else
                                            <div
                                                class="flex items-center justify-center w-8 h-8 text-xs font-bold text-blue-600 uppercase border border-blue-100 rounded-lg bg-blue-50">
                                                {{ substr($tenant->name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="block font-bold text-gray-900">{{ $tenant->name }}</span>
                                            <span class="text-[11px] text-gray-400 font-normal">ID: {{ $tenant->id }}
                                                • {{ $tenant->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-600">
                                    {{ $tenant->business_type ?? 'Belum Diatur' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-xs font-bold text-gray-700 bg-gray-100 rounded-full">
                                        {{ $tenant->users_count ?? $tenant->users()->count() }} Staf
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if (auth()->user()->tenant_id == $tenant->id)
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-black text-blue-700 bg-blue-100 rounded-md uppercase border border-blue-200">
                                            ✓ Sedang Digunakan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-bold text-gray-400 bg-gray-50 rounded-md uppercase">
                                            Standby
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (auth()->user()->tenant_id != $tenant->id)
                                            <form action="{{ route('tenants.switch', ['tenant' => $tenant->id]) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex py-1.5 px-3 text-xs font-bold text-blue-600 bg-blue-50 rounded-lg transition-colors hover:bg-blue-100">
                                                    Gunakan Toko
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('tenants.edit', $tenant->id) }}"
                                            class="inline-flex p-1.5 text-gray-500 bg-gray-50 rounded-lg hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11 20H8v-3l9.414-9.414z" />
                                            </svg>
                                        </a>

                                        <button type="button"
                                            @click="showDeleteModal = true; deleteUrl = '{{ route('tenants.destroy', $tenant->id) }}'; tenantName = '{{ $tenant->name }}'"
                                            class="inline-flex p-1.5 text-gray-500 bg-gray-50 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 italic text-center text-gray-400">
                                    Belum ada tenant yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/40 backdrop-blur-sm"
            x-cloak>

            <div class="w-full max-w-md p-6 bg-white shadow-xl rounded-2xl" @click.away="showDeleteModal = false">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-50 rounded-xl">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div class="mt-4 text-center">
                    <h3 class="text-lg font-bold text-gray-900">Hapus Cabang Bisnis?</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus <span class="font-bold text-gray-900"
                            x-text="tenantName"></span>? Seluruh data produk, kategori, dan transaksi transaksi yang
                        terikat pada toko ini akan dihapus permanen.
                    </p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" @click="showDeleteModal = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-md shadow-red-100 transition-colors">
                            Ya, Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
