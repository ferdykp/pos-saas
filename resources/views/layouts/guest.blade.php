<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS SaaS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="relative flex flex-col items-center justify-center min-h-screen p-4 overflow-hidden">
        <div
            class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 bg-blue-100 rounded-full opacity-50 w-96 h-96 blur-3xl">
        </div>
        <div
            class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 bg-indigo-100 rounded-full opacity-50 w-96 h-96 blur-3xl">
        </div>

        <div class="z-10 w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="/" class="inline-flex items-center space-x-3">
                    <div
                        class="flex items-center justify-center w-12 h-12 bg-blue-600 shadow-xl rounded-2xl shadow-blue-200">
                        <svg class="text-white w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tighter text-gray-900">POS<span
                            class="text-blue-600">SaaS</span></span>
                </a>
            </div>

            <div
                class="bg-white/80 backdrop-blur-xl border border-white shadow-2xl shadow-gray-200/50 rounded-[2.5rem] p-8 md:p-10">
                {{ $slot }}
            </div>

            <p class="mt-8 text-sm font-medium text-center text-gray-400">
                &copy; {{ date('Y') }} POSSaaS Solution. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
