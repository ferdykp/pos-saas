@props([
    'icon',
    'title',
    'desc',
    'image' => null,
    'variant' => 'large',
    'bg' => 'bg-surface-0',
    'iconBg' => 'bg-primary-100',
    'iconColor' => 'text-primary-600',
    'textColor' => 'text-ink-900',
])

<div data-aos="fade-up" @class([
    'rounded-lg p-8 transition-all duration-300',
    'shadow-md border border-border-200 hover:border-primary-500 hover:shadow-lg' =>
        $variant === 'large',
    $bg,
    $textColor,
])>
    @if ($variant === 'large' && $image)
        <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
            <div class="items-center max-w-[450px]">
                <i
                    class="{{ $iconBg }} {{ $iconColor }} text-2xl mb-3 fa-solid {{ $icon }} py-2 px-3 rounded-md inline-block transition-transform duration-300 hover:scale-110"></i>
                <div class="mb-3 text-2xl font-bold">{{ $title }}</div>
                <div class="font-medium text-ink-700 text-body-base">{{ $desc }}</div>
            </div>
            <div class="w-full md:w-auto">
                <img src="{{ asset('img/' . $image) }}"
                    class="w-full md:w-[450px] h-[220px] md:h-[250px] object-cover rounded-lg" alt="{{ $title }}">
            </div>
        </div>
    @else
        <div class="h-[250px]">
            <i
                class="{{ $iconBg }} {{ $iconColor }} text-2xl mb-4 fa-solid {{ $icon }} py-2 px-3 rounded-md inline-block transition-transform duration-300 hover:scale-110"></i>
            <div class="mb-4 text-2xl font-bold">{{ $title }}</div>
            <div class="font-normal text-body-base opacity-90">{{ $desc }}</div>
        </div>
    @endif
</div>
