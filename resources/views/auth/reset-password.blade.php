<x-guest-layout>
    @section('title', 'Atur Ulang Kata Sandi')

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
                    Atur Ulang Kata Sandi Akun Anda.
                </h1>

                <p class="max-w-xl font-normal text-md text-white/90">
                    Buat kata sandi baru yang kuat dan aman untuk kembali mengelola seluruh operasional kasir bisnis
                    Anda.
                </p>
            </div>

            <div class="max-w-md p-6 mt-12 border rounded-lg shadow-lg border-white/20 bg-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-3 text-white">
                    <i class="text-2xl fa-solid fa-shield-halved"></i>
                    <div>
                        <p class="font-semibold">Saran Keamanan Sandi</p>
                        <p class="text-xs text-white/80">Gunakan kombinasi minimal 8 karakter dengan campuran huruf,
                            angka, dan simbol.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Kanan (Form Reset Password) --}}
        <div class="flex flex-col justify-center w-full px-8 py-12 bg-surface-0 md:px-12 lg:px-20">
            <div class="w-full max-w-md mx-auto">

                <div
                    class="flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-primary-50 text-primary-600">
                    <i class="text-2xl fa-solid fa-key"></i>
                </div>

                <h2 class="mb-2 text-2xl font-bold font-heading text-ink-900">Buat Kata Sandi Baru</h2>
                <p class="mb-6 text-sm leading-relaxed font-body text-ink-700">
                    Silakan masukkan alamat email terdaftar dan kata sandi baru yang ingin Anda gunakan.
                </p>

                <div x-data="{
                    isSubmitting: false,
                    showPassword: false,
                    showConfirmPassword: false
                }">
                    <form method="POST" action="{{ route('password.store') }}" class="space-y-4"
                        @submit="isSubmitting = true">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                                    type="email" name="email" :value="old('email', $request->email)" required autofocus
                                    autocomplete="username" placeholder="nama@emailanda.com" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <x-input-label for="password" value="Kata Sandi Baru"
                                class="mb-1.5 ml-1 text-sm font-medium text-ink-700" />
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-ink-400">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <x-text-input id="password"
                                    class="block w-full pl-10 pr-10 transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                    ::type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                    placeholder="Minimal 8 karakter" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-400 hover:text-ink-700">
                                    <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru"
                                class="mb-1.5 ml-1 text-sm font-medium text-ink-700" />
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-ink-400">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <x-text-input id="password_confirmation"
                                    class="block w-full pl-10 pr-10 transition-all rounded-lg placeholder-ink-400 border-border-200 bg-surface-100 focus:bg-surface-0 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10"
                                    ::type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Ulangi kata sandi baru" />
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-400 hover:text-ink-700">
                                    <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
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
                                <i x-show="!isSubmitting" class="mr-2 text-xs fa-solid fa-circle-check"></i>
                                <span
                                    x-text="isSubmitting ? 'Memperbarui Kata Sandi...' : 'Simpan Kata Sandi Baru'"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Navigation Back to Login -->
                <div class="pt-6 mt-6 text-center border-t border-border-200">
                    <p class="text-xs font-medium text-ink-400">
                        Batal memperbarui kata sandi?
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
