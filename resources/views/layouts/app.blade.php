<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GrowPOS - @yield('title', 'SaaS POS UMKM')</title>

    <!-- 1. PERBAIKAN: CSS x-cloak wajib ditaruh paling atas di head agar langsung menyembunyikan elemen Alpine sebelum JS load -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Google Fonts Google Jakarta Sans, Inter, IBM Plex Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@600&family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome & Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* CSS Overrides untuk Select2 sesuai token GrowPOS */
        .select2-container--default .select2-selection--single {
            border: 1px solid #E3E9E6 !important;
            /* border-200 */
            background-color: #FFFFFF !important;
            height: 44px !important;
            border-radius: 6px !important;
            /* radius-sm */
            display: flex;
            align-items: center;
            padding-left: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1A2421;
        }

        .select2-container--default .select2-selection--single:focus-within {
            border-color: #16805F !important;
            /* primary-600 */
            box-shadow: 0 0 0 3px rgba(31, 161, 121, 0.15) !important;
        }

        .select2-container--default .select2-selection__arrow {
            height: 44px !important;
            right: 8px !important;
        }

        /* Custom Scrollbar halus */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F7F9F8;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #8B9994;
            border-radius: 999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #465550;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-body text-ink-900 bg-surface-100 selection:bg-primary-100 selection:text-primary-700">

    <!-- Offline Mode Banner -->
    <div x-data="{ isOffline: !navigator.onLine }" x-init="window.addEventListener('online', () => isOffline = false);
    window.addEventListener('offline', () => isOffline = true);" x-show="isOffline" x-cloak
        class="fixed top-0 inset-x-0 z-[110] bg-accent-500 text-white text-xs font-semibold py-2 px-4 text-center flex items-center justify-center gap-2 shadow-sm">
        <i class="fa-solid fa-wifi-slash"></i>
        <span>Offline — transaksi tetap tersimpan lokal dan akan disinkronkan saat online kembali.</span>
    </div>

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            <!-- Navbar Header -->
            @include('layouts.navbar')

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto focus:outline-none bg-surface-100 custom-scrollbar">
                <div class="h-full">

                    <!-- Floating Toast Notification System -->
                    <div class="fixed top-20 right-5 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none">

                        {{-- Pesan Sukses --}}
                        @if (session('success'))
                            <!-- 2. PERBAIKAN: Ditambahkan x-cloak -->
                            <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 4000)"
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="translate-x-full opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-x-0 opacity-100"
                                x-transition:leave-end="translate-x-full opacity-0"
                                class="pointer-events-auto flex items-center gap-3 px-4 py-3.5 bg-surface-0 border-l-4 border-primary-600 rounded-lg shadow-md text-ink-900 text-sm font-medium">
                                <i class="text-lg fa-solid fa-circle-check text-primary-600"></i>
                                <div class="flex-1">{{ session('success') }}</div>
                                <button @click="show = false" class="text-ink-400 hover:text-ink-700">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Pesan Error Session --}}
                        @if (session('error'))
                            <!-- 2. PERBAIKAN: Ditambahkan x-cloak -->
                            <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 5000)"
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="translate-x-full opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-x-0 opacity-100"
                                x-transition:leave-end="translate-x-full opacity-0"
                                class="pointer-events-auto flex items-center gap-3 px-4 py-3.5 bg-surface-0 border-l-4 border-semantic-danger rounded-lg shadow-md text-ink-900 text-sm font-medium">
                                <i class="text-lg fa-solid fa-circle-exclamation text-semantic-danger"></i>
                                <div class="flex-1">{{ session('error') }}</div>
                                <button @click="show = false" class="text-ink-400 hover:text-ink-700">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Pesan Error Validasi --}}
                        @if ($errors->any())
                            <!-- 2. PERBAIKAN: Ditambahkan x-cloak -->
                            <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 6000)"
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="translate-x-full opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-x-0 opacity-100"
                                x-transition:leave-end="translate-x-full opacity-0"
                                class="p-4 text-sm border-l-4 rounded-lg shadow-md pointer-events-auto bg-surface-0 border-semantic-danger text-ink-900">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-semantic-danger">Ada kendala input:</span>
                                    <button @click="show = false" class="text-ink-400 hover:text-ink-700">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <ul class="list-disc list-inside space-y-0.5 text-xs text-ink-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{ $slot ?? ($slot ?? '') }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
