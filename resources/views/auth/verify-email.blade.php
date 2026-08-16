<x-guest-layout>
    @section('title', 'Verifikasi Alamat Email')

    {{-- Alpine.js Store: Auto-Polling & Timer Cooldown 60 Detik --}}
    <div x-data="{
        timer: 60,
        canResend: false,
        timerInterval: null,
    
        checkVerification() {
            fetch('{{ route('api.check-verification') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        // Email terverifikasi! Otomatis redirect ke halaman tujuan
                        window.location.href = data.redirect_url;
                    }
                })
                .catch(error => console.error('Error checking verification:', error));
        },
    
        startCooldown() {
            this.canResend = false;
            this.timer = 60;
            if (this.timerInterval) clearInterval(this.timerInterval);
    
            this.timerInterval = setInterval(() => {
                this.timer--;
                if (this.timer <= 0) {
                    this.canResend = true;
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },
    
        init() {
            // 1. Jalankan Cooldown Timer saat halaman dimuat
            this.startCooldown();
    
            // 2. Jalankan Auto-Check status verifikasi setiap 3 detik
            setInterval(() => {
                this.checkVerification();
            }, 3000);
        }
    }" class="grid min-h-screen grid-cols-1 overflow-hidden md:grid-cols-2">

        {{-- Bagian Kiri (Branding GrowPOS) --}}
        <div class="flex flex-col justify-between w-full px-8 py-12 bg-primary-600 md:px-16">
            <div class="flex-grow space-y-8">
                <a href="/" class="inline-block transition duration-500 scale-100 hover:scale-105">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/growpos-white.png') }}" class="w-16 h-auto" alt="GrowPOS Logo">
                        <span class="text-4xl font-bold text-white">GrowPOS</span>
                    </div>
                </a>

                <h1 class="font-bold leading-tight text-white text-display lg:text-5xl">
                    Satu Langkah Lagi Menuju Bisnis Digital Anda.
                </h1>

                <p class="max-w-xl font-normal text-md text-white/90">
                    Kami telah mengirimkan tautan verifikasi resmi ke alamat email Gmail Anda untuk memastikan keamanan
                    akun.
                </p>
            </div>

            <div class="max-w-md p-6 mt-12 border rounded-lg shadow-lg border-white/20 bg-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-3 text-white">
                    <i class="text-2xl fa-solid fa-shield-halved"></i>
                    <div>
                        <p class="font-semibold">Sistem Keamanan Terenkripsi</p>
                        <p class="text-xs text-white/80">Perlindungan data akun & transaksi toko Anda adalah prioritas
                            kami.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Kanan (Verify Card) --}}
        <div class="flex flex-col justify-center w-full px-8 py-12 bg-surface-0 md:px-12 lg:px-20">
            <div class="w-full max-w-md mx-auto">

                <div
                    class="flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-primary-50 text-primary-600">
                    <i class="text-2xl fa-solid fa-envelope-circle-check"></i>
                </div>

                <h2 class="mb-2 text-2xl font-bold font-heading text-ink-900">Cek Kotak Masuk Gmail Anda</h2>
                <p class="mb-6 text-sm leading-relaxed font-body text-ink-700">
                    Terima kasih telah mendaftar! Sebelum melanjutkan, mohon verifikasi alamat email Anda dengan
                    mengeklik tautan yang kami kirimkan ke email Anda.
                </p>

                {{-- Status Indicator Auto-Check --}}
                <div
                    class="flex items-center gap-2 p-3 mb-6 text-xs border rounded-lg text-ink-500 bg-surface-100 border-border-200">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-primary-400"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary-600"></span>
                    </span>
                    <span>Menunggu verifikasi... Halaman ini akan beralih otomatis setelah email diklik.</span>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div
                        class="flex items-center gap-2 p-4 mb-6 text-xs font-semibold border rounded-md bg-emerald-50 text-emerald-800 border-emerald-200">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Tautan verifikasi baru telah dikirimkan ke email Anda.</span>
                    </div>
                @endif

                <div class="pt-2 space-y-4">
                    {{-- Form Kirim Ulang Email dengan Timer Anti-Spam --}}
                    <form method="POST" action="{{ route('verification.send') }}"
                        @submit="if(!canResend) $event.preventDefault(); startCooldown();">
                        @csrf
                        <button type="submit" :disabled="!canResend"
                            class="w-full flex justify-center items-center py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="mr-2 text-xs fa-solid fa-paper-plane"></i>
                            <span
                                x-text="canResend ? 'Kirim Ulang Email Verifikasi' : 'Tunggu ' + timer + ' detik untuk kirim ulang'"></span>
                        </button>
                    </form>

                    {{-- Form Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="text-center">
                        @csrf
                        <button type="submit"
                            class="text-xs font-semibold transition-colors text-ink-400 hover:text-ink-900">
                            <i class="mr-1 fa-solid fa-right-from-bracket"></i>
                            Keluar / Keluar Akun
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>
