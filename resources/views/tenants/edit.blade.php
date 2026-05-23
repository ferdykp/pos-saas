<x-app-layout>
    <div class="max-w-2xl px-4 py-8 mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('tenants.index') }}"
                class="inline-flex items-center mb-3 text-sm font-bold text-blue-600 hover:underline">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Manajemen Tenant
            </a>
            <h1 class="text-3xl font-black tracking-tight text-gray-900">Edit Informasi Bisnis</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui profil operasional untuk outlet <span
                    class="font-bold text-gray-800">{{ $tenant->name }}</span>.</p>
        </div>

        <div x-data="{ isSubmitting: false }">
            <form action="{{ route('tenants.update', $tenant->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6" @submit="isSubmitting = true">
                @csrf
                @method('PUT')

                <div
                    class="p-8 bg-white border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.01)] rounded-[2rem] space-y-5">

                    <div x-data="{ imgPreview: '{{ $tenant->img_logo ? asset('storage/' . $tenant->img_logo) : null }}' }">
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
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </template>
                            </div>

                            <div class="flex-1">
                                <input type="file" name="img_logo" accept="image/*"
                                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imgPreview = e.target.result; }; reader.readAsDataURL(file); }"
                                    class="block w-full text-xs text-gray-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100" />
                                <p class="mt-1 text-[10px] text-gray-400">Format: JPG, PNG, atau WEBP. Maksimal 2MB.
                                    Kosongkan jika tidak diganti.</p>
                            </div>
                        </div>
                        @error('img_logo')
                            <p class="mt-1 ml-1 text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label value="Nama Bisnis / Toko"
                            class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                        <x-text-input name="name" :value="old('name', $tenant->name)"
                            class="w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                            required />
                    </div>

                    <div>
                        <x-input-label value="Tipe / Jenis Bisnis"
                            class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                        <select name="business_type" required
                            class="w-full px-4 py-3 font-medium text-gray-900 transition-all border-gray-200 shadow-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl">
                            <option value="F&B / Resto / Cafe"
                                {{ $tenant->business_type == 'F&B / Resto / Cafe' ? 'selected' : '' }}>Makanan & Minuman
                                (F&B / Kafe / Resto)</option>
                            <option value="Retail / Toko Pakaian"
                                {{ $tenant->business_type == 'Retail / Toko Pakaian' ? 'selected' : '' }}>Retail (Toko
                                Baju, Sepatu, Aksesoris)</option>
                            <option value="Minimarket / Sembako"
                                {{ $tenant->business_type == 'Minimarket / Sembako' ? 'selected' : '' }}>Dagang /
                                Kelontong / Toko Sembako</option>
                            <option value="Jasa / Service"
                                {{ $tenant->business_type == 'Jasa / Service' ? 'selected' : '' }}>Penyedia Jasa
                                (Laundry, Salon, Barber)</option>
                            <option value="Lainnya" {{ $tenant->business_type == 'Lainnya' ? 'selected' : '' }}>Lainnya
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label value="Email Bisnis" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                            <x-text-input type="email" name="email" :value="old('email', $tenant->email)"
                                class="w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                                required />
                        </div>
                        <div>
                            <x-input-label value="Nomor WhatsApp"
                                class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                            <x-text-input name="phone" :value="old('phone', $tenant->phone)"
                                class="w-full placeholder-gray-400 transition-all border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                                required />
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Alamat Toko" class="mb-1.5 ml-1 text-sm font-medium text-gray-700" />
                        <textarea name="address" rows="3" required
                            class="w-full p-4 text-gray-900 placeholder-gray-400 transition-all border-gray-200 shadow-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl"
                            placeholder="Alamat lengkap outlet Anda...">{{ old('address', $tenant->address) }}</textarea>
                    </div>
                </div>

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
                        <span x-text="isSubmitting ? 'Menyimpan Perubahan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
