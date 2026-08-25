@props([
    'planId' => null,
    'title',
    'desc',
    'price',
    'period',
    'cta',
    'features' => [],
    'popular' => false,
    'actionUrl' => null,
])

<div data-aos="fade-up" @class([
    'bg-surface-0 rounded-lg p-8 transition duration-300 hover:-translate-y-3 flex flex-col justify-between',
    'border-2 py-14 border-primary-600 shadow-lg relative scale-100 md:scale-105' => $popular,
    'border border-border-200 shadow-sm' => !$popular,
])>
    @if ($popular)
        <div
            class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary-600 text-white text-body-sm font-bold px-4 py-1.5 rounded-full tracking-wider uppercase whitespace-nowrap">
            Paling Populer
        </div>
    @endif

    <div class="space-y-4">
        <h3 class="text-2xl font-bold text-ink-900">{{ $title }}</h3>
        <p class="leading-relaxed text-body-sm text-ink-700">{{ $desc }}</p>
        <div class="flex items-baseline gap-1 font-bold text-primary-600">
            <span class="text-h2">{{ $price }}</span>
            <span class="font-normal text-body-sm text-ink-400">/{{ $period }}</span>
        </div>
        <ul class="pt-2 space-y-3 text-body-sm text-ink-700">
            @foreach ($features as $feature => $included)
                <li class="flex items-center gap-3 {{ !$included ? 'text-ink-400' : '' }}">
                    <i
                        class="text-lg fa-regular {{ $included ? 'fa-circle-check text-primary-600' : 'fa-circle-xmark' }}"></i>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    @auth
        @if ($planId)
            {{-- Form Submit Checkout Paket jika User Sudah Login --}}
            <form action="{{ route('billing.subscribe') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $planId }}">
                <button type="submit" @class([
                    'w-full py-3 rounded-md font-bold text-body-sm transition',
                    'bg-primary-600 text-white hover:bg-primary-900 shadow-md' => $popular,
                    'border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white' => !$popular,
                ])>
                    {{ $cta }}
                </button>
            </form>
        @else
            <a href="{{ route('billing.index') }}" @class([
                'w-full py-3 rounded-md font-bold text-body-sm transition text-center block mt-6',
                'bg-primary-600 text-white hover:bg-primary-900 shadow-md' => $popular,
                'border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white' => !$popular,
            ])>
                {{ $cta }}
            </a>
        @endif
    @else
        {{-- Redirect ke Halaman Register/Login jika Belum Auth --}}
        <a href="{{ route('login') }}" @class([
            'w-full py-3 rounded-md font-bold text-body-sm transition text-center block mt-6',
            'bg-primary-600 text-white hover:bg-primary-900 shadow-md' => $popular,
            'border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white' => !$popular,
        ])>
            {{ $cta }}
        </a>
    @endauth
</div>
