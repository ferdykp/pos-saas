{{--
    Footer Component
    Usage: <x-landing.footer />
--}}
<footer class="bg-brand-mint/20">
    <div class="grid grid-cols-2 gap-8 px-6 py-12 mx-auto max-w-7xl md:grid-cols-12">
        <div class="col-span-2 md:col-span-3">
            <div class="flex items-center mb-4">
                <img src="{{ asset('img/growpos_logo.png') }}" class="w-10" alt="GrowPOS Logo">
                <div class="ml-1 text-sm font-bold text-brand-light">GrowPOS</div>
            </div>
            <p class="text-sm text-gray-600">
                Solusi Kasir Digital No.1 di Indonesia untuk UMKM yang visioner.
            </p>
        </div>

        <x-landing.footer-column title="Produk" :links="['Fitur POS', 'Inventaris', 'Laporan Keuangan', 'Hardware Kasir']" />
        <x-landing.footer-column title="Perusahaan" :links="['Tentang Kami', 'Karir', 'Blog', 'Kontak']" />
        <x-landing.footer-column title="Bantuan" :links="['Pusat Bantuan', 'Kebijakan Privasi', 'Syarat dan Ketentuan', 'Keamanan Data']" />
    </div>
    <hr class="border-brand/10">
    <div class="px-6 py-5 mx-auto text-sm text-center text-gray-600 max-w-7xl md:text-left">
        &copy; {{ date('Y') }} GrowPOS Indonesia. Solusi Kasir Digital UMKM.
    </div>
</footer>
