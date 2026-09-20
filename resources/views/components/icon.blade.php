@php
    $tokens = preg_split('/\s+/', trim($attributes->get('class', '')));
    $family = in_array('fa-brands', $tokens) ? 'brands' : (in_array('fa-regular', $tokens) ? 'regular' : 'solid');
    $name = collect($tokens)->first(fn ($token) => str_starts_with($token, 'fa-') && !in_array($token, ['fa-solid', 'fa-regular', 'fa-brands', 'fa-spin']));
    $icons = app('growpos.icons');
    $key = substr($name ?? 'fa-circle', 3);
    $icon = $icons[$family.':'.$key] ?? $icons['solid:'.$key] ?? $icons['solid:circle'];
    $classes = implode(' ', array_filter($tokens, fn ($token) => !str_starts_with($token, 'fa-')));
    if (in_array('fa-spin', $tokens)) $classes .= ' motion-safe:animate-spin';
@endphp
<svg {{ $attributes->except('class')->merge(['class' => 'inline-block box-content h-[1em] w-[1em] shrink-0 align-[-0.125em] '.$classes]) }} viewBox="0 0 {{ $icon['width'] }} {{ $icon['height'] }}" fill="currentColor" aria-hidden="true" focusable="false">
    @foreach($icon['paths'] as $path)<path d="{{ $path }}" />@endforeach
</svg>
