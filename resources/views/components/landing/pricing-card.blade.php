{{--
    Pricing Card Component
    Usage:
    <x-landing.pricing-card
        title="Growth"
        desc="Untuk toko yang mulai berkembang pesat."
        price="Rp 149rb"
        period="bulan"
        cta="Coba 14 Hari Gratis"
        :popular="true"
        :features="[
            'Transaksi Tanpa Batas' => true,
            'Manajemen Stok Lanjut' => true,
            'CRM & Loyalitas' => true,
            'Support 24/7 Chat' => true,
        ]" />
--}}
@props(['title', 'desc', 'price', 'period', 'cta', 'features' => [], 'popular' => false])

<div data-aos="fade-up" @class([
    'bg-white rounded-3xl p-8 transition duration-300 hover:-translate-y-3 flex flex-col justify-between',
    'border-2 py-14 border-brand shadow-xl relative scale-100 md:scale-105' => $popular,
    'border border-gray-200 shadow-sm' => !$popular,
])>
    @if ($popular)
        <div
            class="absolute -top-4 left-1/2 -translate-x-1/2 bg-brand text-white text-xs font-bold px-4 py-1.5 rounded-full tracking-wider uppercase whitespace-nowrap">
            Paling Populer
        </div>
    @endif

    <div class="space-y-4">
        <h3 class="text-2xl font-bold text-gray-900">{{ $title }}</h3>
        <p class="text-sm leading-relaxed text-gray-600">{{ $desc }}</p>
        <div class="flex items-baseline gap-1 text-xl font-bold text-brand">
            <span class="text-2xl">{{ $price }}</span>
            <span class="text-sm font-normal text-gray-500">/{{ $period }}</span>
        </div>
        <ul class="pt-2 space-y-3 text-sm text-gray-700">
            @foreach ($features as $feature => $included)
                <li class="flex items-center gap-3 {{ !$included ? 'text-gray-400' : '' }}">
                    <i
                        class="text-lg fa-regular {{ $included ? 'fa-circle-check text-brand' : 'fa-circle-xmark' }}"></i>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <button @class([
        'w-full py-3 rounded-xl font-bold text-sm transition mt-6',
        'bg-brand text-white hover:bg-brand-dark shadow-md' => $popular,
        'border-2 border-brand text-brand hover:bg-brand hover:text-white' => !$popular,
    ])>
        {{ $cta }}
    </button>
</div>
