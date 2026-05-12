<x-app-layout>
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="mb-10">
            <h1 class="text-3xl font-black tracking-tight text-gray-900">Pengaturan Akun</h1>
            <p class="mt-1 font-medium text-gray-500">Kelola informasi profil dan daftar unit bisnis Anda.</p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            <div class="space-y-8 lg:col-span-1">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="mb-6 text-lg font-bold text-gray-900">Informasi Personal</h3>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="mb-6 text-lg font-bold text-gray-900">Keamanan</h3>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daftar Bisnis (Tenant)</h3>
                            <p class="text-sm text-gray-500">Pilih atau kelola unit usaha Anda.</p>
                        </div>
                        <a href="{{ route('tenants.create') }}"
                            class="inline-flex items-center px-4 py-2 text-xs font-bold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent shadow-lg rounded-xl hover:bg-blue-700 shadow-blue-100">
                            + Tambah Tenant
                        </a>
                    </div>

                    <div class="space-y-4">
                        @foreach ($tenants as $tenant)
                            <div
                                class="flex items-center justify-between p-5 rounded-2xl border {{ auth()->user()->tenant_id == $tenant->id ? 'border-blue-500 bg-blue-50/30' : 'border-gray-100 bg-gray-50' }}">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 font-black text-blue-600 bg-white shadow-sm rounded-xl">
                                        {{ substr($tenant->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-black text-gray-900">{{ $tenant->name }}</p>
                                            @if (auth()->user()->tenant_id == $tenant->id)
                                                <span
                                                    class="px-2 py-0.5 bg-blue-600 text-[10px] text-white font-bold rounded-md uppercase">Aktif</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $tenant->email }} • {{ $tenant->phone }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if (auth()->user()->tenant_id != $tenant->id)
                                        <form action="{{ route('tenants.switch', $tenant->id) }}" method="POST">
                                            @csrf
                                            <button
                                                class="p-2 text-gray-400 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-blue-600"
                                                title="Pindah ke Bisnis Ini">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-width="2"
                                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('tenants.edit', $tenant->id) }}"
                                        class="p-2 text-gray-400 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-orange-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('tenants.destroy', $tenant->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus tenant ini? Semua data penjualan di dalamnya akan hilang!')">
                                        @csrf @method('DELETE')
                                        <button
                                            class="p-2 text-gray-400 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div
                    class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h4 class="mb-2 text-xl font-bold">Butuh bantuan Multi-Outlet?</h4>
                        <p class="mb-4 text-sm text-indigo-100">Satu akun bisa mengelola banyak cabang toko tanpa biaya
                            tambahan selama masa promo.</p>
                        <button class="px-6 py-2 text-sm font-bold text-indigo-600 bg-white rounded-xl">Baca
                            Panduan</button>
                    </div>
                    <svg class="absolute right-[-20px] bottom-[-20px] w-48 h-48 text-indigo-500 opacity-50"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
