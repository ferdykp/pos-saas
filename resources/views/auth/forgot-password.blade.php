<x-guest-layout>
    @section('title', 'Lupa Kata Sandi')

    <div class="grid min-h-screen grid-cols-1 overflow-hidden md:grid-cols-2">

        {{-- Bagian Kiri (Branding GrowPOS) --}}
        <div class="flex flex-col justify-between w-full px-8 py-12 bg-primary-600 md:px-16">
            <div class="flex-grow space-y-8">
                <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/growpos_logo.png') }}" class="w-12 h-auto" alt="GrowPOS Logo">
                        <span class="text-3xl font-bold text-white">GrowPOS</span>
                    </div>
                </a>

                <h1 class="font-bold leading-tight text-white text-display lg:text-5xl">
                    Keamanan Akun Bisnis Anda Adalah Prioritas Kami.
                </h1>

                <p class="max-w-xl font-normal text-md text-white/90">
                    Atur ulang kata sandi akun GrowPOS Anda dengan mudah dan aman melalui tautan pemulihan yang
                    dikirimkan langsung ke email terdaftar Anda.
                </p>
            </div>

            <div class="max-w-md p-6 mt-12 border rounded-lg shadow-lg border-white/20 bg-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-3 text-white">
                    <i class="text-2xl fa-solid fa-key"></i>
                    <div>
                        <p class="font-semibold">Pemulihan Akun Cepat</p>
                        <p class="text-xs text-white/80">Proses reset password aman melalui verifikasi email
                            terenkripsi.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Kanan (Form Lupa Password) --}}
        <div class="flex flex-col justify-center w-full px-8 py-12 bg-surface-0 md:px-12 lg:px-20">
            <div class="w-full max-w-md mx-auto">

                <div
                    class="flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-primary-50 text-primary-600">
                    <i class="text-2xl fa-solid fa-lock-open"></i>
                </div>

                <h2 class="mb-2 text-2xl font-bold font-heading text-ink-900">Lupa Kata Sandi?</h2>
                <p class="mb-6 text-sm leading-relaxed font-body text-ink-700">
                    Tidak masalah! Masukkan alamat email yang terdaftar pada akun GrowPOS Anda, dan kami akan
                    mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                </p>

                <!-- Session Status Banner (Pesan Sukses Terkirim) -->
                @if (session('status'))
                    <div
                        class="flex items-center gap-3 p-4 mb-6 text-xs font-semibold border rounded-lg text-emerald-800 bg-emerald-50 border-emerald-200">
                        <i class="text-base fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <div x-data="{ isSubmitting: false }">
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5"
                        @submit="isSubmitting = true">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" value="Alamat Email Terdaftar"
                                class="mb-1.5 ml-1 text-sm font-medium text-ink-700" />
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-ink-400">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <x-text-input id="email"
                                    class="block w-full pl-10 transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                    type="email" name="email" :value="old('email')" required autofocus
                                    placeholder="nama@emailanda.com" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" :disabled="isSubmitting"
                                class="w-full flex justify-center items-center py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm shadow-sm transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                                <svg x-show="isSubmitting" class="w-4 h-4 mr-2 text-white animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <i x-show="!isSubmitting" class="mr-2 text-xs fa-solid fa-paper-plane"></i>
                                <span
                                    x-text="isSubmitting ? 'Mengirimkan Tautan...' : 'Kirim Tautan Reset Password'"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Navigation Back to Login -->
                <div class="pt-6 mt-8 text-center border-t border-border-200">
                    <p class="text-xs font-medium text-ink-400">
                        Ingat kata sandi Anda?
                        <a href="{{ route('login') }}"
                            class="ml-1 font-bold text-primary-600 hover:underline decoration-2 underline-offset-4">
                            <i class="mr-1 fa-solid fa-arrow-left text-[10px]"></i>Kembali ke Halaman Masuk
                        </a>
                    </p>
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>
