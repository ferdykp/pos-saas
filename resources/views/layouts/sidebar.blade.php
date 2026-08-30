<aside
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'lg:w-[260px]': !sidebarCollapsed,
        'lg:w-[72px]': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col h-screen transition-all duration-300 border-r bg-surface-0 border-border-200 lg:translate-x-0 lg:static lg:inset-0 shrink-0">

    <!-- Sidebar Header (Brand & Logo) -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-border-200 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">

            @if (auth()->user()->tenant && auth()->user()->tenant->img_logo)
                <div
                    class="flex items-center justify-center w-10 h-10 overflow-hidden border rounded-md border-border-200 bg-surface-100 shrink-0">
                    <img src="{{ asset('storage/' . auth()->user()->tenant->img_logo) }}"
                        alt="Logo {{ auth()->user()->tenant->name }}" class="object-cover w-full h-full">
                </div>
            @else
                <div
                    class="flex items-center justify-center w-10 h-10 text-base font-bold text-white rounded-md shadow-sm bg-primary-600 font-heading shrink-0">
                    GP
                </div>
            @endif

            <div class="flex flex-col min-w-0 transition-opacity duration-200"
                :class="{ 'lg:hidden': sidebarCollapsed }">
                <span class="text-sm font-bold leading-tight tracking-tight truncate font-heading text-ink-900">
                    {{ auth()->user()->tenant ? auth()->user()->tenant->name : 'GrowPOS' }}
                </span>
                <span class="text-[10px] font-semibold text-primary-600 uppercase tracking-wider mt-0.5">
                    {{ auth()->user()->tenant->business_type ?? 'Teman UMKM' }}
                </span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="p-1 text-ink-400 hover:text-ink-900 lg:hidden">
            <i class="text-lg fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Navigation Body -->
    <nav class="flex-1 min-h-0 px-3 py-4 space-y-6 overflow-y-auto custom-scrollbar">

        <!-- Group: Main Menu -->
        <div>
            <p x-show="!sidebarCollapsed"
                class="px-3 text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-2">
                Utama
            </p>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Dashboard' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-chart-pie"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
                </a>
            </div>
        </div>

        @if (auth()->user()->role === 'admin')
            <!-- Group: SaaS Admin -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-2">
                    SaaS Management
                </p>
                <div class="space-y-1">
                    <a href="{{ route('tenants.index') }}"
                        class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('tenants.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                        :title="sidebarCollapsed ? 'Manajemen Tenant' : ''">
                        <i class="w-5 text-base text-center fa-solid fa-store"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Manajemen Tenant</span>
                    </a>

                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                        :title="sidebarCollapsed ? 'Approval Penarikan' : ''">
                        <i class="w-5 text-base text-center fa-solid fa-money-check-dollar"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Approval Penarikan</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Group: Kasir & Operasional -->
        <div>
            <p x-show="!sidebarCollapsed"
                class="px-3 text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-2">
                Kasir & Transaksi
            </p>
            <div class="space-y-1">
                <a href="{{ route('pos.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('pos.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Kasir POS' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-cash-register"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">POS Terminal</span>
                </a>

                <a href="{{ route('orders.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('orders.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Riwayat Transaksi' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-receipt"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Riwayat Transaksi</span>
                </a>

                {{-- MENU CRM / PELANGGAN (PROTEKSI GATE) --}}
                @can('feature-crm')
                    <a href="{{ route('customers.index') }}"
                        class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('customers.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                        :title="sidebarCollapsed ? 'Pelanggan / CRM' : ''">
                        <i class="w-5 text-base text-center fa-solid fa-users"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Pelanggan / CRM</span>
                    </a>
                @else
                    <a href="{{ route('billing.index') }}"
                        class="flex items-center justify-between px-3 text-xs font-semibold rounded-md h-11 text-ink-400 hover:bg-surface-100 opacity-60"
                        :title="sidebarCollapsed ? 'Pelanggan / CRM (Upgrade Growth)' : ''">
                        <div class="flex items-center gap-3 truncate">
                            <i class="w-5 text-base text-center fa-solid fa-users"></i>
                            <span x-show="!sidebarCollapsed" class="truncate">Pelanggan / CRM</span>
                        </div>
                        <span x-show="!sidebarCollapsed"
                            class="px-1.5 py-0.5 text-[9px] font-bold text-amber-800 bg-amber-100 rounded uppercase">GROWTH</span>
                    </a>
                @endcan

                <a href="{{ route('discounts.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('discounts.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Promo & Diskon' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-tags"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Promo & Diskon</span>
                </a>

                <a href="{{ route('shifts.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('shifts.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Shift Kasir' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-user-clock"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Shift Kasir</span>
                </a>

                <a href="{{ route('employees.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('employees.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Karyawan' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-id-card"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Karyawan</span>
                </a>
            </div>
        </div>

        <!-- Group: Inventori & Bahan -->
        <div>
            <p x-show="!sidebarCollapsed"
                class="px-3 text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-2">
                Produk & Stok
            </p>
            <div class="space-y-1">
                <a href="{{ route('products.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('products.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Produk & Inventory' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-boxes-stacked"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Daftar Produk</span>
                </a>

                <a href="{{ route('categories.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('categories.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Kategori Produk' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-layer-group"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Kategori Produk</span>
                </a>

                <a href="{{ route('materials.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('materials.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Bahan Baku' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-cubes"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Bahan Baku</span>
                </a>

                <a href="{{ route('suppliers.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Supplier' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-truck-field"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Supplier</span>
                </a>
            </div>
        </div>

        <!-- Group: Laporan & AI -->
        <div>
            <p x-show="!sidebarCollapsed"
                class="px-3 text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-2">
                Analitik & AI
            </p>
            <div class="space-y-1">
                <a href="{{ route('reports.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('reports.index') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Laporan Analytics' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-chart-line"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Laporan Penjualan</span>
                </a>

                {{-- MENU ANALITIK AI (PROTEKSI GATE) --}}
                @can('feature-ai-analytics')
                    <a href="{{ route('reports.ai') }}"
                        class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('reports.ai') ? 'bg-accent-100 text-accent-700 font-bold' : 'text-ink-700 hover:bg-accent-100/50 hover:text-accent-700' }}"
                        :title="sidebarCollapsed ? 'Tanya GrowPOS AI' : ''">
                        <i class="w-5 text-base text-center fa-solid fa-wand-magic-sparkles text-accent-500"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Tanya GrowPOS AI</span>
                    </a>
                @else
                    <a href="{{ route('billing.index') }}"
                        class="flex items-center justify-between px-3 text-xs font-semibold rounded-md h-11 text-ink-400 hover:bg-surface-100 opacity-60"
                        :title="sidebarCollapsed ? 'Tanya GrowPOS AI (Upgrade Scale)' : ''">
                        <div class="flex items-center gap-3 truncate">
                            <i class="w-5 text-base text-center text-purple-400 fa-solid fa-wand-magic-sparkles"></i>
                            <span x-show="!sidebarCollapsed" class="truncate">Tanya GrowPOS AI</span>
                        </div>
                        <span x-show="!sidebarCollapsed"
                            class="px-1.5 py-0.5 text-[9px] font-bold text-purple-800 bg-purple-100 rounded uppercase">SCALE</span>
                    </a>
                @endcan

                <a href="{{ route('finance.index') }}"
                    class="flex items-center gap-3 px-3 h-11 text-xs font-semibold rounded-md transition-colors {{ request()->routeIs('finance.*') ? 'bg-primary-50 text-primary-600' : 'text-ink-700 hover:bg-surface-100 hover:text-ink-900' }}"
                    :title="sidebarCollapsed ? 'Keuangan' : ''">
                    <i class="w-5 text-base text-center fa-solid fa-wallet"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Keuangan Toko</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Footer / Store Status Badge -->
    <div class="p-3 border-t border-border-200 bg-surface-100/50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-semantic-success animate-pulse shrink-0"></div>
            <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                <p class="text-[11px] font-semibold text-ink-900 truncate">Sistem Online</p>
                <p class="text-[10px] text-ink-700 truncate">Terhubung ke Cloud</p>
            </div>
        </div>
    </div>
</aside>
