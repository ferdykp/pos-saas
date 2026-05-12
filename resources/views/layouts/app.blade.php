<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>POS - @yield('title')</title>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        @include('layouts.sidebar')

        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            @include('layouts.navbar')

            <main class="flex-1 overflow-y-auto focus:outline-none bg-gray-50/50 custom-scrollbar">
                <div class="h-full">
                    <!-- Container Notifikasi Floating (Pojok Kanan Atas) -->
                    <div class="fixed top-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-sm">

                        {{-- Pesan Sukses --}}
                        @if (session('success'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="translate-x-full opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-x-0 opacity-100"
                                x-transition:leave-end="translate-x-full opacity-0">
                                <x-auth-session-status
                                    class="flex items-center justify-between px-5 py-4 font-bold text-green-700 bg-white border-l-4 border-green-500 shadow-xl rounded-xl"
                                    :status="session('success')" />
                            </div>
                        @endif

                        {{-- Pesan Error Session --}}
                        @if (session('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="translate-x-full opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-x-0 opacity-100"
                                x-transition:leave-end="translate-x-full opacity-0">
                                <x-input-error :messages="[session('error')]"
                                    class="px-5 py-4 font-bold text-red-700 bg-white border-l-4 border-red-500 shadow-xl rounded-xl" />
                            </div>
                        @endif

                        {{-- Pesan Error Validasi --}}
                        @if ($errors->any())
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="translate-x-full opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-x-0 opacity-100"
                                x-transition:leave-end="translate-x-full opacity-0">
                                <x-input-error :messages="$errors->all()"
                                    class="px-5 py-4 font-bold text-red-700 bg-white border-l-4 border-red-500 shadow-xl rounded-xl" />
                            </div>
                        @endif
                    </div>

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>
