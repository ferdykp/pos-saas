<x-guest-layout>
    <div class="min-h-screen bg-primary-100/40">
        <nav class="flex justify-between py-3 mx-16">
            <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
                <div class="flex items-center justify-center gap-2">
                    <img src="{{ asset('img/growpos_logo.png') }}" class="w-10" alt="GrowPOS Logo">
                    <div class="font-bold text-h2 text-primary-600">GrowPOS</div>
                </div>
            </a>

            <div class="flex items-center justify-center space-x-2">
                <div class="font-semibold text-body-base">Sudah punya akun ?</div>
                <a href="{{ route('login') }}">
                    <div class="relative inline-block font-bold transition-all group text-body-base text-primary-600">
                        <span
                            class="absolute left-0 bottom-[-2px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-primary-600"></span>
                        Masuk
                    </div>
                </a>
            </div>
        </nav>

        {{-- Banner Notifikasi Verifikasi Email Sukses / Status System --}}
        <div class="px-4 mx-auto max-w-8xl">
            @if (session('status'))
                <div class="flex items-center gap-3 p-4 mt-2 mb-2 text-sm font-semibold border rounded-lg shadow-sm text-emerald-800 bg-emerald-50/90 border-emerald-200"
                    data-aos="fade-down">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-emerald-900 text-body-base">Verifikasi Email Berhasil!</p>
                        <p class="font-medium text-emerald-700 text-body-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if (request()->has('verified') && request()->get('verified') == 1)
                <div class="flex items-center gap-3 p-4 mt-2 mb-2 text-sm font-semibold border rounded-lg shadow-sm text-emerald-800 bg-emerald-50/90 border-emerald-200"
                    data-aos="fade-down">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-emerald-900 text-body-base">Email Berhasil Diverifikasi!</p>
                        <p class="font-medium text-emerald-700 text-body-sm">Selamat, akun GrowPOS Anda telah aktif.
                            Silakan lengkapi informasi bisnis Anda di bawah ini.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="px-4 mx-auto max-w-8xl">
            <!-- items-stretch membuat tinggi kolom kiri & kanan otomatis sama -->
            <div class="grid items-stretch grid-cols-1 gap-8 py-8 lg:grid-cols-2">

                <!-- KOLOM KIRI -->
                <div class="flex flex-col justify-between p-6 space-y-4 rounded-lg" data-aos="fade-right"
                    data-aos-delay="100">
                    <div>
                        <!-- Badge Telah Dipercaya -->
                        <div data-aos="fade-down" class="inline-block py-2">
                            <div
                                class="text-body-sm rounded-md tracking-wider text-primary-900 font-semibold px-3 py-1.5 bg-accent-500/30">
                                <i class="mr-1 fa-regular fa-circle-check"></i>Telah dipercaya 5,000+ UMKM Indonesia
                            </div>
                        </div>

                        <!-- Heading & Deskripsi -->
                        <h1 class="py-2 font-bold leading-tight text-display lg:text-5xl">
                            Mulai Perjalanan
                            <span
                                class="text-transparent bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text">
                                Bisnis Anda Hari Ini.
                            </span>
                        </h1>
                        <p class="py-2 text-ink-700 text-body-lg">
                            Sistem kasir pintar yang tumbuh bersama bisnis Anda. Kelola stok, laporan, dan pelanggan
                            dalam satu aplikasi yang mudah digunakan.
                        </p>

                        <!-- Gambar Screen Hero -->
                        <div class="w-full">
                            <img src="{{ asset('img/hero-screen.png') }}" class="w-full rounded-lg" alt="Hero Screen">
                        </div>
                    </div>

                    <!-- Benefit Badges -->
                    <div class="flex flex-wrap gap-4">
                        <div class="flex-1 px-6 py-3 rounded-md bg-primary-600/10 min-w-[200px]">
                            <div class="flex items-center justify-center">
                                <i class="mr-3 text-[20px] fa-solid fa-gauge-simple-high"></i>
                                <div class="flex-col">
                                    <div class="font-bold text-body-base">Setup Cepat</div>
                                    <div class="font-semibold text-body-sm">Kurang dari 5 Menit</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 px-6 py-3 rounded-md bg-primary-600/10 min-w-[200px]">
                            <div class="flex items-center justify-center">
                                <i class="mr-3 text-[20px] fa-solid fa-headset"></i>
                                <div class="flex-col">
                                    <div class="font-bold text-body-base">Support 24/7</div>
                                    <div class="font-semibold text-body-sm">Bantuan Kapan Saja</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN -->
                <div class="w-full h-full rounded-lg">
                    <div
                        class="flex flex-col justify-start h-full p-8 border rounded-lg shadow-md bg-surface-0 border-border-200">
                        <div class="w-full space-y-4">
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="text-body-sm font-bold text-primary-600 uppercase tracking-wider bg-primary-100 px-2.5 py-1 rounded-md">
                                    Langkah 2 dari 2
                                </span>
                                <span class="font-semibold text-body-sm text-ink-400">Informasi Akun</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full h-2 mb-6 overflow-hidden rounded-full bg-surface-100">
                                <div class="h-full transition-all duration-500 rounded-full bg-primary-600"
                                    style="width: 100%"></div>
                            </div>

                            <div class="text-h2 text-ink-900">Siapkan Bisnis Anda</div>
                            <div class="mt-1 font-semibold text-ink-700 text-body-base">Beri tahu kami sedikit tentang
                                usaha Anda.</div>

                            <div x-data="{ isSubmitting: false }">
                                <form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data"
                                    class="space-y-5" @submit="isSubmitting = true">
                                    @csrf
                                    <div class="space-y-4">
                                        <div x-data="{ imgPreview: null, errorMessage: '' }">
                                            <x-input-label value="Logo Bisnis / Toko (Opsional)"
                                                class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />

                                            <div
                                                class="flex items-center gap-4 p-4 transition-all border border-dashed rounded-lg border-border-200 bg-surface-100 hover:bg-surface-100/50">
                                                <div
                                                    class="flex items-center justify-center flex-shrink-0 w-16 h-16 overflow-hidden border rounded-md text-ink-400 bg-surface-0 border-border-200">
                                                    <template x-if="imgPreview">
                                                        <img :src="imgPreview" class="object-cover w-full h-full">
                                                    </template>
                                                    <template x-if="!imgPreview">
                                                        <i class="text-xl text-ink-400 fa-solid fa-image"></i>
                                                    </template>
                                                </div>

                                                <div class="flex-1">
                                                    <input type="file" name="img_logo" accept="image/*"
                                                        @change="
                                                            const file = $event.target.files[0];
                                                            const maxSize = 2 * 1024 * 1024;

                                                            errorMessage = '';

                                                            if (file) {
                                                                if (file.size > maxSize) {
                                                                    errorMessage = 'Ukuran gambar terlalu besar! Maksimal 2MB.';
                                                                    $event.target.value = '';
                                                                    imgPreview = null;
                                                                    return;
                                                                }

                                                                const reader = new FileReader();
                                                                reader.onload = (e) => { imgPreview = e.target.result; };
                                                                reader.readAsDataURL(file);
                                                            } else {
                                                                imgPreview = null;
                                                            }
                                                        "
                                                        class="block w-full cursor-pointer text-body-sm text-ink-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-body-sm file:font-black file:bg-primary-600/10 file:text-primary-600 hover:file:bg-primary-600/20" />
                                                    <p class="mt-1 text-[10px] text-ink-400">Format: JPG, PNG, atau
                                                        WEBP. Maksimal 2MB.</p>
                                                </div>
                                            </div>

                                            <template x-if="errorMessage">
                                                <p
                                                    class="mt-1.5 ml-1 text-body-sm font-bold text-semantic-danger flex items-center gap-1">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                    <span x-text="errorMessage"></span>
                                                </p>
                                            </template>

                                            @error('img_logo')
                                                <p class="mt-1 ml-1 font-bold text-body-sm text-semantic-danger">
                                                    {{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Nama Bisnis -->
                                        <div>
                                            <x-input-label value="Nama Bisnis / Toko"
                                                class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                            <x-text-input name="name"
                                                class="block w-full transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                                required placeholder="Contoh: Kopi Senja Utama" />
                                        </div>

                                        <div>
                                            <x-input-label value="Tipe / Jenis Bisnis"
                                                class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                            <select name="business_type" required
                                                class="w-full font-medium transition-all rounded-lg shadow-sm text-ink-900 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10">
                                                <option value="" disabled selected>Pilih jenis operasional bisnis
                                                    Anda</option>
                                                <option value="F&B / Resto / Cafe">Makanan & Minuman (F&B / Kafe /
                                                    Resto)</option>
                                                <option value="Retail / Toko Pakaian">Retail (Toko Baju, Sepatu,
                                                    Aksesoris)</option>
                                                <option value="Minimarket / Sembako">Dagang / Kelontong / Toko Sembako
                                                </option>
                                                <option value="Jasa / Service">Penyedia Jasa (Laundry, Salon, Barber)
                                                </option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>

                                        <!-- Email & WA Grid -->
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <x-input-label value="Email Bisnis"
                                                    class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                                <x-text-input type="email" name="email"
                                                    class="w-full transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                                    required placeholder="toko@bisnis.com" />
                                            </div>
                                            <div>
                                                <x-input-label value="Nomor WhatsApp"
                                                    class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                                <x-text-input name="phone"
                                                    class="w-full transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                                    required placeholder="0812XXXXXXXX" />
                                            </div>
                                        </div>

                                        <!-- Alamat Toko -->
                                        <div>
                                            <x-input-label value="Alamat Toko"
                                                class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                            <textarea name="address" rows="3"
                                                class="w-full transition-all rounded-lg shadow-sm text-ink-900 placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                                placeholder="Alamat lengkap outlet Anda..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-2">
                                        <button type="submit" ::disabled="isSubmitting"
                                            class="w-full flex justify-center items-center py-4 bg-primary-600 hover:bg-primary-900 text-white font-semibold rounded-lg text-body-lg shadow-lg shadow-primary-100 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                                            <svg x-show="isSubmitting"
                                                class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                style="display: none;">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span
                                                x-text="isSubmitting ? 'Menyiapkan Dashboard...' : 'Selesaikan & Masuk ke Dashboard'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Cancel Button -->
                            <div class="pt-4 text-center">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-1 font-semibold transition-colors text-body-sm text-ink-400 hover:text-semantic-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                        </svg>
                                        Batal & Keluar
                                    </button>
                                </form>
                            </div>

                            <div class="pt-6 mt-8 text-center border-t border-border-200">
                                <p class="font-medium text-body-sm text-ink-400">
                                    Sudah memiliki akun?
                                    <a href="{{ route('login') }}"
                                        class="ml-1 font-bold text-primary-600 hover:underline decoration-2 underline-offset-4">Masuk</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="bg-primary-100/50">
            <div class="grid grid-cols-2 gap-8 px-6 py-12 mx-auto max-w-7xl md:grid-cols-12">
                <div class="col-span-2 md:col-span-3">
                    <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
                        <div class="flex items-center mb-4">
                            <img src="{{ asset('img/growpos_logo.png') }}" class="w-10" alt="GrowPOS Logo">
                            <div class="ml-1 font-bold text-body-sm text-primary-500">GrowPOS</div>
                        </div>
                    </a>
                    <p class="text-body-sm text-ink-700">
                        Solusi Kasir Digital No.1 di Indonesia untuk UMKM yang visioner.
                    </p>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <div class="mb-4 font-bold text-body-sm text-primary-500">Perusahaan</div>
                    <div class="space-y-3 font-medium text-body-sm text-ink-700">
                        <a href="#" class="block transition hover:text-primary-600 hover:translate-x-1">Tentang
                            Kami</a>
                        <a href="#" class="block transition hover:text-primary-600 hover:translate-x-1">Pusat
                            Bantuan</a>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <div class="mb-4 font-bold text-body-sm text-primary-500">Legal</div>
                    <div class="space-y-3 font-medium text-body-sm text-ink-700">
                        <a href="#"
                            class="block transition hover:text-primary-600 hover:translate-x-1">Kebijakan Privasi</a>
                        <a href="#" class="block transition hover:text-primary-600 hover:translate-x-1">Syarat &
                            Ketentuan</a>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <div class="mb-4 font-bold text-body-sm text-primary-500">Ikuti Kami</div>
                    <div class="flex space-x-3 text-xl text-primary-600">
                        <div class="flex items-center justify-center rounded-full bg-primary-600/25 w-[36px] h-[36px]">
                            <i class="fa-solid fa-earth-americas"></i>
                        </div>
                        <div class="items-center justify-center flex rounded-full bg-primary-600/25 w-[36px] h-[36px]">
                            <i class="fa-solid fa-share-nodes"></i>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="border-border-200">
            <div class="px-6 py-5 mx-auto text-center text-body-sm text-ink-700 max-w-7xl md:text-left">
                &copy; {{ date('Y') }} GrowPOS Indonesia. Solusi Kasir Digital UMKM.
            </div>
        </footer>
    </div>
</x-guest-layout>
