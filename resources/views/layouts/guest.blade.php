<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS SaaS') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome (Optional, pastikan memaintain lisensi jika perlu) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

{{-- Pastikan class h-full diterapkan pada html dan body agar min-h-screen di child bekerja sempurna --}}

<body class="h-full font-sans antialiased text-gray-900">

    {{-- Slot konten akan merender grid full screen dari login.blade.php --}}
    {{ $slot }}

</body>

</html>
