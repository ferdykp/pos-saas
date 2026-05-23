<x-guest-layout>
    <!-- Multi-Step Tracking Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-xs font-bold text-blue-600 uppercase tracking-wider bg-blue-50 px-2.5 py-1 rounded-md">Langkah
                2 dari 2</span>
            <span class="text-xs font-semibold text-gray-400">Informasi Bisnis</span>
        </div>
        <div class="w-full h-2 overflow-hidden bg-gray-100 rounded-full">
            <div class="h-full transition-all duration-500 bg-blue-600 rounded-full" style="width: 100%"></div>
        </div>
    </div>

    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Siapkan Bisnis Anda</h1>
        <p class="mt-1.5 text-sm text-gray-500">Beri tahu kami sedikit tentang usaha Anda.</p>
    </div>

    <div x-data="{ isSubmitting: false }">
        <form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
            @submit="isSubmitting = true">
            @csrf
            <div class="space-y-4">
                <div x-data="{ imgPreview: null }">
                    <x-input-label value="Logo Bisnis / Toko (Opsional)"
                        class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />

                    <div
                        class="flex items-center gap-4 p-4 transition-all border border-gray-200 border-dashed bg-gray-50 rounded-2xl hover:bg-gray-100/50">
                        <div
                            class="flex items-center justify-center flex-shrink-0 w-16 h-16 overflow-hidden text-gray-400 bg-white border border-gray-100 rounded-xl">
                            <template x-if="imgPreview">
                                <img :src="imgPreview" class="object-cover w-full h-full">
                            </template>
                            <template x-if="!imgPreview">
                                <i class="text-xl text-gray-300 fa-solid fa-image"></i>
                            </template>
                        </div>

                        <div class="flex-1">
                            <input type="file" name="img_logo" accept="image/*"
                                @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imgPreview = e.target.result; }; reader.readAsDataURL(file); }"
                                class="block w-full text-xs text-gray-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100" />
                            <p class="mt-1 text-[10px] text-gray-400">Format: JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                        </div>
                    </div>
                    @error('img_logo')
                        <p class="mt-1 ml-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Nama Bisnis -->
                <div>
                    <x-input-label value="Nama Bisnis / Toko" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                    <x-text-input name="name"
                        class="w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                        required placeholder="Contoh: Kopi Senja Utama" />
                </div>

                <div>
                    <x-input-label value="Tipe / Jenis Bisnis" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                    <select name="business_type" required
                        class="w-full font-medium text-gray-900 transition-all border-gray-200 shadow-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl">
                        <option value="" disabled selected>Pilih jenis operasional bisnis Anda</option>
                        <option value="F&B / Resto / Cafe">Makanan & Minuman (F&B / Kafe / Resto)</option>
                        <option value="Retail / Toko Pakaian">Retail (Toko Baju, Sepatu, Aksesoris)</option>
                        <option value="Minimarket / Sembako">Dagang / Kelontong / Toko Sembako</option>
                        <option value="Jasa / Service">Penyedia Jasa (Laundry, Salon, Barber)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- Email & WA Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label value="Email Bisnis" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                        <x-text-input type="email" name="email"
                            class="w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                            required placeholder="toko@bisnis.com" />
                    </div>
                    <div>
                        <x-input-label value="Nomor WhatsApp" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                        <x-text-input name="phone"
                            class="w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                            required placeholder="0812XXXXXXXX" />
                    </div>
                </div>

                <!-- Alamat Toko -->
                <div>
                    <x-input-label value="Alamat Toko" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                    <textarea name="address" rows="3"
                        class="w-full text-gray-900 placeholder-gray-400 transition-all border-gray-200 shadow-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                        placeholder="Alamat lengkap outlet Anda..."></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" ::disabled="isSubmitting"
                    class="w-full flex justify-center items-center py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-base shadow-lg shadow-blue-200 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                    <svg x-show="isSubmitting" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'Menyiapkan Dashboard...' : 'Selesaikan & Masuk ke Dashboard'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Cancel Button -->
    <div class="pt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center justify-center gap-1 text-sm font-semibold text-gray-400 transition-colors hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                Batal & Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
