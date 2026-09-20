  <!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>GrowPOS — Kasir untuk Toko, Jasa & Kuliner</title>
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

      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="antialiased text-ink-900">
      <div class="relative min-h-screen bg-surface-0">

          <x-landing.navbar />
          <main id="main-content">
          <x-landing.hero />

          {{-- ===================== FITUR ===================== --}}
          <section id="fitur" class="py-20 bg-primary-50/60">
              <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">

                  <div class="flex flex-col items-center justify-center py-16 space-y-4 text-center">
                      <h2 class="text-5xl font-bold text-primary-900">Fitur Unggulan GrowPOS</h2>
                      <p class="max-w-xl font-normal text-body-lg text-ink-700">
                          Dari penjualan barang dan layanan jasa, sampai persediaan dan laporan usaha.
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
                          <x-landing.feature-card variant="solid" icon="fa-chart-line" title="Ringkasan yang Bisa Ditindaklanjuti"
                              desc="Lihat perbandingan penjualan, menu terlaris, bahan menipis, dan selisih kas dengan sumber transaksi yang bisa diperiksa."
                              bg="bg-accent-100 text-accent-700" icon-bg="bg-accent-700/20"
                              icon-color="text-accent-700" />
                      </div>

                      <div class="md:col-span-8">
                          <x-landing.feature-card variant="large" icon="fa-circle-nodes" title="Pantau Usaha dari HP"
                              desc="Buka ringkasan setiap toko dari browser. Transaksi yang sudah tersinkron tersedia pada laporan; jumlah outlet mengikuti paket."
                              image="maps.png" />
                      </div>
                  </div>
              </div>
          </section>

          {{-- ===================== HARGA ===================== --}}
          <section id="harga" class="py-20 bg-primary-50">
              <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">

                  <div class="flex flex-col items-center justify-center py-16 space-y-4 text-center">
                      <h2 class="text-5xl font-bold text-primary-900">Pilih Paket Pertumbuhan Anda</h2>
                      <p class="font-normal text-body-lg text-ink-700">Lihat harga, masa berlaku, dan kapasitas paket sebelum memilih.</p>
                  </div>

                  <div class="flex items-center justify-center">
                      <div class="grid items-center w-full max-w-6xl grid-cols-1 gap-6 md:grid-cols-3">

                          @if (isset($plans) && $plans->count() > 0)
                              @foreach ($plans as $plan)
                                  <x-landing.pricing-card :plan-id="$plan->id" :popular="$plan->slug === 'growth'" :title="$plan->name"
                                      :desc="$plan->description" :price="$plan->price == 0
                                          ? 'Rp 0'
                                          : 'Rp ' . number_format($plan->price, 0, ',', '.')" :period="$plan->duration_days . ' hari'" :cta="auth()->check()
                                          ? ($plan->price == 0
                                              ? 'Pilih Starter'
                                              : 'Langganan Sekarang')
                                          : 'Mulai Sekarang'"
                                      :capacity="$plan->max_outlets.' outlet · '.$plan->max_users.' pengguna · '.$plan->max_products.' menu'" :features="$plan->features ?? []" />
                              @endforeach
                          @else
                              <p class="bg-white [border:1px_solid_#e1e9e4] rounded-lg [padding:22px] [box-shadow:0_2px_5px_#17392c03] max-[701px]:[padding:18px] [color:#65796f] [font-size:13px] [line-height:1.6]">Paket belum tersedia. Harga dan kapasitas akan ditampilkan setelah paket diaktifkan.</p>
                          @endif

                      </div>
                  </div>
              </div>
          </section>
          <div class="[max-width:1280px] [margin:auto] p-7 [color:#18372d] max-[701px]:[padding:20px_16px]"><div class="[padding:13px_16px] rounded-md [background:#fff6df] [color:#81591c] [font-size:13px] [line-height:1.6]">Pembayaran QRIS mengikuti ketersediaan paket dan konfigurasi payment gateway. Komisi platform saat ini {{ number_format(config('platform.commission_rate', 0.015) * 100, 2, ',', '.') }}% dari transaksi QRIS. Periksa rincian saldo dan pengajuan pencairan di aplikasi.</div></div>
          <x-landing.cta-section />
          </main>
          <x-landing.footer />
      </div>
  </body>

  </html>
