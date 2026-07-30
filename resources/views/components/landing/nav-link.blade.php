{{-- 
    Nav Link Component
    Usage: <x-landing.nav-link href="#fitur">Fitur</x-landing.nav-link>
--}}
@props(['href'])

<a href="{{ $href }}" class="relative inline-block transition duration-300 group hover:text-brand-dark">
    <span>{{ $slot }}</span>
    <span class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-brand"></span>
</a>
