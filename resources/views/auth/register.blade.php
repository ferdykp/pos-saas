<x-guest-layout>
    <nav class="flex justify-between py-3 mx-10">
        <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
            <div class="flex items-center justify-center">
                <img src="{{ asset('img/growpos_logo.png') }}" class="w-10" alt="">
                <div class="text-2xl font-bold text-brand">GrowPOS</div>
            </div>
        </a>

        <div class="flex items-center justify-center space-x-2">
            <div class="font-semibold text-md">Sudah punya akun ?</div>
            <a href="{{ route('login') }}">
                <div class="relative inline-block font-bold transition-all group text-md text-brand">
                    <span
                        class="absolute left-0 bottom-[-2px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-brand"></span>

                    Masuk
                </div>
                {{-- <x-landing.nav-link href="{{ route('login') }}" class="font-bold text-brand">Masuk</x-landing.nav-link> --}}
            </a>
        </div>
    </nav>
</x-guest-layout>
{{-- <x-guest-layout>
    <!-- Multi-Step Tracking Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-xs font-bold text-blue-600 uppercase tracking-wider bg-blue-50 px-2.5 py-1 rounded-md">Langkah
                1 dari 2</span>
            <span class="text-xs font-semibold text-gray-400">Informasi Akun</span>
        </div>
        <div class="w-full h-2 overflow-hidden bg-gray-100 rounded-full">
            <div class="h-full transition-all duration-500 bg-blue-600 rounded-full" style="width: 50%"></div>
        </div>
    </div>

    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Daftar Akun</h1>
        <p class="mt-1.5 text-sm text-gray-500">Mulai transformasi digital UMKM Anda</p>
    </div>

    <!-- Inisialisasi Alpine.js -->
    <div x-data="{
        showPassword: false,
        password: '',
        password_confirmation: '',
        isSubmitting: false,
        get isDigitValid() { return this.password.length >= 8 },
        get isMatch() { return this.password === this.password_confirmation && this.password_confirmation !== '' }
    }">
        <form method="POST" action="{{ route('register') }}" class="space-y-5" @submit="isSubmitting = true">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <x-input-label for="name" value="Nama Lengkap"
                    class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                <x-text-input id="name"
                    class="block w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                    type="text" name="name" :value="old('name')" required autofocus
                    placeholder="Masukkan nama lengkap Anda" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs" />
            </div>

            <!-- Email Pribadi -->
            <div>
                <x-input-label for="email" value="Email Pribadi"
                    class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                <x-text-input id="email"
                    class="block w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                    type="email" name="email" :value="old('email')" required placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
            </div>

            <!-- Password Fields -->
            <div class="space-y-4">
                <!-- Kata Sandi -->
                <div>
                    <x-input-label for="password" value="Kata Sandi"
                        class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <x-text-input id="password"
                            class="block w-full pr-12 placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                            ::type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••" x-model="password" />

                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 transition-colors hover:text-gray-600"
                            @click="showPassword = !showPassword">
                            <!-- Icon Mata Terbuka -->
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <!-- Icon Mata Tertutup -->
                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"
                                style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <!-- Digit Validator Indicator -->
                    <div class="flex items-center mt-2 ml-1 text-xs transition-colors"
                        :class="isDigitValid ? 'text-green-600' : 'text-gray-400'" x-show="password.length > 0">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
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
                        class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <x-text-input id="password_confirmation"
                            class="block w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                            type="password" name="password_confirmation" required placeholder="••••••••"
                            x-model="password_confirmation" />
                    </div>
                    <!-- Match Password Indicator -->
                    <div class="flex items-center mt-2 ml-1 text-xs transition-colors"
                        :class="isMatch ? 'text-green-600' : 'text-red-500'" x-show="password_confirmation.length > 0">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                :d="isMatch ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                        </svg>
                        <span x-text="isMatch ? 'Kata sandi cocok' : 'Kata sandi belum sama'"></span>
                    </div>
                </div>

                <div class="col-span-2">
                    <x-input-error :messages="$errors->get('password')" class="text-xs" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-xs" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" ::disabled="isSubmitting"
                    class="w-full flex justify-center items-center py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-base shadow-lg shadow-blue-200 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                    <svg x-show="isSubmitting" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'Memproses...' : 'Lanjutkan Pendaftaran'"></span>
                </button>
            </div>
        </form>
    </div>

    <div class="pt-6 mt-8 text-center border-t border-gray-100">
        <p class="text-sm font-medium text-gray-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}"
                class="ml-1 font-bold text-blue-600 hover:underline decoration-2 underline-offset-4">Masuk</a>
        </p>
    </div>
</x-guest-layout> --}}
