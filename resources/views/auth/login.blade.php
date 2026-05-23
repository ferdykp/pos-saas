<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang</h1>
        <p class="mt-1 text-gray-500">Masuk untuk mengelola bisnis Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Inisialisasi Alpine.js untuk mengontrol state halaman -->
    <div x-data="{
        showPassword: false,
        isSubmitting: false,
        capsLockOn: false
    }">
        <form method="POST" action="{{ route('login') }}" class="space-y-5" @submit="isSubmitting = true">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Alamat Email" class="mb-1 ml-1 text-gray-600" />
                <x-text-input id="email"
                    class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl"
                    type="email" name="email" :value="old('email')" required autofocus placeholder="nama@bisnis.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1 ml-1">
                    <x-input-label for="password" value="Kata Sandi" class="text-gray-600" />
                    @if (Route::has('password.request'))
                        <a class="text-xs font-bold text-blue-600 hover:text-blue-700"
                            href="{{ route('password.request') }}">Lupa Sandi?</a>
                    @endif
                </div>

                <!-- Wrapper untuk Show/Hide Password -->
                <div class="relative">
                    <x-text-input id="password"
                        class="block w-full pr-12 transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl"
                        ::type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••"
                        @keydown="capsLockOn = $event.getModifierState('CapsLock')" />

                    <!-- Tombol Mata (Show/Hide) -->
                    <button type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600"
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
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                <!-- Peringatan Caps Lock -->
                <div x-show="capsLockOn" class="flex items-center mt-2 ml-1 text-xs text-amber-600"
                    style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    Peringatan: Caps Lock Anda menyala!
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center ml-1">
                <input id="remember_me" type="checkbox"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" name="remember">
                <span class="text-sm font-medium text-gray-500 select-none ms-3">Ingat saya di perangkat ini</span>
            </div>

            <!-- Submit Button dengan Loading State -->
            <div class="pt-2">
                <button type="submit" ::disabled="isSubmitting"
                    class="w-full flex justify-center items-center py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-base shadow-lg shadow-blue-200 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">

                    <!-- Spinner Animasi Loading -->
                    <svg x-show="isSubmitting" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span x-text="isSubmitting ? 'Memproses...' : 'Masuk Sekarang'"></span>
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 text-center">
        <p class="text-sm font-medium text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}"
                class="font-bold text-blue-600 hover:underline decoration-2 underline-offset-4">Daftar Gratis</a>
        </p>
    </div>
</x-guest-layout>
