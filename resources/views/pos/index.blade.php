<x-app-layout>
    <div class="flex h-full overflow-hidden bg-gray-50">
        <div class="flex flex-col flex-1 p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <!-- Bagian Search -->
                <div class="relative w-full group sm:w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i
                            class="transition-colors fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-blue-500"></i>
                    </div>
                    <input type="text" id="searchProduct" onkeyup="filterProducts()"
                        placeholder="Cari produk atau scan..."
                        class="w-full py-3 pl-12 pr-4 text-sm font-medium transition-all bg-white border shadow-sm outline-none text-slate-700 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Bagian Filter Kategori -->
                <div class="flex gap-2 pb-2 overflow-x-auto" id="categoryFilters">
                    <button onclick="filterCategory('all')"
                        class="px-4 py-2 text-sm font-bold text-white transition-all bg-blue-600 shadow-lg category-btn active rounded-xl shadow-blue-100">
                        Semua
                    </button>
                    @foreach ($categories as $cat)
                        <button onclick="filterCategory('{{ $cat->name }}')"
                            class="px-4 py-2 text-sm font-bold text-gray-600 transition bg-white border border-transparent category-btn rounded-xl hover:bg-gray-100">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Grid Produk -->
            <div id="productGrid"
                class="grid grid-cols-2 gap-4 pb-20 pr-2 overflow-y-auto lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($products as $p)
                    <!-- Tambahkan data-category dan class product-item -->
                    <div onclick="addToCart({{ $p->id }}, '{{ $p->product_name }}', {{ $p->sell_price }})"
                        data-category="{{ $p->category ? $p->category->name : '' }}"
                        data-name="{{ strtolower($p->product_name) }}"
                        class="p-2 transition-all bg-white border border-gray-100 rounded-lg shadow-sm cursor-pointer product-item hover:shadow-xl hover:-translate-y-1 group">

                        <div
                            class="flex items-center justify-center w-full h-32 mb-3 overflow-hidden text-gray-300 bg-gray-50 rounded-2xl">
                            @if ($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" class="object-cover w-full h-full">
                            @else
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <h4 class="mb-1 text-sm font-bold text-gray-900 truncate transition group-hover:text-blue-600">
                            {{ $p->product_name }}</h4>
                        <p class="text-xs font-black text-blue-600">Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold">
                            {{ $p->type === 'service' ? 'Jasa' : 'Stok: ' . $p->stock }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bagian Cart tetap sama -->
        <div class="flex flex-col bg-white border-l border-gray-100 shadow-2xl w-96">
            <!-- ... (Konten Cart Anda) ... -->
            <div class="p-6 border-b border-gray-50">
                <h3 class="mb-4 text-xl font-black text-gray-900">Pesanan Baru</h3>
                <div class="flex gap-2">
                    <select id="customerSelect"
                        class="flex-1 px-4 py-3 text-sm font-bold border-none bg-gray-50 rounded-2xl focus:ring-blue-500">
                        <option value="">Pelanggan Umum (Guest)</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}
                                ({{ $c->is_member ? 'Member' : 'Reguler' }})
                            </option>
                        @endforeach
                    </select>
                    <button onclick="document.getElementById('addCustomerModal').classList.remove('hidden')"
                        class="flex items-center justify-center px-4 text-blue-600 transition bg-blue-50 rounded-2xl hover:bg-blue-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
            <div id="cartItems" class="flex-1 p-6 space-y-4 overflow-y-auto">
                <div class="py-20 italic text-center text-gray-300">
                    <p>Keranjang masih kosong</p>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-t-[3rem]">
                <div class="mb-6 space-y-2">
                    <div class="flex justify-between text-sm font-bold text-gray-500"><span>Subtotal</span><span
                            id="subtotalText">Rp 0</span></div>
                    <div class="flex justify-between pt-2 text-xl font-black text-gray-900 border-t border-gray-200">
                        <span>Total</span><span id="totalText">Rp 0</span>
                    </div>
                </div>
                <button onclick="openPaymentModal()"
                    class="w-full py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                    BAYAR SEKARANG
                </button>
            </div>
        </div>
    </div>

    <!-- Modal dan Scripts -->
    <script>
        // Logika Filter Kategori
        function filterCategory(category) {
            const items = document.querySelectorAll('.product-item');
            const buttons = document.querySelectorAll('.category-btn');

            // 1. Update UI Tombol
            buttons.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-100');
                btn.classList.add('bg-white', 'text-gray-600');
            });
            event.currentTarget.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-100');
            event.currentTarget.classList.remove('bg-white', 'text-gray-600');

            // 2. Filter Produk
            items.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Logika Search (Sinkron dengan Filter)
        function filterProducts() {
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            const items = document.querySelectorAll('.product-item');

            // Saat mencari, kita abaikan filter kategori atau cari di dalam kategori yang tampil
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                if (name.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // --- Sisa Script Cart & Payment Anda ---
        let cart = [];

        function addToCart(id, name, price) {
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    quantity: 1
                });
            }
            renderCart();
        }

        function updateQty(id, delta) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) cart = cart.filter(i => i.id !== id);
            }
            renderCart();
        }

        function renderCart() {
            const cartContainer = document.getElementById('cartItems');
            let subtotal = 0;
            if (cart.length === 0) {
                cartContainer.innerHTML =
                    '<div class="py-20 italic text-center text-gray-300"><p>Keranjang masih kosong</p></div>';
                document.getElementById('subtotalText').innerText = 'Rp 0';
                document.getElementById('totalText').innerText = 'Rp 0';
                return;
            }
            cartContainer.innerHTML = cart.map(item => {
                subtotal += item.price * item.quantity;
                return `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-900">${item.name}</p>
                        <p class="text-xs font-black text-blue-600">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="updateQty(${item.id}, -1)" class="flex items-center justify-center w-6 h-6 text-gray-500 bg-white border rounded-lg">-</button>
                        <span class="w-4 text-sm font-bold text-center">${item.quantity}</span>
                        <button onclick="updateQty(${item.id}, 1)" class="flex items-center justify-center w-6 h-6 text-gray-500 bg-white border rounded-lg">+</button>
                    </div>
                </div>`;
            }).join('');
            const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
            document.getElementById('subtotalText').innerText = formatted;
            document.getElementById('totalText').innerText = formatted;
        }

        function openPaymentModal() {
            if (cart.length === 0) return alert('Keranjang masih kosong!');
            let total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            document.getElementById('modalTotalAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('paymentModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('cashAmount').focus(), 100);
        }

        function formatCurrencyInput(input) {
            let value = input.value.replace(/\D/g, "");
            input.value = value !== "" ? new Intl.NumberFormat('id-ID').format(value) : "";
            calculateChange();
        }

        function getRawValue(id) {
            let value = document.getElementById(id).value;
            return parseInt(value.replace(/\./g, "")) || 0;
        }

        function calculateChange() {
            let total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            let cash = getRawValue('cashAmount');
            let change = cash - total;
            const changeText = document.getElementById('changeText');
            if (cash === 0) {
                changeText.innerText = 'Rp 0';
                changeText.classList.remove('text-red-500');
            } else if (change < 0) {
                changeText.innerText = 'Uang Kurang';
                changeText.classList.add('text-red-500');
            } else {
                changeText.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                changeText.classList.remove('text-red-500');
            }
        }

        async function submitOrder(paymentStatus) {
            if (cart.length === 0) return alert('Keranjang kosong!');
            const customerId = document.getElementById('customerSelect').value;
            if (!customerId) return alert('Pilih pelanggan terlebih dahulu!');
            const total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            const paidAmount = getRawValue('cashAmount');
            const data = {
                customer_id: customerId,
                subtotal: total,
                grand_total: total,
                paid_amount: paidAmount,
                payment_status: paymentStatus,
                items: cart,
                _token: '{{ csrf_token() }}'
            };
            try {
                const response = await fetch('{{ route('pos.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
            }
        }
    </script>
</x-app-layout>
