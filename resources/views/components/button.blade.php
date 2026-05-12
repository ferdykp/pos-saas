@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $classes = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800',
    ][$variant];
@endphp

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-4 py-2 rounded-lg font-medium transition duration-150 $classes"]) }}>
    {{ $slot }}
</button>
