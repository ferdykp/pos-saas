<nav x-data="{ open: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="scrolled ? 'shadow-lg bg-white/80' : 'shadow-md bg-primary-50'"
    class="sticky top-0 z-50 font-sans transition-all duration-300 border-b border-border-200 backdrop-blur-md">
    <div class="flex items-center justify-between px-4 py-5 mx-3 max-w-8xl sm:px-6 lg:px-8">
        {{-- Logo --}}
        <div class="flex items-center space-x-10">
            <a href="/" class="flex items-center transition duration-500 scale-90 hover:scale-105">
                <img src="{{ asset('img/growpos_logo.png') }}" class="w-12" alt="GrowPOS Logo">
                <span
                    class="text-3xl font-bold tracking-tight transition-all duration-500 font-heading text-primary-600 hover:text-primary-900">
                    GrowPOS
                </span>
            </a>
        </div>

        {{-- Desktop Nav Links --}}
        <div class="items-center hidden space-x-6 font-semibold text-body-lg md:flex">
            <x-landing.nav-link href="#fitur">Fitur</x-landing.nav-link>
            <x-landing.nav-link href="#harga">Harga</x-landing.nav-link>
            <x-landing.nav-link href="#us">Tentang Kami</x-landing.nav-link>
        </div>

        {{-- Desktop CTA --}}
        <div class="items-center hidden space-x-8 font-semibold text-body-lg text-ink-700 md:flex">
            <a href="{{ route('login') }}" class="relative transition duration-300 group hover:text-primary-900">
                Masuk
                <span
                    class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-accent-500"></span>
            </a>
            <a href="{{ route('register') }}"
                class="relative px-5 py-2.5 text-white transition duration-500 rounded-sm shadow-md bg-primary-900 scale-95 hover:scale-105 hover:bg-accent-500 shadow-primary-600/20">
                Mulai Gratis
            </a>
        </div>

        {{-- Mobile Toggle --}}
        <button @click="open = !open" class="text-2xl md:hidden text-primary-900" aria-label="Toggle menu">
            <i class="fa-solid" :class="open ? 'fa-xmark' : 'fa-bars'"></i>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="px-6 pb-6 space-y-4 font-semibold border-t bg-surface-0 border-border-200 md:hidden">
        <a href="#fitur" @click="open = false" class="block py-2 hover:text-primary-900">Fitur</a>
        <a href="#harga" @click="open = false" class="block py-2 hover:text-primary-900">Harga</a>
        <a href="#us" @click="open = false" class="block py-2 hover:text-primary-900">Tentang Kami</a>
        <hr class="border-border-200">
        <a href="{{ route('login') }}" class="block py-2 hover:text-primary-900">Masuk</a>
        <a href="{{ route('register') }}"
            class="block px-5 py-3 text-center text-white transition rounded-sm bg-primary-900 hover:bg-accent-500">
            Mulai Gratis
        </a>
    </div>
</nav>
