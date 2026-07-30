<x-guest-layout>
    {{-- Container Utama Full Screen --}}
    <div class="grid min-h-screen grid-cols-1 overflow-hidden md:grid-cols-2">

        {{-- Bagian Kiri (Hijau/Branding) --}}
        <div class="w-full px-8 py-12 bg-[#16805F] flex flex-col justify-between md:px-16">
            <div class="flex-grow space-y-8">
                {{-- Logo --}}
                <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/growpos-white.png') }}" class="w-16 h-auto" alt="GrowPOS Logo">
                        <span class="text-3xl font-bold text-white">GrowPOS</span>
                    </div>
                </a>

                {{-- Headline --}}
                <h1 class="text-4xl font-extrabold leading-tight text-white lg:text-5xl">
                    Berdayakan UMKM Anda menuju pertumbuhan digital.
                </h1>

                {{-- Sub-headline --}}
                <p class="max-w-xl text-lg text-white/90">
                    Solusi kasir pintar yang didesain khusus untuk lanskap bisnis Indonesia yang dinamis dan berkembang.
                </p>
            </div>

            {{-- Testimonial Card --}}
            <div class="max-w-md p-6 mt-12 border shadow-lg rounded-2xl border-white/20 bg-white/10 backdrop-blur-sm">
                <div class="flex gap-1 mb-4 text-xl text-amber-400">★ ★ ★ ★ ★</div>
                <blockquote class="mb-4 text-lg italic leading-relaxed text-white">
                    "GrowPOS membantu pencatatan stok kami menjadi jauh lebih akurat dan profesional. Transaksi jadi
                    lebih cepat!"
                </blockquote>
                <p class="font-semibold text-white">
                    — Budi Santoso, <span class="font-normal opacity-80">Pemilik Warung Sejahtera</span>
                </p>
            </div>
        </div>

        {{-- Bagian Kanan (Form Login) --}}
        <div class="flex flex-col justify-center w-full px-8 py-12 bg-white md:px-12 lg:px-20">

            {{-- Session Status (Pesan Sukses/Error dari Laravel) --}}
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <div class="mb-10 space-y-3">
                <h2 class="text-4xl font-bold text-gray-900">Selamat Datang</h2>
                <p class="text-lg text-gray-600">Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            {{-- Tombol Google --}}
            <a href="#" class="block mb-6">
                <div
                    class="border-2 border-[#218665]/30 hover:bg-[#5FBC97]/10 transition duration-300 bg-[#218665]/5 py-3.5 space-x-3 rounded-xl items-center flex justify-center w-full">
                    <img src="https://authjs.dev/img/providers/google.svg" alt="Google" class="w-5 h-5">
                    <span class="text-base font-semibold text-[#16805F]">Masuk dengan Google</span>
                </div>
            </a>

            {{-- Pembatas (Or Email) --}}
            <div class="relative flex items-center justify-center w-full mb-8">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-4 text-xs font-semibold tracking-wider uppercase text-slate-400">
                    atau email
                </span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            {{-- Reusable Alpine.js Store untuk Form --}}
            <div x-data="{
                showPassword: false,
                isSubmitting: false,
                capsLockOn: false
            }" @keydown.window="capsLockOn = $event.getModifierState('CapsLock')">

                <form method="POST" action="{{ route('login') }}" class="space-y-6" @submit="isSubmitting = true">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" value="Alamat Email"
                            class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                        <x-text-input id="email"
                            class="block w-full px-4 py-3 transition-all border-gray-200 bg-gray-50 focus:border-[#5FBC97] focus:ring-[#5FBC97] focus:bg-white rounded-xl"
                            type="email" name="email" :value="old('email')" required autofocus
                            placeholder="nama@bisnis.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5 ml-1">
                            <x-input-label for="password" value="Kata Sandi"
                                class="text-sm font-medium text-gray-700" />
                            @if (Route::has('password.request'))
                                {{-- Ganti 'text-brand' dengan warna hex jika belum dikonfigurasi di tailwind.config.js --}}
                                <a class="text-sm font-bold text-[#16805F] hover:text-[#00513A]"
                                    href="{{ route('password.request') }}">Lupa Sandi?</a>
                            @endif
                        </div>

                        <div class="relative">
                            <x-text-input id="password"
                                class="block w-full px-4 py-3 pr-12 transition-all border-gray-200 bg-gray-50 focus:border-[#5FBC97] focus:ring-[#5FBC97] focus:bg-white rounded-xl"
                                ::type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••" />

                            <!-- Tombol Mata (Show/Hide) -->
                            <button type="button"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#16805F]"
                                @click="showPassword = !showPassword" tabindex="-1">

                                {{-- Icon Mata (Heroicons) --}}
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7s" />
                                </svg>

                                {{-- Icon Mata Coret --}}
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>

                        <!-- Peringatan Caps Lock -->
                        <div x-show="capsLockOn" class="flex items-center mt-2.5 ml-1 text-sm text-amber-600"
                            style="display: none;">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Caps Lock Anda menyala
                        </div>

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between ml-1">
                        <div class="flex items-center">
                            {{-- Ganti 'text-brand' dan 'focus:ring-...' dengan warna hex jika belum dikonfigurasi --}}
                            <input id="remember_me" type="checkbox"
                                class="w-4 h-4 border-gray-300 rounded text-[#16805F] focus:ring-[#5FBC97]"
                                name="remember">
                            <label for="remember_me" class="text-sm font-medium text-gray-600 select-none ms-2.5">
                                Ingat saya di perangkat ini
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" ::disabled="isSubmitting"
                            class="w-full flex justify-center items-center py-4 bg-[#00513A] hover:bg-[#006C4E] text-white font-bold rounded-2xl text-lg shadow-md hover:shadow-lg transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">

                            <!-- Spinner Animasi Loading -->
                            <svg x-show="isSubmitting" class="w-5 h-5 mr-3 animate-spin text-white/80"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>

                            <span x-text="isSubmitting ? 'Memproses Masuk...' : 'Masuk Sekarang'"></span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Footer (Daftar & Security) --}}
            <div class="mt-16 text-center">
                <p class="text-base font-medium text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                        class="font-bold text-[#16805F] hover:underline decoration-2 underline-offset-4">
                        Daftar Gratis
                    </a>
                </p>
                <div class="flex items-center justify-center gap-2.5 pt-6 text-gray-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <p class="text-sm">Koneksi Aman dan Terenkripsi Standar Industri</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
