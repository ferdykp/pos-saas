@props(['active' => false, 'icon' => 'circle'])

<a {{ $attributes }}
    class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl transition-all duration-200 group {{ $active ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
    <span class="flex-shrink-0">
        <svg class="w-5 h-5 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18H12" />
        </svg>
    </span>
    <span class="text-sm font-bold tracking-tight">{{ $slot }}</span>
</a>
