<x-app-layout>
    <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

        <div class="relative bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-blue-600 to-indigo-700"></div>

            <div
                class="relative flex flex-col items-end justify-between gap-6 px-8 pt-16 pb-8 md:flex-row md:items-center">
                <div class="flex flex-col items-center space-y-4 md:flex-row md:items-end md:space-y-0 md:space-x-6">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-white p-2 shadow-xl">
                        <div
                            class="w-full h-full rounded-[2rem] bg-blue-50 flex items-center justify-center text-blue-600 text-4xl font-black border-2 border-blue-100">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>

                    <div class="pb-2 text-center md:text-left">
                        <h1 class="text-3xl font-black tracking-tight text-gray-900">{{ auth()->user()->name }}</h1>
                        <p class="font-medium text-gray-500">{{ auth()->user()->email }}</p>
                        <div class="flex items-center justify-center mt-2 md:justify-start">
                            <span
                                class="px-3 py-1 bg-green-100 text-green-600 text-[10px] font-black uppercase rounded-lg tracking-wider">
                                Akun Terverifikasi
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('profile.edit') }}"
                        class="px-6 py-3 font-bold text-gray-700 transition bg-white border border-gray-200 shadow-sm rounded-2xl hover:bg-gray-50 active:scale-95">
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-gray-900">Bisnis Aktif</h3>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>

                @if (auth()->user()->tenant)
                    <div class="flex items-center p-4 space-x-4 border border-blue-100 bg-blue-50 rounded-3xl">
                        <div
                            class="flex items-center justify-center w-12 h-12 font-black text-blue-600 bg-white shadow-sm rounded-xl">
                            {{ substr(auth()->user()->tenant->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900">{{ auth()->user()->tenant->name }}</p>
                            <p class="text-xs italic font-medium text-gray-500">{{ auth()->user()->tenant->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm italic text-gray-400">Belum ada bisnis yang aktif.</p>
                @endif

                <div class="flex items-center justify-between pt-6 mt-6 text-sm font-bold border-t border-gray-50">
                    <span class="text-gray-400">Total Bisnis:</span>
                    <span class="text-gray-900">{{ auth()->user()->tenants->count() }} Unit</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="mb-6 text-lg font-black text-gray-900">Keamanan Akun</h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-sm font-medium text-gray-600">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span>Password Terenkripsi</span>
                        </div>
                        <span class="px-2 py-1 text-xs font-bold text-blue-600 rounded-lg bg-blue-50">Aktif</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-sm font-medium text-gray-600">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span>Email Terverifikasi</span>
                        </div>
                        <span
                            class="text-xs italic font-bold text-gray-400">{{ auth()->user()->email_verified_at ? 'Ya' : 'Belum' }}</span>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="text-[11px] text-gray-400 font-medium leading-relaxed">
                        Terakhir login: {{ now()->diffForHumans() }} (GMT+7)
                    </p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-2 gap-4 mt-8 md:grid-cols-4">
            <a href="{{ route('profile.edit') }}"
                class="p-4 text-center transition bg-white border border-gray-100 rounded-3xl hover:border-blue-500 hover:shadow-md group">
                <p
                    class="text-xs font-black tracking-widest text-gray-400 uppercase transition group-hover:text-blue-600">
                    Manage</p>
                <p class="mt-1 text-sm font-bold text-gray-900">Tenant</p>
            </a>
            <div class="p-4 text-center border border-gray-100 cursor-not-allowed bg-gray-50/50 rounded-3xl">
                <p class="text-xs font-black tracking-widest text-gray-300 uppercase">Logs</p>
                <p class="mt-1 text-sm font-bold text-gray-300">Aktivitas</p>
            </div>
        </div>
    </div>
</x-app-layout>
