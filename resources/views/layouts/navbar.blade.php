<header class="z-40 flex items-center justify-between h-20 px-8 bg-white border-b border-gray-100">
    <div class="flex items-center flex-1">
        <button @click="sidebarOpen = true" class="p-2 mr-4 text-gray-500 lg:hidden bg-gray-50 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </button>

        <div
            class="items-center hidden w-full max-w-md px-4 py-2 transition-all border border-transparent md:flex bg-gray-50 rounded-2xl focus-within:border-blue-100 focus-within:bg-white">
            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Cari invoice atau produk..."
                class="w-full text-sm font-medium text-gray-600 placeholder-gray-400 bg-transparent border-none focus:ring-0">
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <button
            class="flex items-center justify-center w-10 h-10 text-gray-400 transition hover:text-blue-600 bg-gray-50 hover:bg-blue-50 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>

        <div class="relative" x-data="{ userMenu: false }">
            <button @click="userMenu = !userMenu"
                class="flex items-center space-x-3 p-1.5 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100">
                <div
                    class="flex items-center justify-center shadow-md w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </button>

            <div x-show="userMenu" @click.away="userMenu = false"
                class="absolute right-0 mt-3 w-56 bg-white rounded-[1.5rem] shadow-2xl border border-gray-100 p-2 z-50">
                <a href="{{ route('profile.index') }}"
                    class="flex items-center p-3 space-x-3 text-sm font-bold text-gray-700 transition rounded-xl hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profil Saya</span>
                </a>
                <a href="#"
                    class="flex items-center p-3 space-x-3 text-sm font-bold text-gray-700 transition rounded-xl hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan Toko</span>
                </a>
                <hr class="my-2 border-gray-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="flex items-center w-full p-3 space-x-3 text-sm font-bold text-red-500 transition rounded-xl hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
