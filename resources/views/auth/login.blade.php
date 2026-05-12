<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang</h1>
        <p class="mt-1 text-gray-500">Masuk untuk mengelola bisnis Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Alamat Email" class="mb-1 ml-1 text-gray-600" />
            <x-text-input id="email"
                class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl" type="email"
                name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1 ml-1">
                <x-input-label for="password" value="Kata Sandi" class="text-gray-600" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-blue-600 hover:text-blue-700"
                        href="{{ route('password.request') }}">Lupa Sandi?</a>
                @endif
            </div>
            <x-text-input id="password"
                class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl"
                type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center ml-1">
            <input id="remember_me" type="checkbox"
                class="text-blue-600 border-gray-300 rounded-md shadow-sm focus:ring-blue-500" name="remember">
            <span class="text-sm font-medium text-gray-500 ms-3">Ingat saya di perangkat ini</span>
        </div>

        <div class="pt-2">
            <x-primary-button
                class="w-full justify-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl text-base shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">
                Masuk Sekarang
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm font-medium text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}"
                class="font-bold text-blue-600 hover:underline decoration-2 underline-offset-4">Daftar Gratis</a>
        </p>
    </div>
</x-guest-layout>
