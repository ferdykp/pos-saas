<x-app-layout>
    @section('title', 'POS Kasir Terminal')

    <!-- POS Main Wrapper with Alpine Cart Drawer state for Mobile -->
    <div class="flex flex-col lg:flex-row h-[calc(100vh-64px)] overflow-hidden bg-surface-100" x-data="{ mobileCartOpen: false }">

        <!-- ==================== LEFT AREA: PRODUCT CATALOG ==================== -->
        <div class="flex flex-col flex-1 h-full min-w-0 p-4 overflow-hidden md:p-6">

            <!-- Filter & Search Action Bar -->
            <div class="flex flex-col items-stretch justify-between gap-3 mb-5 sm:flex-row sm:items-center shrink-0">

                <!-- Search Product Input Field (Height 44px, radius-sm) -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-400">
                        <i class="text-xs fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" id="searchProduct" onkeyup="filterProducts()"
                        placeholder="Cari nama produk, SKU, atau scan barcode..."
                        class="w-full pr-4 text-xs transition-all border rounded-sm outline-none h-11 pl-9 font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100">
                </div>

                <!-- Category Filter Dropdown -->
                <div class="relative shrink-0" x-data="{ openCategory: false }">
                    <button @click="openCategory = !openCategory" @click.away="openCategory = false"
                        class="flex items-center justify-between w-full gap-3 px-4 text-xs font-semibold transition-colors border rounded-sm sm:w-auto h-11 bg-surface-0 border-border-200 text-ink-900 hover:border-primary-600">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-primary-600"></i>
                            <span id="activeCategoryName">Semua Kategori</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-ink-400 transition-transform"
                            :class="{ 'rotate-180': openCategory }"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="openCategory" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 z-40 mt-1.5 w-56 bg-surface-0 border border-border-200 rounded-md shadow-lg p-1 max-h-64 overflow-y-auto custom-scrollbar">
                        <button onclick="filterCategory('all', 'Semua Kategori')" @click="openCategory = false"
                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-ink-700 hover:bg-primary-50 hover:text-primary-600 rounded-md transition-colors text-left">
                            <i class="w-4 fa-solid fa-border-all text-ink-400"></i>
                            <span>Semua Kategori</span>
                        </button>
                        @foreach ($categories as $cat)
                            <button onclick="filterCategory('{{ $cat->name }}', '{{ $cat->name }}')"
                                @click="openCategory = false"
                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-ink-700 hover:bg-primary-50 hover:text-primary-600 rounded-md transition-colors text-left">
                                <i class="w-4 fa-solid fa-tag text-ink-400"></i>
                                <span>{{ $cat->name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div id="productGrid"
                class="grid flex-1 grid-cols-2 gap-3 pb-24 pr-1 overflow-y-auto custom-scrollbar sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:gap-4 lg:pb-6">
                @foreach ($products as $p)
                    <div onclick="addToCart({{ $p->id }}, '{{ addslashes($p->product_name) }}', {{ $p->sell_price }}, {{ $p->final_price }}, {{ $p->discount_applied }}, '{{ addslashes($p->discount_name ?? '') }}')"
                        data-category="{{ $p->category ? $p->category->name : '' }}"
                        data-name="{{ strtolower($p->product_name) }}"
                        class="product-item group relative bg-surface-0 border border-border-200 rounded-lg shadow-sm hover:shadow-md hover:border-primary-600 cursor-pointer transition-all flex flex-col justify-between overflow-hidden p-2.5 aspect-[1/1.1]">

                        @if ($p->discount_applied > 0)
                            <span
                                class="absolute top-2 right-2 z-10 inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-accent-500 text-white shadow-sm">
                                {{ $p->discount_name }}
                            </span>
                        @endif

                        <div
                            class="h-[68%] w-full rounded-md bg-surface-100 flex items-center justify-center overflow-hidden mb-2 relative">
                            @if ($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->product_name }}"
                                    class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">
                            @else
                                <i class="text-2xl fa-solid fa-box text-ink-400 opacity-40"></i>
                            @endif
                        </div>

                        <div class="flex flex-col justify-between flex-1 min-w-0">
                            <h4
                                class="text-xs font-semibold truncate transition-colors font-body text-ink-900 group-hover:text-primary-600">
                                {{ $p->product_name }}
                            </h4>

                            <div class="flex items-center justify-between mt-1">
                                <div class="flex flex-col">
                                    @if ($p->discount_applied > 0)
                                        <span class="font-mono text-[10px] text-ink-400 line-through leading-none">
                                            Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                                        </span>
                                        <span
                                            class="font-mono text-xs font-semibold leading-tight text-semantic-danger">
                                            Rp {{ number_format($p->final_price, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="font-mono text-xs font-semibold leading-tight text-primary-600">
                                            Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <span class="text-[10px] font-medium text-ink-400">
                                    @if (($p->type ?? 'product') === 'service')
                                        Jasa
                                    @elseif (empty($p->manage_stock))
                                        Stok: ∞
                                    @else
                                        Stok: {{ $p->stock }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ==================== MOBILE FLOATING SUMMARY BOTTOM SHEET BAR ==================== -->
        <div
            class="fixed inset-x-0 bottom-0 z-30 flex items-center justify-between p-3 border-t shadow-lg lg:hidden bg-surface-0 border-border-200">
            <div class="flex flex-col">
                <span class="text-[11px] font-semibold text-ink-400 uppercase tracking-wider">Total Pesanan</span>
                <span id="mobileTotalText" class="font-mono text-base font-bold text-primary-600">Rp 0</span>
            </div>

            <button @click="mobileCartOpen = true"
                class="inline-flex items-center gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 font-body">
                <i class="fa-solid fa-basket-shopping"></i>
                <span>Lihat Cart (<span id="mobileItemCount">0</span>)</span>
            </button>
        </div>

        <!-- ==================== RIGHT PANEL: CART & CHECKOUT ==================== -->
        <div :class="mobileCartOpen ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'"
            class="fixed lg:static inset-0 z-40 lg:z-0 w-full lg:w-[380px] xl:w-[420px] bg-surface-0 border-l border-border-200 flex flex-col h-full shadow-lg lg:shadow-none transition-transform duration-300">

            <!-- Cart Header & Shift Controls -->
            <div class="p-4 border-b border-border-200 shrink-0 bg-surface-100/50">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <button @click="mobileCartOpen = false" class="lg:hidden p-1.5 text-ink-400 hover:text-ink-900">
                            <i class="text-lg fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-base font-bold font-heading text-ink-900">Daftar Pesanan</h3>
                    </div>

                    <!-- Shift & Customer Actions -->
                    <div class="flex items-center gap-2">
                        @if (!$hasShift)
                            <button type="button" onclick="openOpenShiftModal()"
                                class="inline-flex items-center gap-1.5 h-8 px-3 text-[11px] font-semibold text-primary-700 bg-primary-100 hover:bg-primary-600 hover:text-white rounded-md transition-colors">
                                <i class="fa-solid fa-cash-register"></i> Buka Shift
                            </button>
                        @else
                            <button type="button" onclick="openCloseShiftModal()"
                                class="inline-flex items-center gap-1.5 h-8 px-3 text-[11px] font-semibold text-semantic-danger bg-red-50 hover:bg-semantic-danger hover:text-white rounded-md transition-colors">
                                <i class="fa-solid fa-power-off"></i> Tutup Shift
                            </button>
                        @endif

                        <button type="button" onclick="openCustomerModal()"
                            class="flex items-center justify-center w-8 h-8 transition-colors rounded-md text-primary-600 bg-primary-50 hover:bg-primary-100"
                            title="Tambah Pelanggan Baru">
                            <i class="text-xs fa-solid fa-user-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Customer Selection Field -->
                <div class="relative">
                    <select id="customerSelect" class="w-full">
                        <option value="guest">Pelanggan Umum (Guest)</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->name }} {{ $c->phone ? '(' . $c->phone . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cart Items Scrollable List -->
            <div id="cartItems" class="flex-1 p-4 space-y-2.5 overflow-y-auto custom-scrollbar">
                <div class="flex flex-col items-center justify-center py-16 text-center text-ink-400">
                    <i class="mb-2 text-3xl opacity-50 fa-solid fa-cart-flatbed"></i>
                    <p class="text-xs font-semibold font-body">Keranjang masih kosong</p>
                    <p class="font-body text-[11px] text-ink-400 mt-0.5">Pilih produk di sebelah kiri untuk ditambahkan.
                    </p>
                </div>
            </div>

            <!-- Cart Calculation Summary & Checkout Action -->
            <div class="p-4 border-t bg-surface-100 border-border-200 shrink-0">
                <div class="mb-4 space-y-2">
                    <div class="flex justify-between text-xs font-medium text-ink-700">
                        <span>Subtotal Item</span>
                        <span id="subtotalText" class="font-mono font-semibold text-ink-900">Rp 0</span>
                    </div>

                    <div class="flex justify-between text-xs font-medium text-semantic-danger">
                        <span>Potongan Diskon</span>
                        <span id="discountText" class="font-mono font-semibold">-Rp 0</span>
                    </div>

                    @if (($settings['tax_active'] ?? '0') == '1')
                        <div id="taxRow" class="flex justify-between text-xs font-medium text-ink-700">
                            <span>Pajak Outlet ({{ $settings['tax_percentage'] ?? 0 }}%)</span>
                            <span id="taxText" class="font-mono font-semibold">+Rp 0</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-2 border-t border-border-200">
                        <span class="text-sm font-bold font-heading text-ink-900">Total Tagihan</span>
                        <span id="totalText" class="font-mono text-lg font-bold text-primary-600">Rp 0</span>
                    </div>
                </div>

                <button onclick="openPaymentModal()"
                    class="flex items-center justify-center w-full gap-2 text-xs font-semibold text-white transition-all rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 active:bg-primary-900 font-body md:text-sm">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>PROSES PEMBAYARAN</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODALS SECTION ==================== -->

    <!-- Modal 1: Buka Shift Kasir -->
    <div id="openShiftModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-ink-900/40 backdrop-blur-sm">
        <div class="w-full p-6 text-center border rounded-lg shadow-lg bg-surface-0 max-w-modal-sm border-border-200">
            <div
                class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-full bg-primary-50 text-primary-600">
                <i class="text-xl fa-solid fa-cash-register"></i>
            </div>
            <h3 class="mb-1 text-lg font-semibold font-heading text-ink-900">Buka Shift Kasir</h3>
            <p class="mb-5 text-xs font-body text-ink-700">Masukkan modal tunai awal di dalam laci kasir sebelum
                memulai transaksi.</p>

            <form id="openShiftForm" class="space-y-4 text-left">
                <div>
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Uang Modal Awal
                        (Cash)</label>
                    <input type="text" id="input_cash_start" oninput="formatCurrencyInput(this)" required
                        class="w-full px-3 font-mono text-sm font-bold text-center border rounded-sm outline-none h-11 text-ink-900 bg-surface-100 border-border-200 focus:border-primary-600"
                        placeholder="Rp 0">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')"
                        class="flex-1 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 font-body">
                        Mulai Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Tutup Shift Kasir -->
    <div id="closeShiftModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-ink-900/40 backdrop-blur-sm">
        <div class="w-full p-6 border rounded-lg shadow-lg bg-surface-0 max-w-modal-sm border-border-200">
            <h3 class="mb-1 text-lg font-semibold text-center font-heading text-ink-900">Tutup Shift Kasir</h3>
            <p class="mb-4 text-xs text-center font-body text-ink-700">Hitung saldo fisik kasir dan sesuaikan dengan
                laporan sistem.</p>

            <div class="p-3 mb-4 space-y-2 text-xs rounded-md bg-surface-100 font-body">
                <div class="flex justify-between text-ink-700">
                    <span>Modal Awal:</span>
                    <span id="text_shift_start" class="font-mono font-semibold text-ink-900">Rp 0</span>
                </div>
                <div class="flex justify-between text-ink-700">
                    <span>Penjualan Tunai:</span>
                    <span id="text_shift_sales" class="font-mono font-semibold text-primary-600">+Rp 0</span>
                </div>
                <div class="flex justify-between pt-2 font-semibold border-t border-border-200 text-ink-900">
                    <span>Ekspektasi Kasir:</span>
                    <span id="text_shift_expected" class="font-mono text-primary-700">Rp 0</span>
                </div>
            </div>

            <form id="closeShiftForm" class="space-y-4">
                <div>
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Total Uang Fisik Kasir
                        (Cash)</label>
                    <input type="text" id="input_cash_actual" oninput="formatCurrencyInput(this)" required
                        class="w-full px-3 font-mono text-sm font-bold border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600"
                        placeholder="Masukkan hitungan uang fisik...">
                </div>
                <div>
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Catatan Kasir
                        (Optional)</label>
                    <textarea id="shift_notes" rows="2"
                        class="w-full p-2.5 text-xs font-body text-ink-900 bg-surface-0 border border-border-200 rounded-sm focus:border-primary-600 outline-none"
                        placeholder="Keterangan selisih atau catatan operasional..."></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button"
                        onclick="document.getElementById('closeShiftModal').classList.add('hidden')"
                        class="flex-1 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-semantic-danger hover:bg-red-700 font-body">
                        Konfirmasi Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Tambah Pelanggan Baru -->
    <div id="customerModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-ink-900/40 backdrop-blur-sm">
        <div class="w-full p-6 border rounded-lg shadow-lg bg-surface-0 max-w-modal-sm border-border-200">
            <h3 class="mb-4 text-lg font-semibold font-heading text-ink-900">Tambah Pelanggan Baru</h3>

            <form id="addCustomerForm" class="space-y-4">
                <div>
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="cust_name" required
                        class="w-full px-3 text-xs border rounded-sm outline-none h-11 font-body text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600"
                        placeholder="Contoh: Budi Santoso">
                </div>
                <div>
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">No. WhatsApp / HP</label>
                    <input type="text" id="cust_phone"
                        class="w-full px-3 text-xs border rounded-sm outline-none h-11 font-body text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600"
                        placeholder="0812xxxx">
                </div>
                <div class="flex items-center gap-2.5 p-3 bg-primary-50 rounded-md">
                    <input type="checkbox" id="cust_member" value="1"
                        class="w-4 h-4 rounded text-primary-600 border-border-200 focus:ring-primary-600">
                    <label for="cust_member" class="text-xs font-semibold font-body text-primary-700">Daftarkan
                        sebagai Member Toko</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeCustomerModal()"
                        class="flex-1 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 font-body">
                        Simpan Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Payment Confirmation -->
    <div id="paymentModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-ink-900/40 backdrop-blur-sm">
        <div
            class="bg-surface-0 rounded-lg p-6 w-full max-w-modal-lg shadow-lg border border-border-200 overflow-y-auto max-h-[90vh] custom-scrollbar">
            <h3 class="pb-2 mb-4 text-xl font-bold border-b font-heading text-ink-900 border-border-200">
                Konfirmasi Pembayaran
            </h3>

            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                <div class="p-4 space-y-3 rounded-md bg-surface-100">
                    <div>
                        <span
                            class="block text-[11px] font-semibold text-ink-400 uppercase tracking-wider">Pelanggan</span>
                        <p id="reviewCustomer" class="font-body text-xs font-semibold text-ink-900 mt-0.5">-</p>
                    </div>

                    <div>
                        <span
                            class="block text-[11px] font-semibold text-ink-400 uppercase tracking-wider mb-1">Rincian
                            Item</span>
                        <div id="reviewItems"
                            class="pr-1 space-y-1 overflow-y-auto text-xs max-h-36 custom-scrollbar"></div>
                    </div>

                    <div class="pt-3 space-y-1 border-t border-border-200">
                        <div class="flex justify-between text-xs text-ink-700">
                            <span>Subtotal</span>
                            <span id="reviewSubtotal" class="font-mono font-medium">Rp 0</span>
                        </div>
                        <div id="reviewTaxRow" class="flex justify-between text-xs text-ink-700">
                            <span>Pajak Outlet</span>
                            <span id="reviewTax" class="font-mono font-medium">Rp 0</span>
                        </div>
                        <div
                            class="flex items-center justify-between pt-2 text-sm font-bold font-heading text-primary-600">
                            <span>Total Akhir</span>
                            <span id="reviewGrandTotal" class="font-mono text-base">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold font-body text-ink-900">Metode
                            Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" class="hidden peer"
                                    checked onchange="toggleCashInput(true)">
                                <div
                                    class="flex items-center justify-center text-xs font-semibold transition-all border rounded-md h-11 font-body text-ink-700 bg-surface-0 border-border-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-600">
                                    <i class="mr-2 fa-solid fa-money-bill-wave"></i> Tunai (Cash)
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="midtrans" class="hidden peer"
                                    onchange="toggleCashInput(false)">
                                <div
                                    class="flex items-center justify-center text-xs font-semibold transition-all border rounded-md h-11 font-body text-ink-700 bg-surface-0 border-border-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-600">
                                    <i class="mr-2 fa-solid fa-qrcode"></i> QRIS / Transfer
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="cashInputGroup">
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Uang Tunai
                            Diterima</label>
                        <input type="text" id="cashAmount" oninput="formatCurrencyInput(this)"
                            class="w-full px-3 font-mono text-base font-bold border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600"
                            placeholder="0">

                        <div class="flex items-center justify-between p-3 mt-3 rounded-md bg-primary-50">
                            <span class="text-xs font-semibold text-primary-700">Kembalian</span>
                            <p id="changeText" class="font-mono text-base font-bold text-primary-700">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                    Batal
                </button>
                <button onclick="submitOrder('paid')"
                    class="flex-1 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 font-body">
                    Konfirmasi & Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 5: QRIS Popup -->
    <div id="qrisModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-ink-900/40 backdrop-blur-sm">
        <div class="w-full p-6 text-center border rounded-lg shadow-lg bg-surface-0 max-w-modal-sm border-border-200">
            <h3 class="text-lg font-bold font-heading text-ink-900">Pembayaran QRIS Dynamic</h3>
            <p id="qrisInvoiceNumber" class="font-mono font-semibold text-xs text-primary-600 mt-0.5">INV-XXXXX</p>

            <div class="inline-block p-3 my-4 border rounded-md border-border-200 bg-surface-100">
                <img id="qrisImage" src="" alt="QRIS Dynamic Code" class="object-contain w-56 h-56 mx-auto">
            </div>

            <div class="mb-5">
                <p class="text-xs font-body text-ink-700">Arahkan aplikasi m-Banking/E-Wallet pelanggan ke QR Code di
                    atas.</p>
                <p id="qrisTotalAmount" class="mt-1 font-mono text-lg font-bold text-ink-900">Rp 0</p>
            </div>

            <div class="flex gap-3">
                <button onclick="tutupModalQris()"
                    class="flex-1 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                    Tutup
                </button>
                <button id="btnCheckStatus" onclick="cekStatusManual()"
                    class="flex-1 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 font-body">
                    Cek Status
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 6: Success Receipt Modal -->
    <div id="successModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-ink-900/40 backdrop-blur-sm">
        <div class="w-full p-6 text-center border rounded-lg shadow-lg bg-surface-0 max-w-modal-sm border-border-200">
            <div
                class="flex items-center justify-center mx-auto mb-3 rounded-full w-14 h-14 bg-primary-100 text-primary-600">
                <i class="text-2xl fa-solid fa-check"></i>
            </div>
            <h3 class="mb-1 text-lg font-bold font-heading text-ink-900">Transaksi Berhasil!</h3>
            <p class="mb-6 text-xs font-body text-ink-700">Struk belanja berhasil dibuat dan siap dicetak.</p>

            <div class="space-y-2">
                <button id="btnPrintReceipt"
                    class="flex items-center justify-center w-full gap-2 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 font-body">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Struk Belanja</span>
                </button>
                <button onclick="location.reload()"
                    class="w-full text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                    Selesai & Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    <iframe id="printFrame" class="hidden"></iframe>

    <!-- JavaScript Logics Preserved 100% & Cleaned -->
    <script>
        const IS_TAX_ACTIVE = {{ ($settings['tax_active'] ?? '0') == '1' ? 'true' : 'false' }};
        const TAX_PERCENTAGE = {{ $settings['tax_percentage'] ?? '0' }};

        let cart = [];
        let currentOrderId = null;
        let qrisInterval = null;

        $(document).ready(function() {
            $('#customerSelect').select2({
                placeholder: "Cari nama atau nomor HP...",
                allowClear: false,
                width: '100%'
            });
        });

        function filterCategory(category, name) {
            document.getElementById('activeCategoryName').innerText = name;
            const items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                item.style.display = (category === 'all' || itemCategory === category) ? 'flex' : 'none';
            });
        }

        function filterProducts() {
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            const items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(searchTerm) ? 'flex' : 'none';
            });
        }

        function addToCart(id, name, originalPrice, finalPrice, discountApplied, discountName) {
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id,
                    name,
                    price: originalPrice,
                    finalPrice,
                    discountApplied,
                    discountName,
                    quantity: 1
                });
            }
            renderCart();
        }

        function updateQty(id, delta) {
            const itemIndex = cart.findIndex(item => item.id === id);
            if (itemIndex > -1) {
                cart[itemIndex].quantity += delta;
                if (cart[itemIndex].quantity <= 0) cart.splice(itemIndex, 1);
            }
            renderCart();
        }

        function calculateTotals() {
            const subtotal = cart.reduce((acc, i) => acc + (i.price * i.quantity), 0);
            const totalDiscount = cart.reduce((acc, i) => acc + (i.discountApplied * i.quantity), 0);
            const netTotal = subtotal - totalDiscount;
            const tax = IS_TAX_ACTIVE ? Math.round(netTotal * (TAX_PERCENTAGE / 100)) : 0;
            const total = netTotal + tax;

            return {
                subtotal,
                totalDiscount,
                tax,
                total
            };
        }

        function renderCart() {
            const cartContainer = document.getElementById('cartItems');
            const {
                subtotal,
                totalDiscount,
                tax,
                total
            } = calculateTotals();
            const itemCount = cart.reduce((acc, i) => acc + i.quantity, 0);

            if (cart.length === 0) {
                cartContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16 text-center text-ink-400">
                        <i class="mb-2 text-3xl opacity-50 fa-solid fa-cart-flatbed"></i>
                        <p class="text-xs font-semibold font-body">Keranjang masih kosong</p>
                        <p class="font-body text-[11px] text-ink-400 mt-0.5">Pilih produk di sebelah kiri untuk ditambahkan.</p>
                    </div>`;
            } else {
                cartContainer.innerHTML = cart.map(item => `
                    <div class="flex items-center justify-between p-3 border rounded-md shadow-sm bg-surface-0 border-border-200">
                        <div class="flex-1 min-w-0 pr-2 text-left">
                            <p class="text-xs font-semibold leading-tight truncate font-body text-ink-900">${item.name}</p>
                            ${item.discountApplied > 0 ? `<p class="font-body text-[10px] text-accent-500 font-semibold">${item.discountName}</p>` : ''}
                            <p class="font-mono text-xs font-semibold text-primary-600 mt-0.5">Rp ${new Intl.NumberFormat('id-ID').format(item.finalPrice)}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button onclick="updateQty(${item.id}, -1)" class="flex items-center justify-center text-xs font-bold transition-colors rounded-md w-7 h-7 bg-surface-100 hover:bg-border-200 text-ink-900">-</button>
                            <span class="w-6 font-mono text-xs font-semibold text-center text-ink-900">${item.quantity}</span>
                            <button onclick="updateQty(${item.id}, 1)" class="flex items-center justify-center text-xs font-bold transition-colors rounded-md w-7 h-7 bg-surface-100 hover:bg-border-200 text-ink-900">+</button>
                        </div>
                    </div>
                `).join('');
            }

            // Sync Desktop Elements
            document.getElementById('subtotalText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
            document.getElementById('discountText').innerText = '-Rp ' + new Intl.NumberFormat('id-ID').format(
                totalDiscount);
            document.getElementById('totalText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

            // Sync Mobile Elements
            document.getElementById('mobileTotalText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('mobileItemCount').innerText = itemCount;

            const taxElement = document.getElementById('taxText');
            if (taxElement) {
                const taxRow = taxElement.parentElement;
                if (IS_TAX_ACTIVE && tax > 0) {
                    taxRow.style.display = 'flex';
                    taxElement.innerText = '+Rp ' + new Intl.NumberFormat('id-ID').format(tax);
                } else {
                    taxRow.style.display = 'none';
                }
            }
        }

        function formatCurrencyInput(input) {
            let value = input.value.replace(/\D/g, "");
            input.value = value !== "" ? new Intl.NumberFormat('id-ID').format(value) : "";
            calculateChange();
        }

        function calculateChange() {
            const {
                total
            } = calculateTotals();
            const cash = parseInt(document.getElementById('cashAmount').value.replace(/\./g, "")) || 0;
            const change = cash - total;
            const text = document.getElementById('changeText');

            if (cash === 0) {
                text.innerText = "Rp 0";
                text.classList.remove('text-semantic-danger');
            } else if (change < 0) {
                text.innerText = "Uang Kurang";
                text.classList.add('text-semantic-danger');
            } else {
                text.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(change);
                text.classList.remove('text-semantic-danger');
            }
        }

        function toggleCashInput(isCash) {
            const group = document.getElementById('cashInputGroup');
            group.style.opacity = isCash ? "1" : "0.3";
            group.style.pointerEvents = isCash ? "auto" : "none";
            if (!isCash) document.getElementById('cashAmount').value = "";
        }

        function openPaymentModal() {
            if (cart.length === 0) return alert('Pilih produk terlebih dahulu!');

            const {
                subtotal,
                totalDiscount,
                tax,
                total
            } = calculateTotals();
            const customerData = $('#customerSelect').select2('data')[0];
            document.getElementById('reviewCustomer').innerText = customerData ? customerData.text :
                'Pelanggan Umum (Guest)';

            const reviewItemsContainer = document.getElementById('reviewItems');
            reviewItemsContainer.innerHTML = cart.map(item => `
                <div class="flex justify-between font-body text-ink-700">
                    <span>${item.quantity}x ${item.name}</span>
                    <span class="font-mono font-medium text-ink-900">Rp ${new Intl.NumberFormat('id-ID').format(item.finalPrice * item.quantity)}</span>
                </div>
            `).join('');

            document.getElementById('reviewSubtotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal -
                totalDiscount);
            document.getElementById('reviewTax').innerText = '+Rp ' + new Intl.NumberFormat('id-ID').format(tax);
            document.getElementById('reviewGrandTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('reviewTaxRow').style.display = tax > 0 ? 'flex' : 'none';

            document.getElementById('cashAmount').value = "";
            document.getElementById('changeText').innerText = "Rp 0";
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        async function submitOrder(status) {
            const {
                subtotal,
                totalDiscount,
                tax,
                total
            } = calculateTotals();
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            const cashAmount = parseInt(document.getElementById('cashAmount').value.replace(/\./g, "")) || 0;

            if (method === 'cash' && cashAmount < total) return alert('Uang tunai kurang!');

            const data = {
                customer_id: document.getElementById('customerSelect').value === 'guest' ? null : document
                    .getElementById('customerSelect').value,
                payment_method: method,
                payment_status: status,
                subtotal: subtotal,
                discount: totalDiscount,
                tax: tax,
                grand_total: total,
                paid_amount: method === 'cash' ? cashAmount : total,
                items: cart.map(i => ({
                    id: i.id,
                    name: i.name,
                    quantity: i.quantity,
                    price: i.price,
                    finalPrice: i.finalPrice
                })),
                _token: '{{ csrf_token() }}'
            };

            const btn = document.querySelector('#paymentModal button[onclick="submitOrder(\'paid\')"]');

            try {
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = "Memproses...";
                }

                const response = await fetch('/pos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();

                if (res.success) {
                    document.getElementById('paymentModal').classList.add('hidden');

                    if (res.payment_method === 'midtrans' && res.qr_url) {
                        currentOrderId = res.order_id;
                        document.getElementById('qrisInvoiceNumber').innerText = res.invoice_number;
                        document.getElementById('qrisTotalAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID')
                            .format(total);
                        document.getElementById('qrisImage').src = res.qr_url;

                        document.getElementById('qrisModal').classList.remove('hidden');

                        if (qrisInterval) clearInterval(qrisInterval);
                        qrisInterval = setInterval(function() {
                            jalankanAutoCheckStatus(currentOrderId);
                        }, 3000);
                    } else {
                        triggerTampilkanReceipt(res.order_id);
                    }
                } else {
                    alert('Gagal: ' + res.message);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerText = "Konfirmasi & Cetak";
                    }
                }
            } catch (e) {
                console.error('Detail Error:', e);
                alert('Terjadi kesalahan koneksi sistem.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = "Konfirmasi & Cetak";
                }
            }
        }

        function tutupModalQris() {
            if (qrisInterval) {
                clearInterval(qrisInterval);
                qrisInterval = null;
            }
            document.getElementById('qrisModal').classList.add('hidden');
            const btn = document.querySelector('#paymentModal button[onclick="submitOrder(\'paid\')"]');
            if (btn) {
                btn.disabled = false;
                btn.innerText = "Konfirmasi & Cetak";
            }
        }

        async function jalankanAutoCheckStatus(orderId) {
            if (!orderId) return;
            try {
                const response = await fetch(`/orders/${orderId}/check-status`);
                const res = await response.json();

                if (res.status === 'paid') {
                    if (qrisInterval) {
                        clearInterval(qrisInterval);
                        qrisInterval = null;
                    }
                    document.getElementById('qrisModal').classList.add('hidden');
                    triggerTampilkanReceipt(orderId);
                }
            } catch (e) {
                console.error('Auto check status error:', e);
            }
        }

        async function cekStatusManual() {
            if (!currentOrderId) return;
            const btn = document.getElementById('btnCheckStatus');
            btn.disabled = true;
            btn.innerText = "Memeriksa...";

            try {
                const response = await fetch(`/orders/${currentOrderId}/check-status`);
                const res = await response.json();

                if (res.status === 'paid') {
                    if (qrisInterval) {
                        clearInterval(qrisInterval);
                        qrisInterval = null;
                    }
                    alert('Pembayaran Terverifikasi Lunas!');
                    document.getElementById('qrisModal').classList.add('hidden');
                    triggerTampilkanReceipt(currentOrderId);
                } else {
                    alert('Pembayaran belum masuk. Silakan lakukan pembayaran terlebih dahulu.');
                }
            } catch (e) {
                alert('Gagal memeriksa status.');
            } finally {
                btn.disabled = false;
                btn.innerText = "Cek Status";
            }
        }

        function triggerTampilkanReceipt(orderId) {
            const btnPrint = document.getElementById('btnPrintReceipt');
            btnPrint.onclick = function() {
                const frame = document.getElementById('printFrame');
                const url = `/orders/${orderId}/print`;
                frame.src = url;

                btnPrint.disabled = true;
                btnPrint.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Menyiapkan...';

                frame.onload = function() {
                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } catch (e) {
                        console.error("Gagal mencetak iframe:", e);
                        window.open(url, '_blank');
                    }
                };
            };
            document.getElementById('successModal').classList.remove('hidden');
        }

        function openCustomerModal() {
            document.getElementById('customerModal').classList.remove('hidden');
        }

        function closeCustomerModal() {
            document.getElementById('customerModal').classList.add('hidden');
            document.getElementById('addCustomerForm').reset();
        }

        function openOpenShiftModal() {
            document.getElementById('openShiftModal').classList.remove('hidden');
        }

        document.getElementById('addCustomerForm').onsubmit = async function(e) {
            e.preventDefault();
            const name = document.getElementById('cust_name').value;
            const phone = document.getElementById('cust_phone').value;
            const is_member = document.getElementById('cust_member').checked ? '1' : '0';

            try {
                const response = await fetch('/customers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name,
                        phone,
                        is_member
                    })
                });

                const res = await response.json();

                if (res.success) {
                    const displayPhone = res.phone ? ` (${res.phone})` : '';
                    const newOption = new Option(`${res.name}${displayPhone}`, res.id, true, true);

                    $('#customerSelect').append(newOption).trigger('change');
                    closeCustomerModal();
                    alert('Pelanggan berhasil ditambahkan!');
                } else {
                    alert('Gagal: ' + (res.message || 'Terjadi kesalahan.'));
                }
            } catch (error) {
                alert('Gagal menambah pelanggan.');
            }
        };

        document.getElementById('openShiftForm').onsubmit = async function(e) {
            e.preventDefault();
            const rawCash = document.getElementById('input_cash_start').value.replace(/\./g, "") || 0;
            const cash_start = parseInt(rawCash);

            try {
                const response = await fetch('/shifts/open', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cash_start
                    })
                });
                const res = await response.json();
                if (res.success) {
                    alert(res.message);
                    document.getElementById('openShiftModal').classList.add('hidden');
                    location.reload();
                } else {
                    alert(res.message);
                }
            } catch (error) {
                alert('Gagal memproses buka shift.');
            }
        };

        async function openCloseShiftModal() {
            try {
                const response = await fetch('/shifts/summary');
                const res = await response.json();

                if (res.success) {
                    document.getElementById('text_shift_start').innerText = 'Rp ' + new Intl.NumberFormat('id-ID')
                        .format(res.cash_start);
                    document.getElementById('text_shift_sales').innerText = '+Rp ' + new Intl.NumberFormat('id-ID')
                        .format(res.cash_sales);
                    document.getElementById('text_shift_expected').innerText = 'Rp ' + new Intl.NumberFormat('id-ID')
                        .format(res.cash_expected);

                    document.getElementById('input_cash_actual').value = "";
                    document.getElementById('shift_notes').value = "";
                    document.getElementById('closeShiftModal').classList.remove('hidden');
                }
            } catch (error) {
                alert('Gagal memuat rangkuman shift.');
            }
        }

        document.getElementById('closeShiftForm').onsubmit = async function(e) {
            e.preventDefault();
            const rawActual = document.getElementById('input_cash_actual').value.replace(/\./g, "") || 0;
            const cash_actual = parseInt(rawActual);
            const notes = document.getElementById('shift_notes').value;

            if (!confirm('Apakah Anda yakin ingin menutup shift kerja sekarang?')) return;

            try {
                const response = await fetch('/shifts/close', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cash_actual,
                        notes
                    })
                });
                const res = await response.json();
                if (res.success) {
                    alert('Shift berhasil ditutup!');
                    location.reload();
                }
            } catch (error) {
                alert('Gagal memproses tutup shift.');
            }
        };
    </script>
</x-app-layout>
