<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS SaaS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50/50">
    <div class="relative flex flex-col items-center justify-center min-h-screen p-5 overflow-hidden">
        <!-- Modern Blur Glow Background -->
        <div
            class="absolute top-0 left-0 -translate-x-1/3 -translate-y-1/3 bg-gradient-to-br from-blue-400 to-indigo-300 rounded-full opacity-20 w-[30rem] h-[30rem] blur-[80px] pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 right-0 translate-x-1/3 translate-y-1/3 bg-gradient-to-tr from-purple-400 to-pink-300 rounded-full opacity-20 w-[30rem] h-[30rem] blur-[80px] pointer-events-none">
        </div>

        <div class="z-10 w-full max-w-md">
            <!-- Logo Brand -->
            <div class="mb-8 text-center animate-fade-in">
                <a href="/" class="inline-flex items-center space-x-3 group">
                    <div
                        class="flex items-center justify-center transition-transform duration-300 bg-blue-600 shadow-lg w-11 h-11 shadow-blue-500/20 rounded-xl group-hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-900">POS<span
                            class="text-blue-600">SaaS</span></span>
                </a>
            </div>

            <!-- Main Card Container -->
            <div
                class="bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-[2rem] p-7 sm:p-10 transition-all">
                {{ $slot }}
            </div>

            <!-- Footer Copyright -->
            <p class="mt-8 text-xs font-semibold tracking-wider text-center text-gray-400 uppercase">
                &copy; {{ date('Y') }} POSSaaS Solution. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
