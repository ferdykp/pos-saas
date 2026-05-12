<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Akun</h1>
        <p class="mt-1 text-gray-500">Mulai transformasi digital UMKM Anda</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nama Lengkap" class="mb-1 ml-1 text-gray-600" />
            <x-text-input id="name"
                class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl" type="text"
                name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email Bisnis" class="mb-1 ml-1 text-gray-600" />
            <x-text-input id="email"
                class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl" type="email"
                name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" value="Kata Sandi" class="mb-1 ml-1 text-gray-600" />
                <x-text-input id="password"
                    class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl"
                    type="password" name="password" required />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi" class="mb-1 ml-1 text-gray-600" />
                <x-text-input id="password_confirmation"
                    class="block w-full transition-all border-gray-200 bg-gray-50 focus:bg-white rounded-2xl"
                    type="password" name="password_confirmation" required />
            </div>
            <div class="col-span-2">
                <x-input-error :messages="$errors->get('password')" />
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
        </div>

        <div class="pt-4">
            <x-primary-button
                class="w-full justify-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl text-base shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">
                Buat Akun Sekarang
            </x-primary-button>
        </div>
    </form>

    <div class="pt-6 mt-8 text-center border-t border-gray-100">
        <p class="text-sm font-medium text-gray-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}"
                class="font-bold text-blue-600 hover:underline decoration-2 underline-offset-4">Masuk</a>
        </p>
    </div>
</x-guest-layout>
