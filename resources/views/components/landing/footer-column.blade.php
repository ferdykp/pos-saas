{{--
    Footer Column Component
    Usage: <x-landing.footer-column title="Produk" :links="['Fitur POS', 'Inventaris']" />
--}}
@props(['title', 'links' => []])

<div class="col-span-1 md:col-span-3">
    <div class="mb-4 text-sm font-bold text-brand-light">{{ $title }}</div>
    <div class="space-y-3 text-sm font-medium text-gray-700">
        @foreach ($links as $link)
            <a href="#" class="block transition hover:text-brand hover:translate-x-1">{{ $link }}</a>
        @endforeach
    </div>
</div>
