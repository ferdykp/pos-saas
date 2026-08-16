<x-guest-layout>
    @section('title', 'Halaman Register')

    <div class="bg-primary-100/40">
        <nav class="flex justify-between py-3 mx-16">
            <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('img/growpos_logo.png') }}" class="w-14" alt="">
                    <div class="text-3xl font-bold text-primary-600">GrowPOS</div>
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
                        <div class="w-full my-4">
                            <img src="{{ asset('img/hero-screen.png') }}" class="w-full rounded-lg" alt="Hero Screen">
                        </div>
                    </div>

                    <!-- Benefit Badges -->
                    <div class="flex flex-wrap gap-4 pt-4">
                        <div
                            class="flex-1 px-6 py-3 rounded-md bg-primary-100/80 border-[1px] border-primary-500 shadow-md min-w-[200px]">
                            <div class="flex items-center justify-center text-primary-900">
                                <i class="mr-3 text-[20px] fa-solid fa-gauge-simple-high"></i>
                                <div class="flex-col ">
                                    <div class="font-bold text-body-base ">Setup Cepat</div>
                                    <div class="font-semibold text-body-sm">Kurang dari 5 Menit</div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex-1 px-6 py-3 rounded-md bg-primary-100/80 border-[1px] border-primary-500 shadow-md min-w-[200px]">
                            <div class="flex items-center justify-center text-primary-900">
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
                    <!-- PENTING: Gunakan 'justify-start' agar konten mulai dari ATAS -->
                    <div
                        class="flex flex-col justify-start h-full p-8 border rounded-lg shadow-md bg-surface-0 border-border-200">
                        <div class="w-full space-y-4">
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="text-body-sm font-bold text-primary-600 uppercase tracking-wider bg-primary-100 px-2.5 py-1 rounded-md">
                                    Langkah 1 dari 2
                                </span>
                                <span class="font-semibold text-body-sm text-ink-400">Informasi Akun</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full h-2 mb-6 overflow-hidden rounded-full bg-surface-100">
                                <div class="h-full transition-all duration-500 rounded-full bg-primary-600"
                                    style="width: 50%"></div>
                            </div>

                            <div class="text-h2 text-ink-900">Daftar Akun Baru</div>
                            <div class="mt-1 font-semibold text-ink-700 text-body-base">Gratis coba fitur premium selama
                                14
                                Hari</div>

                            <!-- Inisialisasi Alpine.js -->
                            <div x-data="{
                                showPassword: false,
                                password: '',
                                password_confirmation: '',
                                isSubmitting: false,
                                get isDigitValid() { return this.password.length >= 8 },
                                get isMatch() { return this.password === this.password_confirmation && this.password_confirmation !== '' }
                            }">

                                <!-- Tombol Register dengan Google -->
                                <a href="{{ route('auth.google') }}" class="block w-full mb-6 no-underline">
                                    <div
                                        class="border-2 border-primary-600/30 hover:bg-primary-500/10 transition duration-300 bg-primary-600/5 py-3.5 space-x-3 rounded-md items-center flex justify-center w-full cursor-pointer">
                                        <img src="https://authjs.dev/img/providers/google.svg" alt="Google"
                                            class="w-5 h-5 pointer-events-none">
                                        <span
                                            class="font-semibold pointer-events-none text-body-base text-primary-600">Daftar
                                            Instan dengan Google</span>
                                    </div>
                                </a>

                                <!-- Pembatas OR -->
                                <div class="relative flex items-center justify-center w-full mb-6">
                                    <div class="flex-grow border-t border-border-200"></div>
                                    <span
                                        class="flex-shrink mx-4 font-semibold tracking-wider uppercase text-body-sm text-ink-400">
                                        atau daftar dengan email
                                    </span>
                                    <div class="flex-grow border-t border-border-200"></div>
                                </div>
                                <form method="POST" action="{{ route('register') }}" class="space-y-5"
                                    @submit="isSubmitting = true">
                                    @csrf

                                    <!-- Nama Lengkap -->
                                    <div>
                                        <x-input-label for="name" value="Nama Lengkap"
                                            class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                        <x-text-input id="name"
                                            class="block w-full transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                            type="text" name="name" :value="old('name')" required autofocus
                                            placeholder="Masukkan nama lengkap Anda" />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-body-sm" />
                                    </div>

                                    <!-- Email Pribadi -->
                                    <div>
                                        <x-input-label for="email" value="Email Pribadi"
                                            class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                        <x-text-input id="email"
                                            class="block w-full transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                            type="email" name="email" :value="old('email')" required
                                            placeholder="nama@email.com" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-body-sm" />
                                    </div>

                                    <!-- Password Fields -->
                                    <div class="space-y-4">
                                        <!-- Kata Sandi -->
                                        <div>
                                            <x-input-label for="password" value="Kata Sandi"
                                                class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                            <div class="relative">
                                                <x-text-input id="password"
                                                    class="block w-full pr-12 transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                                    ::type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••"
                                                    x-model="password" />

                                                <button type="button"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-4 transition-colors text-ink-400 hover:text-ink-700"
                                                    @click="showPassword = !showPassword">
                                                    <!-- Icon Mata Terbuka -->
                                                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                    <!-- Icon Mata Tertutup -->
                                                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="w-5 h-5" style="display: none;">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <!-- Digit Validator Indicator -->
                                            <div class="flex items-center mt-2 ml-1 transition-colors text-body-sm"
                                                :class="isDigitValid ? 'text-semantic-success' : 'text-ink-400'"
                                                x-show="password.length > 0">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        :d="isDigitValid ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                                                </svg>
                                                <span
                                                    x-text="isDigitValid ? 'Minimal 8 digit terpenuhi' : 'Password harus minimal 8 digit'"></span>
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Kata Sandi -->
                                        <div>
                                            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi"
                                                class="mb-1.5 ml-1 text-body-base font-medium text-ink-700" />
                                            <div class="relative">
                                                <x-text-input id="password_confirmation"
                                                    class="block w-full transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                                    type="password" name="password_confirmation" required
                                                    placeholder="••••••••" x-model="password_confirmation" />
                                            </div>
                                            <!-- Match Password Indicator -->
                                            <div class="flex items-center mt-2 ml-1 transition-colors text-body-sm"
                                                :class="isMatch ? 'text-semantic-success' : 'text-semantic-danger'"
                                                x-show="password_confirmation.length > 0">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        :d="isMatch ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                                                </svg>
                                                <span
                                                    x-text="isMatch ? 'Kata sandi cocok' : 'Kata sandi belum sama'"></span>
                                            </div>
                                        </div>

                                        <div class="col-span-2">
                                            <x-input-error :messages="$errors->get('password')" class="text-body-sm" />
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="text-body-sm" />
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
                                                x-text="isSubmitting ? 'Memproses...' : 'Lanjutkan Pendaftaran'"></span>
                                        </button>
                                    </div>
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
                        <a href="#" class="block transition hover:text-primary-600 hover:translate-x-1">
                            Pusat Bantuan</a>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <div class="mb-4 font-bold text-body-sm text-primary-500">Legal</div>
                    <div class="space-y-3 font-medium text-body-sm text-ink-700">
                        <a href="#"
                            class="block transition hover:text-primary-600 hover:translate-x-1">Kebijakan
                            Privasi</a>
                        <a href="#" class="block transition hover:text-primary-600 hover:translate-x-1">
                            Syarat & Ketentuan</a>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <div class="mb-4 font-bold text-body-sm text-primary-500">Ikuti Kami</div>
                    <div class="flex space-x-3 text-xl text-primary-600">
                        <div
                            class="flex items-center justify-center rounded-full border-[1px] border-primary-500 bg-primary-600/15 w-[36px] h-[36px]">
                            <i class="fa-solid fa-earth-americas"></i>
                        </div>
                        <div
                            class="items-center justify-center flex rounded-full bg-primary-600/15 w-[36px] h-[36px] border-[1px] border-primary-500">
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
