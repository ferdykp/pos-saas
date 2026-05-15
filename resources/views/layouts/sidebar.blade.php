<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 transition-transform duration-300 transform bg-white border-r border-gray-100 w-72 lg:translate-x-0 lg:static lg:inset-0">

    <div class="flex items-center justify-between h-20 px-8 border-b border-gray-50">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="flex items-center justify-center bg-blue-600 shadow-lg w-9 h-9 rounded-xl shadow-blue-200">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-xl font-black tracking-tighter text-gray-900 uppercase">POS<span
                    class="text-blue-600">SaaS</span></span>
        </a>
        <button @click="sidebarOpen = false" class="text-gray-500 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
            </svg>
        </button>
    </div>

    <nav class="p-6 space-y-8 overflow-y-auto">
        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Main Menu</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
                    Dashboard
                </x-sidebar-link>
                {{-- <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" icon="shopping-cart">
                    Penjualan (POS)
                </x-sidebar-link> --}}
            </div>
        </div>
        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Customer Management</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.index')" icon="customer">
                    Customer
                </x-sidebar-link>
                <x-sidebar-link :href="route('pos.index')" :active="request()->routeIs('pos.index')" icon="customer">
                    POS Kasir
                </x-sidebar-link>
                <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.index')" icon="customer">
                    Order
                </x-sidebar-link>
                <x-sidebar-link :href="route('discounts.index')" :active="request()->routeIs('discounts.index')" icon="customer">
                    Discounts
                </x-sidebar-link>

                {{-- <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" icon="shopping-cart">
                    Penjualan (POS)
                </x-sidebar-link> --}}
            </div>
        </div>

        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Inventory Management</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" icon="box">
                    Daftar Kategori</x-sidebar-link>
                <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="box">
                    Daftar Produk</x-sidebar-link>
                <x-sidebar-link :href="route('materials.index')" :active="request()->routeIs('materials.*')" icon="box">
                    Daftar Bahan Baku</x-sidebar-link>
                <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="box">
                    Supplier</x-sidebar-link>


            </div>
        </div>

        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Analytic</p>
            <div class="space-y-1">
                {{-- <x-sidebar-link href="#" icon="chart-bar">Laporan Laba</x-sidebar-link>
                <x-sidebar-link href="#" icon="users">Pelanggan</x-sidebar-link> --}}
            </div>
        </div>
    </nav>

    <div class="absolute bottom-0 w-full p-6 border-t border-gray-50 bg-gray-50/30">
        <div class="flex items-center space-x-3">
            <div
                class="flex items-center justify-center w-10 h-10 font-bold text-blue-600 bg-blue-100 border-2 border-white rounded-full shadow-sm">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] font-bold text-blue-600 uppercase">{{ auth()->user()->tenant->name ?? 'Owner' }}
                </p>
            </div>
        </div>
    </div>
</aside>
