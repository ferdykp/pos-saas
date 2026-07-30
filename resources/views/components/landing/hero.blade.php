{{-- 
    Hero Section Component
    Usage: <x-landing.hero />
--}}
<div class="py-20 overflow-hidden md:py-32 bg-brand-mint/10">
    <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">
        <div data-aos="fade-down" class="inline-block py-6">
            <div class="text-sm rounded-xl tracking-wider text-brand-dark text-center font-semibold px-3 py-1.5 bg-brand-accent/30">
                <i class="mr-1 fa-solid fa-star"></i> All in One Solution
            </div>
        </div>

        <div class="flex flex-col items-center justify-between w-full gap-8 lg:flex-row">
            {{-- Text Content --}}
            <div class="w-full max-w-xl" data-aos="fade-right" data-aos-delay="100">
                <h1 class="py-2 text-4xl font-bold leading-tight md:text-5xl">
                    Solusi Kasir Digital yang
                    <span class="bg-gradient-to-r from-brand to-brand-light bg-clip-text text-transparent">
                        Tumbuh Bersama
                    </span>
                    Bisnis Anda
                </h1>
                <p class="py-2 text-gray-600 text-md">
                    Berdayakan bisnis kecil Anda dengan teknologi POS kelas dunia.
                    Kelola inventaris, pantau laporan keuangan secara real-time, dan
                    tingkatkan loyalitas pelanggan dalam satu aplikasi.
                </p>

                {{-- Stats counter - kesan modern & data-driven --}}
                <div class="grid grid-cols-3 gap-4 py-6" x-data="counterGroup()" x-init="start()">
                    <div>
                        <div class="text-2xl font-extrabold text-brand-dark" x-text="counters[0].display"></div>
                        <div class="text-xs text-gray-500">UMKM Bergabung</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-brand-dark" x-text="counters[1].display"></div>
                        <div class="text-xs text-gray-500">Kota di Indonesia</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-brand-dark" x-text="counters[2].display + '%'"></div>
                        <div class="text-xs text-gray-500">Kepuasan Pengguna</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 py-4 font-semibold text-white sm:grid-cols-2 text-md">
                    <a href="{{ route('register') }}" class="relative flex items-center justify-center px-4 py-3 overflow-hidden transition duration-300 rounded-md shadow-md group bg-warning text-warning-dark hover:-translate-y-1">
                        <span class="absolute inset-0 skew-x-12 -translate-x-full bg-white/20 group-hover:translate-x-full transition-transform duration-700"></span>
                        <span class="relative">Mulai Uji Coba Gratis</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 px-4 py-3 transition duration-300 border rounded-md text-brand-accent border-brand-accent hover:-translate-y-1 hover:bg-brand-accent/5">
                        <i class="fa-solid fa-circle-play"></i> Jadwalkan Demo
                    </a>
                </div>
            </div>

            {{-- Screenshot Card --}}
            <div class="flex justify-end w-full lg:w-auto" data-aos="fade-left" data-aos-delay="200">
                <div class="relative group">
                    <div class="absolute rotate-3 -inset-1 bg-gradient-to-r from-brand-dark to-brand-accent rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-500"></div>
                    <div class="relative w-full max-w-[700px] p-1.5 bg-white border border-gray-100 rounded-[2rem] shadow-2xl overflow-hidden rotate-3 transition duration-500 hover:scale-105 hover:rotate-0">
                        <div class="rounded-[1.5rem] overflow-hidden bg-gray-50 border border-gray-100 aspect-[16/9] flex items-center justify-center">
                            <img class="object-cover w-full h-full" src="{{ asset('img/hero-screen.png') }}" alt="Tampilan Aplikasi GrowPOS">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
