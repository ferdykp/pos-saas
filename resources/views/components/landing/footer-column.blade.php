@props(['title', 'links' => []])

<div class="col-span-1 md:col-span-3">
    <div class="mb-4 font-bold text-body-sm text-primary-500">{{ $title }}</div>
    <div class="space-y-3 font-medium text-body-sm text-ink-700">
        @foreach ($links as $link)
            <a href="#" class="block transition hover:text-primary-600 hover:translate-x-1">{{ $link }}</a>
        @endforeach
    </div>
</div>
