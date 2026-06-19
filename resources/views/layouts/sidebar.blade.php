<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex flex-col h-screen transition-transform duration-300 transform bg-white border-r border-gray-100 w-72 lg:translate-x-0 lg:static lg:inset-0">


    <div class="flex items-center justify-between h-20 px-8 border-b border-gray-50 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">

            @if (auth()->user()->tenant && auth()->user()->tenant->img_logo)
                <div
                    class="flex items-center justify-center w-10 h-10 overflow-hidden transition-transform border border-gray-100 shadow-md rounded-xl bg-gray-50 group-hover:scale-105">
                    <img src="{{ asset('storage/' . auth()->user()->tenant->img_logo) }}"
                        alt="Logo {{ auth()->user()->tenant->name }}" class="object-cover w-full h-full">
                </div>
            @else
                <div
                    class="flex items-center justify-center w-10 h-10 text-sm font-black tracking-wider text-white uppercase transition-transform bg-blue-600 shadow-lg rounded-xl shadow-blue-200 group-hover:scale-105">
                    {{ auth()->user()->tenant ? substr(auth()->user()->tenant->name, 0, 2) : 'PS' }}
                </div>
            @endif

            <div class="flex flex-col">
                <span class="text-base font-black tracking-tight text-gray-900 leading-tight truncate max-w-[150px]">
                    {{ auth()->user()->tenant ? auth()->user()->tenant->name : 'POS' }}
                </span>
                @if (auth()->user()->tenant)
                    <span class="text-[9px] font-black tracking-widest text-blue-600 uppercase mt-0.5">
                        {{ auth()->user()->tenant->business_type ?? 'SaaS Tenant' }}
                    </span>
                @else
                    <span class="text-[9px] font-black tracking-widest text-gray-400 uppercase mt-0.5">Superadmin</span>
                @endif
            </div>
        </a>

        <button @click="sidebarOpen = false" class="text-gray-500 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 min-h-0 p-6 space-y-8 overflow-y-auto">
        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Main Menu</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
                    Dashboard
                </x-sidebar-link>
            </div>
        </div>

        @if (auth()->user()->role === 'admin')
            <div x-data="{ openTenantMenu: {{ request()->routeIs('tenants.*') ? 'true' : 'false' }} }">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">SaaS Management</p>
                <div class="space-y-1">
                    <button @click="openTenantMenu = !openTenantMenu"
                        class="flex items-center justify-between w-full px-4 py-3 text-sm font-semibold text-gray-600 transition-all rounded-xl hover:bg-gray-50 hover:text-gray-900 group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-600"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Manajemen Tenant</span>
                        </div>
                        <svg :class="openTenantMenu ? 'rotate-180' : ''"
                            class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openTenantMenu" x-cloak x-collapse class="mt-1 space-y-1 pl-11">
                        <a href="{{ route('tenants.index') }}"
                            class="block py-2 text-sm font-medium {{ request()->routeIs('tenants.index') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-900' }}">
                            • Daftar Semua Tenant
                        </a>
                        <a href="{{ route('tenants.create') }}"
                            class="block py-2 text-sm font-medium {{ request()->routeIs('tenants.create') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-900' }}">
                            • Tambah Tenant Baru
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Customer & Staff</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.index')" icon="customer">
                    Customer
                </x-sidebar-link>
                <x-sidebar-link :href="route('pos.index')" :active="request()->routeIs('pos.index')" icon="customer">
                    POS Kasir
                </x-sidebar-link>
                <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.index')" icon="customer">
                    Order / Riwayat Transaksi
                </x-sidebar-link>
                <x-sidebar-link :href="route('discounts.index')" :active="request()->routeIs('discounts.index')" icon="customer">
                    Discounts
                </x-sidebar-link>
                <x-sidebar-link :href="route('shifts.index')" :active="request()->routeIs('shifts.index')" icon="customer">
                    Shifts Kasir
                </x-sidebar-link>
                <x-sidebar-link :href="route('employees.index')" :active="request()->routeIs('employees.index')" icon="customer">
                    Employees / Karyawan
                </x-sidebar-link>
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
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Report Management</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.index')" icon="box">
                    Report</x-sidebar-link>

                <x-sidebar-link :href="route('reports.ai')" :active="request()->routeIs('reports.ai')" icon="box">
                    AI Analysis
                </x-sidebar-link>
            </div>
        </div>
        <div>
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Finance Management</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('finance.index')" :active="request()->routeIs('finance.index')" icon="box">
                    Finance</x-sidebar-link>
            </div>
        </div>
    </nav>

    <div class="p-6 border-t border-gray-50 bg-gray-50/50 shrink-0">
        <div class="flex items-center space-x-3">
            <div
                class="flex items-center justify-center w-10 h-10 font-bold text-blue-600 uppercase bg-blue-100 border-2 border-white rounded-full shadow-sm">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] font-bold text-blue-600 uppercase">
                    {{ auth()->user()->tenant->name ?? 'Mulai Bisnis' }}</p>
            </div>
        </div>
    </div>
</aside>
