@php
    $stylesheet = public_path('build/mail.css');
    if (!is_file($stylesheet)) {
        throw new RuntimeException('Tailwind email assets are missing. Run npm run build before sending email.');
    }
@endphp
{!! file_get_contents($stylesheet) !!}
