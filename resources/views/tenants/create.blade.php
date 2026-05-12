<x-guest-layout>
    <div class="mb-8">
        <div class="flex items-center mb-2 space-x-2">
            <span class="px-2 py-1 bg-blue-100 text-blue-600 text-[10px] font-black uppercase rounded-md">Langkah
                Terakhir</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Siapkan Bisnis Anda</h1>
        <p class="mt-1 text-gray-500">Beri tahu kami sedikit tentang usaha Anda.</p>
    </div>

    <form action="{{ route('tenants.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="space-y-4">
            <div>
                <x-input-label value="Nama Bisnis / Toko" class="mb-1 ml-1 text-gray-600" />
                <x-text-input name="name" class="w-full border-gray-200 bg-gray-50 rounded-2xl" required
                    placeholder="Contoh: Kopi Senja Utama" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <x-input-label value="Email Bisnis" class="mb-1 ml-1 text-gray-600" />
                    <x-text-input type="email" name="email" class="w-full border-gray-200 bg-gray-50 rounded-2xl"
                        required />
                </div>
                <div>
                    <x-input-label value="Nomor WhatsApp" class="mb-1 ml-1 text-gray-600" />
                    <x-text-input name="phone" class="w-full border-gray-200 bg-gray-50 rounded-2xl" required
                        placeholder="0812..." />
                </div>
            </div>

            <div>
                <x-input-label value="Alamat Toko" class="mb-1 ml-1 text-gray-600" />
                <textarea name="address" rows="3"
                    class="w-full transition-all border-gray-200 bg-gray-50 rounded-2xl focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Alamat lengkap outlet Anda..."></textarea>
            </div>


        </div>
    </form>

    <div class="pt-4 space-y-3">
        <x-primary-button
            class="justify-center w-full py-4 text-base bg-blue-600 shadow-lg hover:bg-blue-700 rounded-2xl shadow-blue-200">
            Selesaikan & Masuk ke Dashboard
        </x-primary-button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-sm font-bold text-center text-gray-400 transition-colors hover:text-red-500">
                Batal & Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
