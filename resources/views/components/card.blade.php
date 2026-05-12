<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200']) }}>
    @if (isset($title))
        <div class="px-6 py-4 font-bold text-gray-800 border-b border-gray-100">
            {{ $title }}
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
