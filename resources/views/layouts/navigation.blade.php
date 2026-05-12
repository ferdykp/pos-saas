<nav x-data="{ open: false, atTop: true }" @scroll.window="atTop = (window.pageYOffset > 10 ? false : true)"
    :class="{ 'bg-white/80 backdrop-blur-md shadow-sm border-b': !atTop, 'bg-transparent': atTop }"
    class="fixed z-50 w-full transition-all duration-300">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-blue-600 shadow-lg rounded-xl shadow-blue-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span :class="{ 'text-white': atTop && !open, 'text-gray-900': !atTop || open }"
                        class="text-xl font-bold tracking-tight transition-colors duration-300">
                        POS<span class="text-blue-600">SaaS</span>
                    </span>
                </a>
            </div>

            <div class="items-center hidden space-x-8 md:flex">
                <a href="#features"
                    :class="{ 'text-blue-100 hover:text-white': atTop, 'text-gray-600 hover:text-blue-600': !atTop }"
                    class="font-medium transition">Fitur</a>
                <a href="#pricing"
                    :class="{ 'text-blue-100 hover:text-white': atTop, 'text-gray-600 hover:text-blue-600': !atTop }"
                    class="font-medium transition">Harga</a>

                @auth
                    <a href="{{ route('dashboard') }}"
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" :class="{ 'text-white': atTop, 'text-gray-900': !atTop }"
                        class="font-semibold transition">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Coba
                        Gratis</a>
                @endauth
            </div>

            <div class="flex items-center md:hidden">
                <button @click="open = !open" :class="{ 'text-white': atTop && !open, 'text-gray-900': !atTop || open }"
                    class="p-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition class="bg-white border-b md:hidden">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="#features" class="block px-3 py-2 font-medium text-gray-700">Fitur</a>
            <a href="{{ route('login') }}" class="block px-3 py-2 font-medium text-gray-700">Masuk</a>
            <a href="{{ route('register') }}" class="block px-3 py-2 font-bold text-blue-600">Daftar Sekarang</a>
        </div>
    </div>
</nav>
