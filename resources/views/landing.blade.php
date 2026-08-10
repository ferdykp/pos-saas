  <!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>GrowPOS - Solusi Kasir Digital untuk UMKM</title>
      {{-- <link rel="icon" href="{{ asset('growpos_logo.png') }}" type="image/x-icon"> --}}
      {{-- <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png?v={{ time() }}"> --}}
      <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
      <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
      <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
      <link rel="manifest" href="/site.webmanifest">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link
          href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
          rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="antialiased text-ink-900" x-cloak>
      <div class="relative min-h-screen bg-surface-0">

          <x-landing.navbar />
          <x-landing.hero />

          {{-- ===================== FITUR ===================== --}}
          <section id="fitur" class="py-20 bg-primary-50/60">
              <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">

                  <div class="flex flex-col items-center justify-center py-16 space-y-4 text-center" data-aos="fade-up">
                      <h2 class="text-5xl font-bold text-primary-900">Fitur Unggulan GrowPOS</h2>
                      <p class="max-w-xl font-normal text-body-lg text-ink-700">
                          Dirancang khusus untuk ekosistem bisnis Indonesia yang dinamis dan kompetitif.
                      </p>
                  </div>

                  <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                      <div class="md:col-span-8">
                          <x-landing.feature-card variant="large" icon="fa-box-archive"
                              title="Manajemen Inventaris Pintar"
                              desc="Update stok otomatis tiap transaksi, notifikasi stok menipis, dan tracking barang masuk-keluar secara akurat."
                              image="warehouse.png" />
                      </div>

                      <div class="md:col-span-4">
                          <x-landing.feature-card variant="solid" icon="fa-users" title="Loyalitas Pelanggan"
                              desc="Bangun basis data pelanggan yang loyal dengan program poin dan promo khusus yang terintegrasi."
                              bg="bg-primary-600 text-white" icon-bg="bg-white/15" icon-color="text-white" />
                      </div>

                      <div class="md:col-span-4">
                          <x-landing.feature-card variant="solid" icon="fa-chart-line" title="Analitik Berbasis AI"
                              desc="Dapatkan rekomendasi stok dan prediksi penjualan bulan depan berdasarkan data historis bisnis Anda secara otomatis."
                              bg="bg-accent-100 text-accent-700" icon-bg="bg-accent-700/20"
                              icon-color="text-accent-700" />
                      </div>

                      <div class="md:col-span-8">
                          <x-landing.feature-card variant="large" icon="fa-circle-nodes" title="Multi-Outlet Sync"
                              desc="Kelola banyak cabang toko hanya dari satu layar ponsel. Semua data tersinkronisasi instan ke cloud."
                              image="maps.png" />
                      </div>
                  </div>
              </div>
          </section>

          {{-- ===================== HARGA ===================== --}}
          <section id="harga" class="py-20 bg-primary-50">
              <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">

                  <div class="flex flex-col items-center justify-center py-16 space-y-4 text-center" data-aos="fade-up">
                      <h2 class="text-5xl font-bold text-primary-900">Pilih Paket Pertumbuhan Anda</h2>
                      <p class="font-normal text-body-lg text-ink-700">Tanpa biaya tersembunyi. Batalkan kapan saja.</p>
                  </div>

                  <div class="flex items-center justify-center">
                      <div class="grid items-center w-full max-w-6xl grid-cols-1 gap-6 md:grid-cols-3">

                          <x-landing.pricing-card title="Starter" desc="Cocok untuk pedagang kaki lima & UMKM baru."
                              price="Rp 0" period="selamanya" cta="Pilih Starter" :features="[
                                  '100 Transaksi / Bulan' => true,
                                  'Manajemen Stok Dasar' => true,
                                  'Laporan Harian' => true,
                                  'Analitik AI' => false,
                              ]" />

                          <x-landing.pricing-card :popular="true" title="Growth"
                              desc="Untuk toko yang mulai berkembang pesat." price="Rp 149rb" period="bulan"
                              cta="Coba 14 Hari Gratis" :features="[
                                  'Transaksi Tanpa Batas' => true,
                                  'Manajemen Stok Lanjut' => true,
                                  'CRM & Loyalitas' => true,
                                  'Support 24/7 Chat' => true,
                              ]" />

                          <x-landing.pricing-card title="Scale" desc="Solusi perusahaan untuk bisnis multi-cabang."
                              price="Rp 499rb" period="bulan" cta="Hubungi Sales" :features="[
                                  'Hingga 10 Outlet' => true,
                                  'Analitik AI Eksklusif' => true,
                                  'Integrasi API Terbuka' => true,
                                  'Account Manager Pribadi' => true,
                              ]" />

                      </div>
                  </div>
              </div>
          </section>

          <x-landing.cta-section />
          <x-landing.footer />
      </div>
  </body>

  </html>
