<div class="py-20 overflow-hidden md:py-32 bg-primary-50">
    <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">
        <div data-aos="fade-down" class="inline-block py-6">
            <div
                class="text-body-sm rounded-md tracking-wider text-primary-900 text-center font-semibold px-3 py-1.5 bg-accent-100">
                <i class="mr-1 fa-solid fa-star"></i> All in One Solution
            </div>
        </div>

        <div class="flex flex-col items-center justify-between w-full gap-8 lg:flex-row">
            {{-- Text Content --}}
            <div class="w-full max-w-xl" data-aos="fade-right" data-aos-delay="100">
                <h1 class="py-2 text-5xl font-bold leading-tight text-display">
                    Solusi Kasir Digital yang
                    <span class="text-transparent bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text">
                        Tumbuh Bersama
                    </span>
                    Bisnis Anda
                </h1>
                <p class="py-2 text-ink-700 text-body-lg">
                    Berdayakan bisnis kecil Anda dengan teknologi POS kelas dunia.
                    Kelola inventaris, pantau laporan keuangan secara real-time, dan
                    tingkatkan loyalitas pelanggan dalam satu aplikasi.
                </p>

                {{-- Stats counter --}}
                <div class="grid grid-cols-3 gap-4 py-6" x-data="counterGroup()" x-init="start()">
                    <div>
                        <div class="text-nominal text-primary-900" x-text="counters[0].display"></div>
                        <div class="text-body-sm text-ink-400">UMKM Bergabung</div>
                    </div>
                    <div>
                        <div class="text-nominal text-primary-900" x-text="counters[1].display"></div>
                        <div class="text-body-sm text-ink-400">Kota di Indonesia</div>
                    </div>
                    <div>
                        <div class="text-nominal text-primary-900" x-text="counters[2].display + '%'"></div>
                        <div class="text-body-sm text-ink-400">Kepuasan Pengguna</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 py-4 font-semibold text-white sm:grid-cols-2 text-body-lg">
                    <a href="{{ route('register') }}"
                        class="relative flex items-center justify-center px-4 py-3 overflow-hidden font-bold transition duration-300 rounded-md shadow-md border-accent-500 group bg-accent-700 border-1 text-accent-100 hover:-translate-y-1">
                        <span
                            class="absolute inset-0 transition-transform duration-700 -translate-x-full skew-x-12 bg-white/40 group-hover:translate-x-full"></span>
                        <span class="relative ">Mulai Uji Coba Gratis</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="flex items-center justify-center gap-2 px-4 py-3 transition duration-300 border rounded-md text-accent-500 border-accent-500 hover:-translate-y-1 hover:bg-accent-500/5">
                        <i class="fa-solid fa-circle-play"></i> Jadwalkan Demo
                    </a>
                </div>
            </div>

            {{-- Screenshot Card --}}
            <div class="flex justify-end w-full lg:w-auto" data-aos="fade-left" data-aos-delay="200">
                <div class="relative group">
                    <div
                        class="absolute transition duration-500 rounded-lg rotate-3 -inset-1 bg-gradient-to-r from-primary-900 to-accent-500 blur opacity-10 group-hover:opacity-20">
                    </div>
                    <div
                        class="relative w-full max-w-[700px] p-1.5 bg-surface-0 border border-border-200 rounded-lg shadow-lg overflow-hidden rotate-3 transition duration-500 hover:scale-105 hover:rotate-0">
                        <div
                            class="rounded-md overflow-hidden bg-surface-100 border border-border-200 aspect-[16/9] flex items-center justify-center">
                            <img class="object-cover w-full h-full" src="{{ asset('img/hero-screen.png') }}"
                                alt="Tampilan Aplikasi GrowPOS">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
