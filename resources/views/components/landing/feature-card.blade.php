{{--
    Feature Card Component
    Usage:
    <x-landing.feature-card
        icon="fa-box-archive"
        title="Manajemen Inventaris Pintar"
        desc="Update stok otomatis..."
        image="warehouse.png"
        variant="large" />

    Variants: "large" (white, span-8), "solid" (colored bg, span-4)
--}}
@props([
    'icon',
    'title',
    'desc',
    'image' => null,
    'variant' => 'large',
    'bg' => 'bg-white',
    'iconBg' => 'bg-brand/10',
    'iconColor' => 'text-brand',
    'textColor' => 'text-gray-900',
])

<div data-aos="fade-up" @class([
    'rounded-xl p-8 transition-all duration-300',
    'shadow-md border hover:border-brand-light hover:shadow-xl' =>
        $variant === 'large',
    $bg,
    $textColor,
])>
    @if ($variant === 'large' && $image)
        <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
            <div class="items-center max-w-[450px]">
                <i
                    class="{{ $iconBg }} {{ $iconColor }} text-2xl mb-3 fa-solid {{ $icon }} py-2 px-3 rounded-xl inline-block transition-transform duration-300 hover:scale-110"></i>
                <div class="mb-3 text-2xl font-bold">{{ $title }}</div>
                <div class="font-medium text-gray-600 text-md">{{ $desc }}</div>
            </div>
            <div class="w-full md:w-auto">
                <img src="{{ asset('img/' . $image) }}"
                    class="w-full md:w-[450px] h-[220px] md:h-[250px] object-cover rounded-xl" alt="{{ $title }}">
            </div>
        </div>
    @else
        <div class="h-[250px]">
            <i
                class="{{ $iconBg }} {{ $iconColor }} text-2xl mb-4 fa-solid {{ $icon }} py-2 px-3 rounded-xl inline-block transition-transform duration-300 hover:scale-110 "></i>
            <div class="mb-4 text-2xl font-bold">{{ $title }}</div>
            <div class="font-normal text-md opacity-90">{{ $desc }}</div>
        </div>
    @endif
</div>
