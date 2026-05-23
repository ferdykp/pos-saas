<x-app-layout>
    <div class="flex h-full overflow-hidden bg-gray-50">
        <div class="flex flex-col flex-1 p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="relative w-full group sm:w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i
                            class="transition-colors fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-blue-500"></i>
                    </div>
                    <input type="text" id="searchProduct" onkeyup="filterProducts()" placeholder="Cari menu..."
                        class="w-full py-3 pl-12 pr-4 text-sm font-medium transition-all bg-white border shadow-sm outline-none text-slate-700 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="relative ml-4" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-bold text-gray-700 transition-all bg-white border shadow-sm border-slate-200 rounded-xl hover:bg-gray-50">
                        <i class="text-blue-500 fa-solid fa-layer-group"></i>
                        <span id="activeCategoryName">Semua Kategori</span>
                        <i class="text-xs transition-transform duration-200 fa-solid fa-chevron-down"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open"
                        class="absolute left-0 z-50 w-56 mt-2 overflow-hidden bg-white border shadow-2xl border-slate-100 rounded-2xl">
                        <div class="py-2 overflow-y-auto max-h-80">
                            <button onclick="filterCategory('all', 'Semua Kategori')" @click="open = false"
                                class="flex items-center w-full px-4 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50">
                                <i class="mr-3 opacity-50 fa-solid fa-border-all"></i> Semua Kategori
                            </button>
                            @foreach ($categories as $cat)
                                <button onclick="filterCategory('{{ $cat->name }}', '{{ $cat->name }}')"
                                    @click="open = false"
                                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50">
                                    <i class="mr-3 text-xs fa-solid fa-tag opacity-30"></i> {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div id="productGrid"
                class="grid grid-cols-2 gap-4 pb-20 pr-2 overflow-y-auto lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($products as $p)
                    <div onclick="addToCart({{ $p->id }}, '{{ $p->product_name }}', {{ $p->sell_price }}, {{ $p->final_price }}, {{ $p->discount_applied }}, '{{ $p->discount_name }}')"
                        data-category="{{ $p->category ? $p->category->name : '' }}"
                        data-name="{{ strtolower($p->product_name) }}"
                        class="relative p-2 transition-all bg-white border border-gray-100 rounded-lg shadow-sm cursor-pointer product-item hover:shadow-xl hover:-translate-y-1 group">

                        @if ($p->discount_applied > 0)
                            <span
                                class="absolute top-2 right-2 bg-rose-500 text-white font-black text-[9px] px-2 py-0.5 rounded-full z-10 uppercase tracking-wider animate-pulse">
                                {{ $p->discount_name }}
                            </span>
                        @endif

                        <div
                            class="flex items-center justify-center w-full h-32 mb-3 overflow-hidden text-gray-300 bg-gray-50 rounded-2xl">
                            @if ($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" class="object-cover w-full h-full">
                            @else
                                <i class="text-3xl fa-solid fa-image opacity-20"></i>
                            @endif
                        </div>

                        <h4 class="mb-1 text-sm font-bold text-gray-900 truncate group-hover:text-blue-600">
                            {{ $p->product_name }}</h4>

                        <div class="flex flex-col">
                            @if ($p->discount_applied > 0)
                                <span class="text-[10px] text-gray-400 line-through">Rp
                                    {{ number_format($p->sell_price, 0, ',', '.') }}</span>
                                <p class="text-xs font-black text-rose-600">Rp
                                    {{ number_format($p->final_price, 0, ',', '.') }}</p>
                            @else
                                <p class="text-xs font-black text-blue-600">Rp
                                    {{ number_format($p->sell_price, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold">
                            {{ $p->type === 'service' ? 'Jasa' : 'Stok: ' . $p->stock }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col bg-white border-l border-gray-100 shadow-2xl w-96">
            <div class="p-6 border-b border-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-black text-gray-900">Daftar Pesanan</h3>
                    <div class="flex gap-2">
                        @if (!$hasShift)
                            <button type="button" onclick="openOpenShiftModal()"
                                class="flex items-center gap-1 px-3 py-1 text-xs font-bold text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100 animate-pulse">
                                <i class="fa-solid fa-cash-register"></i> Buka Shift
                            </button>
                        @else
                            <button type="button" onclick="openCloseShiftModal()"
                                class="flex items-center gap-1 px-3 py-1 text-xs font-bold transition-colors rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 animate-pulse">
                                <i class="fa-solid fa-cash-register"></i> Tutup Shift
                            </button>
                            {{-- <button type="button" onclick="openCloseShiftModal()" title="Tutup Shift Kerja"
                                class="flex items-center justify-center w-8 h-8 transition-colors rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100">
                                <i class="fa-solid fa-store-slash"></i> Tutup Shift
                            </button> --}}
                        @endif
                        <button type="button" onclick="openCustomerModal()"
                            class="flex items-center justify-center w-8 h-8 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100">
                            <i class="fa-solid fa-user-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 z-10 flex items-center pl-4 pointer-events-none">
                        <i class="text-xs text-gray-400 fa-solid fa-search"></i>
                    </div>
                    <select id="customerSelect" class="w-full">
                        <option value="guest">Guest (Pelanggan Umum)</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->name }} {{ $c->phone ? '(' . $c->phone . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="cartItems" class="flex-1 p-6 space-y-4 overflow-y-auto text-center no-scrollbar">
                <div class="py-20 italic text-gray-300">
                    <p>Pilih menu untuk mulai...</p>
                </div>
            </div>

            <div class="p-6 bg-gray-50 rounded-t-[3rem] shadow-[0_-8px_30px_rgb(0,0,0,0.04)]">
                <div class="mb-4 space-y-2">
                    <div class="flex justify-between text-sm font-bold text-gray-500">
                        <span>Subtotal</span>
                        <span id="subtotalText">Rp 0</span>
                    </div>

                    <div class="flex justify-between text-xs font-black text-rose-600">
                        <span>Potongan Promo</span>
                        <span id="discountText">-Rp 0</span>
                    </div>

                    @if (($settings['tax_active'] ?? '0') == '1')
                        <div id="taxRow" class="flex justify-between text-xs font-medium text-gray-400">
                            <span>Pajak Resto ({{ $settings['tax_percentage'] ?? 0 }}%)</span>
                            <span id="taxText">+Rp 0</span>
                        </div>
                    @endif

                    <div class="flex justify-between pt-4 text-2xl font-black text-gray-900 border-t border-gray-200">
                        <span>Total</span>
                        <span id="totalText">Rp 0</span>
                    </div>
                </div>

                <button onclick="openPaymentModal()"
                    class="w-full py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700 active:scale-95">
                    KONFIRMASI PEMBAYARAN
                </button>
            </div>
        </div>
    </div>

    <div id="openShiftModal"
        class="fixed inset-0 z-[100] flex items-center justify-center hidden p-4 bg-gray-900/80 backdrop-blur-md">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl text-center">
            <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 text-blue-600 bg-blue-100 rounded-full">
                <i class="text-3xl fa-solid fa-cash-register"></i>
            </div>
            <h3 class="mb-2 text-2xl font-black text-gray-900">Buka Shift Kasir</h3>
            <p class="mb-6 text-xs font-medium text-gray-400">Masukkan jumlah saldo kas kecil / modal awal uang tunai
                yang ada di dalam laci meja kasir saat ini.</p>

            <form id="openShiftForm" class="space-y-4 text-left">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Uang Modal Awal (Cash)</label>
                    <input type="text" id="input_cash_start" oninput="formatCurrencyInput(this)" required
                        class="w-full px-6 py-4 text-xl font-black text-center border-none bg-gray-50 rounded-2xl focus:ring-blue-500"
                        placeholder="Rp 0">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')"
                        class="flex-1 py-4 font-bold text-gray-400 bg-gray-100 rounded-2xl">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-4 font-black text-white bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700">
                        MULAI OPERASIONAL TOKO
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="closeShiftModal"
        class="fixed inset-0 z-[100] flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <h3 class="mb-2 text-2xl font-black text-center text-gray-900">Laporan Tutup Shift</h3>
            <p class="mb-6 text-xs font-medium text-center text-gray-400">Hitung uang fisik di laci dan samakan dengan
                hitungan sistem.</p>

            <div class="p-4 mb-4 space-y-2 text-sm font-bold text-gray-600 bg-gray-50 rounded-2xl">
                <div class="flex justify-between"><span>Modal Awal:</span><span id="text_shift_start"
                        class="text-gray-900">Rp 0</span></div>
                <div class="flex justify-between"><span>Penjualan Tunai:</span><span id="text_shift_sales"
                        class="text-blue-600">+Rp 0</span></div>
                <div class="flex justify-between pt-2 font-black text-gray-900 border-t border-gray-200">
                    <span>Total Ekspektasi Sistem:</span><span id="text_shift_expected">Rp 0</span>
                </div>
            </div>

            <form id="closeShiftForm" class="space-y-4">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Total Uang Fisik Di Laci
                        (Cash)</label>
                    <input type="text" id="input_cash_actual" oninput="formatCurrencyInput(this)" required
                        class="w-full px-5 py-3 text-lg font-black bg-gray-100 border-none rounded-2xl focus:ring-blue-500"
                        placeholder="Hitung uang fisik tunai...">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Catatan / Keterangan</label>
                    <textarea id="shift_notes" rows="2"
                        class="w-full px-4 py-3 text-sm border-none bg-gray-50 rounded-2xl focus:ring-blue-500"
                        placeholder="Contoh: Selisih minus Rp 2.000 karena tidak ada kembalian kembalian pecahan kecil..."></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button"
                        onclick="document.getElementById('closeShiftModal').classList.add('hidden')"
                        class="flex-1 py-4 font-bold text-gray-400 bg-gray-100 rounded-2xl">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 font-black text-white shadow-xl bg-rose-600 rounded-2xl hover:bg-rose-700">
                        TUTUP SHIFT
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="customerModal"
        class="fixed inset-0 z-[80] flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <h3 class="mb-6 text-2xl font-black text-center text-gray-900">Tambah Pelanggan Baru</h3>

            <form id="addCustomerForm" class="space-y-4">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Nama Lengkap</label>
                    <input type="text" id="cust_name" required
                        class="w-full px-5 py-3 border-none rounded-2xl bg-gray-50 focus:ring-blue-500"
                        placeholder="Contoh: Budi Santoso">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase">No. WhatsApp</label>
                    <input type="text" id="cust_phone"
                        class="w-full px-5 py-3 border-none rounded-2xl bg-gray-50 focus:ring-blue-500"
                        placeholder="0812xxxx">
                </div>
                <div class="flex items-center p-4 bg-blue-50 rounded-2xl">
                    <input type="checkbox" id="cust_member" value="1"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="cust_member" class="ml-3 text-sm font-bold text-blue-900">Daftarkan sebagai Member
                        Aktif</label>
                </div>

                <div class="pt-4 space-y-2">
                    <button type="submit"
                        class="w-full py-4 font-black text-white bg-blue-600 shadow-lg rounded-2xl hover:bg-blue-700">SIMPAN
                        PELANGGAN</button>
                    <button type="button" onclick="closeCustomerModal()"
                        class="w-full py-2 text-sm font-bold text-gray-400">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <div id="paymentModal"
        class="fixed inset-0 z-[70] flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-2xl shadow-2xl overflow-y-auto max-h-[90vh]">
            <h3 class="mb-2 text-2xl font-black text-center text-gray-900">Konfirmasi Pembayaran</h3>

            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                <div class="p-4 space-y-4 border bg-gray-50 rounded-2xl">
                    <div>
                        <h4 class="mb-2 text-xs font-black text-gray-400 uppercase">Pelanggan</h4>
                        <p id="reviewCustomer" class="text-sm font-bold text-gray-800">-</p>
                    </div>
                    <div>
                        <h4 class="mb-2 text-xs font-black text-gray-400 uppercase">Rincian Item</h4>
                        <div id="reviewItems" class="space-y-1 max-h-40 overflow-y-auto pr-2 text-[11px]"></div>
                    </div>
                    <div class="pt-2 border-t border-gray-200">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Subtotal</span>
                            <span id="reviewSubtotal" class="font-bold">Rp 0</span>
                        </div>
                        <div id="reviewTaxRow" class="flex justify-between text-xs">
                            <span class="text-gray-500">Pajak</span>
                            <span id="reviewTax" class="font-bold">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm font-black text-blue-600">
                            <span>Total Akhir</span>
                            <span id="reviewGrandTotal">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-xs font-black text-gray-400 uppercase">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="hidden peer" checked
                                onchange="toggleCashInput(true)">
                            <div
                                class="p-3 text-sm font-bold text-center text-gray-600 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600">
                                Tunai</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="transfer" class="hidden peer"
                                onchange="toggleCashInput(false)">
                            <div
                                class="p-3 text-sm font-bold text-center text-gray-600 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600">
                                QRIS/TF</div>
                        </label>
                    </div>

                    <div id="cashInputGroup">
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Uang Tunai
                            Diterima</label>
                        <input type="text" id="cashAmount" oninput="formatCurrencyInput(this)"
                            class="w-full px-6 py-4 text-2xl font-black bg-gray-100 border-none rounded-2xl focus:ring-blue-500"
                            placeholder="0">
                        <div class="flex items-center justify-between p-4 mt-3 bg-blue-50 rounded-2xl">
                            <span class="text-xs font-bold text-blue-400 uppercase">Kembalian</span>
                            <p id="changeText" class="text-xl font-black text-blue-900">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 py-4 font-bold text-gray-400 bg-gray-100 rounded-2xl">BATAL</button>
                <button onclick="submitOrder('paid')"
                    class="flex-[2] py-4 font-black text-white bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700 active:scale-95">KONFIRMASI
                    & CETAK</button>
            </div>
        </div>
    </div>

    <div id="successModal"
        class="fixed inset-0 z-[90] flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-sm shadow-2xl text-center">
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto mb-6 text-green-600 bg-green-100 rounded-full">
                <i class="text-4xl fa-solid fa-check"></i>
            </div>
            <h3 class="mb-2 text-2xl font-black text-gray-900">Transaksi Berhasil!</h3>
            <p class="mb-8 text-sm font-medium text-gray-500">Pesanan telah berhasil disimpan ke dalam sistem.</p>

            <div class="space-y-3">
                <button id="btnPrintReceipt"
                    class="flex items-center justify-center w-full gap-2 py-4 font-black text-white bg-blue-600 shadow-lg rounded-2xl hover:bg-blue-700">
                    <i class="fa-solid fa-print"></i> CETAK STRUK
                </button>
                <button onclick="location.reload()" class="w-full py-2 text-sm font-bold text-gray-400">Tutup &
                    Selesai</button>
            </div>
        </div>
    </div>

    <iframe id="printFrame" style="display:none;"></iframe>

    <script>
        const IS_TAX_ACTIVE = {{ ($settings['tax_active'] ?? '0') == '1' ? 'true' : 'false' }};
        const TAX_PERCENTAGE = {{ $settings['tax_percentage'] ?? '0' }};

        let cart = [];

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
                item.style.display = (category === 'all' || itemCategory === category) ? 'block' : 'none';
            });
        }

        function filterProducts() {
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            const items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(searchTerm) ? 'block' : 'none';
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

            if (cart.length === 0) {
                cartContainer.innerHTML = '<div class="py-20 italic text-gray-300">Pilih menu untuk mulai...</div>';
            } else {
                cartContainer.innerHTML = cart.map(item => `
                    <div class="flex items-center justify-between p-3 bg-white border shadow-sm border-gray-50 rounded-2xl">
                        <div class="flex-1 text-left">
                            <p class="text-sm font-bold leading-tight text-gray-900">${item.name}</p>
                            <p class="text-[10px] font-black text-rose-500">${item.discountApplied > 0 ? item.discountName : ''}</p>
                            <p class="text-xs font-black text-blue-600">Rp ${new Intl.NumberFormat('id-ID').format(item.finalPrice)}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateQty(${item.id}, -1)" class="font-bold bg-gray-100 rounded-lg w-7 h-7 hover:bg-gray-200">-</button>
                            <span class="w-4 text-sm font-bold text-center">${item.quantity}</span>
                            <button onclick="updateQty(${item.id}, 1)" class="font-bold bg-gray-100 rounded-lg w-7 h-7 hover:bg-gray-200">+</button>
                        </div>
                    </div>
                `).join('');
            }

            document.getElementById('subtotalText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
            document.getElementById('discountText').innerText = '-Rp ' + new Intl.NumberFormat('id-ID').format(
                totalDiscount);
            document.getElementById('totalText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

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
                text.classList.remove('text-red-500');
            } else if (change < 0) {
                text.innerText = "Uang Kurang";
                text.classList.add('text-red-500');
            } else {
                text.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(change);
                text.classList.remove('text-red-500');
            }
        }

        function toggleCashInput(isCash) {
            const group = document.getElementById('cashInputGroup');
            group.style.opacity = isCash ? "1" : "0.3";
            group.style.pointerEvents = isCash ? "auto" : "none";
            if (!isCash) document.getElementById('cashAmount').value = "";
        }

        function openPaymentModal() {
            if (cart.length === 0) return alert('Pilih menu terlebih dahulu!');

            const {
                subtotal,
                totalDiscount,
                tax,
                total
            } = calculateTotals();
            const customerData = $('#customerSelect').select2('data')[0];
            document.getElementById('reviewCustomer').innerText = customerData ? customerData.text : 'Guest';

            const reviewItemsContainer = document.getElementById('reviewItems');
            reviewItemsContainer.innerHTML = cart.map(item => `
                <div class="flex justify-between">
                    <span class="text-gray-600">${item.quantity}x ${item.name}</span>
                    <span class="font-medium text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(item.finalPrice * item.quantity)}</span>
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

            const btn = event.target;

            try {
                btn.disabled = true;
                btn.innerText = "MEMPROSES...";

                const response = await fetch('/pos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();

                if (res.success) {
                    document.getElementById('paymentModal').classList.add('hidden');
                    const btnPrint = document.getElementById('btnPrintReceipt');
                    btnPrint.onclick = function() {
                        const frame = document.getElementById('printFrame');
                        const url = `/orders/${res.order_id}/print`;
                        frame.src = url;

                        btnPrint.disabled = true;
                        btnPrint.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> MENYIAPKAN...';

                        frame.onload = function() {
                            try {
                                frame.contentWindow.focus();
                                frame.contentWindow.print();
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } catch (e) {
                                console.error("Gagal mencetak melalui iframe:", e);
                                window.open(url, '_blank');
                            }
                        };
                    };
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    alert('Gagal: ' + res.message);
                    btn.disabled = false;
                    btn.innerText = "KONFIRMASI & CETAK";
                }
            } catch (e) {
                console.error('Detail Error:', e);
                alert('Terjadi kesalahan koneksi sistem.');
                btn.disabled = false;
                btn.innerText = "KONFIRMASI & CETAK";
            }
        }

        // FUNGSI MODAL & AJAX MANAJEMEN PELANGGAN BARU
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
                    alert('Gagal: ' + (res.message || 'Terjadi kesalahan internal.'));
                }
            } catch (error) {
                console.error('Error Add Customer:', error);
                alert('Gagal menambah pelanggan.');
            }
        };

        // LOGIKA OPERASIONAL SHIFT KASIR
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
                    location.reload(); // Refresh untuk update status halaman
                } else {
                    alert(res.message);
                }
            } catch (error) {
                alert('Gagal memproses buka shift.');
            }
        };

        async function openCloseShiftModal() {
            try {
                // Ambil data summary berjalan dari server via AJAX
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
                alert('Gagal memuat rangkuman shift berjalan.');
            }
        }

        document.getElementById('closeShiftForm').onsubmit = async function(e) {
            e.preventDefault();
            const rawActual = document.getElementById('input_cash_actual').value.replace(/\./g, "") || 0;
            const cash_actual = parseInt(rawActual);
            const notes = document.getElementById('shift_notes').value;

            if (!confirm('Apakah Anda yakin ingin menutup shift kerja sekarang? Data tidak bisa diubah kembali.'))
                return;

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
                    alert('Shift berhasil ditutup! Halaman akan dimuat ulang.');
                    location.reload();
                }
            } catch (error) {
                alert('Gagal memproses tutup shift.');
            }
        };
    </script>
</x-app-layout>
