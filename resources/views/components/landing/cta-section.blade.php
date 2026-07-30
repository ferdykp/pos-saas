{{--
    CTA Section Component
    Usage: <x-landing.cta-section />
--}}
<section id="us" class="py-24">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div
            data-aos="zoom-in"
            class="relative overflow-hidden text-center bg-brand-light rounded-[3rem] p-10 md:p-20"
        >
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <circle cx="100" cy="0" r="40" fill="white" />
                    <circle cx="0" cy="100" r="30" fill="white" />
                </svg>
            </div>
            <div class="relative z-10">
                <h2 class="mb-6 text-3xl font-extrabold text-white md:text-5xl">
                    Siap Menumbuhkan Bisnis Anda?
                </h2>
                <p class="max-w-xl mx-auto mb-10 text-lg font-medium text-white/80">
                    Bergabunglah dengan ribuan pengusaha Indonesia lainnya yang telah
                    beralih ke kasir digital modern.
                </p>
                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row sm:gap-8">
                    <a href="{{ route('register') }}"
                        class="inline-block w-full sm:w-[280px] px-8 py-4 text-lg font-bold transition transform shadow-2xl text-warning-dark bg-warning rounded-2xl hover:bg-warning/90 hover:scale-105 active:scale-95">
                        Mulai Gratis Sekarang
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-block w-full sm:w-[280px] px-8 py-4 text-lg font-bold text-white transition transform bg-brand-dark shadow-2xl rounded-2xl hover:bg-brand-dark/80 hover:scale-105 active:scale-95">
                        Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
