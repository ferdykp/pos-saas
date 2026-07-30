  {{-- <!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="duration-1000 scroll-smooth">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <title>GrowPOS</title>

      <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

      <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link
          href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
          rel="stylesheet">

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

      <!-- Scripts -->
      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
      AOS.init({
          duration: 1000,
          once: true,
          easing: 'ease-out-cubic'
      });
  </script>

  <body class="antialiased text-gray-900 ">
      <div class="relative min-h-screen antialiased bg-white">
          <nav class="sticky top-0 z-50 font-sans border-b border-gray-100 shadow-md bg-[#BCFFE0]/10 backdrop-blur-md">
              <div class="flex items-center justify-between px-4 py-5 mx-3 max-w-8xl sm:px-6 lg:px-8">
                  <div class="flex items-center space-x-10">
                      <div class="flex transition duration-500 scale-85 hover:scale-105">
                          <a href="/" class="flex items-center">
                              <img src="{{ asset('img/growpos_logo.png') }}" class="w-12 " alt="">
                              <span
                                  class="text-3xl font-extrabold tracking-tight text-[#16805F] hover:text-[#12664B] duration-500 transition-all">GrowPOS
                              </span>
                          </a>
                      </div>
                  </div>
                  <div class="flex items-center space-x-6 text-[15px] font-semibold ">

                      <a href="#fitur"
                          class="group relative hover:text-[#12664B] transition duration-300 inline-block">
                          <span>Fitur</span>
                          <!-- Perubahan ada di class w-0, group-hover:w-full, dan left-0 -->
                          <span
                              class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#16805F]"></span>
                      </a>
                      <a href="#harga"
                          class="group relative hover:text-[#12664B] transition duration-300 inline-block">
                          <span>Harga</span>
                          <span
                              class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#16805F]"></span>
                      </a>
                      <a href="#us"
                          class="group relative hover:text-[#12664B] transition duration-300 inline-block"><span>Tentang
                              Kami</span>
                          <span
                              class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#16805F]"></span>
                      </a>
                  </div>

                  <div class="flex items-center space-x-8 text-[15px] font-semibold text-gray-600">
                      <a href="{{ route('login') }}"
                          class="group relative hover:text-[#12664B] transition duration-300 inline-block">Masuk<span
                              class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#00885D]"></span></a>
                      <a href="{{ route('register') }}"
                          class="scale-85 hover:scale-105  duration-500 relative bg-[#12664B] text-white px-5 py-2.5 rounded-[15px] hover:bg-[#0a9c6e] transition shadow-md shadow-blue-100">Mulai
                          Gratis</a>
                  </div>
              </div>
          </nav>
          <div class="py-32 bg-[#BCFFE0]/10 ">
              <div class="px-2 mx-10 max-w-8xl">
                  <div class="w-40 py-6">
                      <div
                          class="text-sm rounded-xl tracking-wider text-[#003824] text-center font-semibold px-2 py-1.5 bg-[#00885D]/30 ">
                          All in One
                          Solution</div>
                  </div>
                  <div class="flex items-center justify-between w-full gap-8">
                      <div class="w-full max-w-xl">
                          <div class="relative">
                              <div class="py-2 text-5xl font-bold text-uppercase">Solusi Kasir Digital yang
                                  <span
                                      class="text-[#00885D] underline underline-offset-4 decoration-[#F0932B] decoration-4">Tumbuh</span>
                                  <span
                                      class="text-[#00885D] underline underline-offset-4 decoration-[#F0932B] decoration-4">
                                      Bersama </span>Bisnis Anda
                              </div>
                              <div class="py-2 text-md">Berdayakan bisnis kecil Anda dengan teknologi POS kelas dunia.
                                  Kelola inventaris, pantau laporan keuangan secara real-time, dan tingkatkan loyalitas
                                  pelanggan dalam satu aplikasi.
                              </div>

                              <div class="grid grid-cols-2 py-6 font-semibold text-white text-md ">
                                  <a href=""
                                      class="py-3 px-1.5 items-center justify-center flex bg-[#F0932B] rounded-md  text-[#4B2800] hover:-translate-y-1 transition duration-300">Mulai
                                      Uji Coba Gratis</a>
                                  <a href=""
                                      class="flex items-center gap-2 justify-center px-1.5 py-3 rounded-md text-[#00885D] border-[#00885D] border-[1px] mx-3 hover:-translate-y-1 transition duration-300">
                                      <i class="fa-solid fa-circle-play text-md"></i>Jadwalkan
                                      Demo</a>
                              </div>
                          </div>
                      </div>
                      <div class="flex justify-end">
                          <div class="relative group">
                              <div
                                  class="rotate-3 absolute -inset-1  bg-gradient-to-r from-[#003824] to-[#00885D] rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-500">
                              </div>
                              <div
                                  class="rotate-3 scale-100 hover:scale-105 transition duration-500 relative w-[700px] bg-white border border-gray-100 rounded-[2rem] shadow-2xl overflow-hidden p-1.5">
                                  <div
                                      class="rounded-[1.5rem] overflow-hidden bg-gray-50 border border-gray-100 aspect-[16/9] flex items-center justify-center">
                                      <div class="flex flex-col items-center ">
                                          <img class="object-cover w-full h-full "
                                              src="{{ asset('img/hero-screen.png') }}" alt="">
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <section id="fitur">
              <div class="py-20 bg-[#A5F2CF]/10">
                  <div class="px-4 mx-4 w-max-8xl">
                      <div class="flex flex-col items-center justify-center py-20 space-y-6">
                          <div class="text-4xl font-bold text-[#006C4E]">
                              Fitur Unggulan GrowPOS
                          </div>
                          <div class="font-normal text-md text-[#0B3D2E]">
                              Dirancang khusus untuk ekosistem bisnis Indonesia yang dinamis dan kompetitif.
                          </div>
                      </div>
                      <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                          <div
                              class="rounded-xl shadow-md md:col-span-8 px-8 py-10 bg-white border-[1px] transition-all hover:border-[#6FBA9A] ">
                              <div class="flex items-center justify-between gap-6 ">
                                  <div class="items-center max-w-[450px]">
                                      <i
                                          class="text-2xl mb-3 fa-solid py-2 px-3 bg-[#006C49]/10 backdrop-blur-0 rounded-xl fa-box-archive text-[#006C4E]"></i>
                                      <div class="mb-3 text-2xl font-bold">
                                          Manajemen Inventaris Pintar
                                      </div>
                                      <div class="font-medium text-md">
                                          Update stok otomatis tiap transaksi, notifikasi stok menipis, dan tracking
                                          barang
                                          masuk-keluar secara akurat.
                                      </div>
                                  </div>
                                  <div class="items-center">
                                      <img src="{{ asset('img/warehouse.png') }}" class="w-[450px] h-[250px] rounded-xl"
                                          alt="">
                                  </div>
                              </div>
                          </div>
                          <div class="md:col-span-4 bg-[#006C4E] text-white rounded-xl">
                              <div class="px-8 py-10 max-w-[400px]">
                                  <div class="">
                                      <i
                                          class="bg-[#079064]  backdrop-blur-0 rounded-xl text-2xl mb-4 py-2 px-3 fa-solid fa-users"></i>
                                  </div>
                                  <div class="mb-4 text-2xl font-bold">
                                      Loyalitas Pelanggan
                                  </div>
                                  <div class="font-semibold text-md">
                                      Bangun basis data pelanggan yang loyal dengan program poin dan promo khusus yang
                                      terintegrasi.
                                  </div>
                              </div>
                          </div>
                          <div class="md:col-span-4 rounded-xl bg-[#F0932B] px-8 py-10">
                              <div>
                                  <i
                                      class="text-2xl mb-4 fa-solid fa-chart-line text-[#4B2800] bg-[#8C4F00]/20 py-2 px-3 rounded-xl"></i>
                              </div>

                              <div class="mb-4 text-2xl font-bold">
                                  Analitik Berbasis AI
                              </div>

                              <div class="font-normal text-md">
                                  Dapatkan rekomendasi stok dan prediksi penjualan bulan depan berdasarkan data historis
                                  bisnis Anda secara otomatis.
                              </div>
                          </div>
                          <div
                              class="px-8 py-10 md:col-span-8 bg-white rounded-xl border-[1px] transition-all hover:border-[#6FBA9A]">
                              <div class="flex items-start justify-between gap-8">
                                  <div class="">
                                      <img src="{{ asset('img/maps.png') }}" class="w-[800px] h-[250px] rounded-xl"
                                          alt="">
                                  </div>
                                  <div class="flex flex-col justify-between w-[500px] h-[200px]">
                                      <div class="">
                                          <i
                                              class="text-2xl fa-solid fa-circle-nodes text-[#006C49] bg-[#A5F2CF] rounded-xl py-2 px-3"></i>
                                      </div>
                                      <div class="text-2xl font-bold">Multi-Outlet Sync</div>
                                      <div class="font-normal text-md">Kelola banyak cabang toko hanya dari satu layar
                                          ponsel. Semua
                                          data
                                          tersinkronisasi instan ke cloud.</div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </section>

          <section id="harga">
              <div class="py-20 bg-[#F2FAF6]">
                  <div class="max-w-8xl">
                      <div class="flex flex-col items-center justify-center py-20 space-y-6">
                          <div class="text-4xl font-bold text-[#006C4E]">
                              Pilih Paket Pertumbuhan Anda
                          </div>
                          <div class="font-normal text-md text-[#0B3D2E]">
                              Tanpa biaya tersembunyi. Batalkan kapan saja. </div>
                      </div>

                      <div class="flex items-center justify-center ">
                          <div class="grid items-center w-full max-w-6xl grid-cols-1 gap-6 md:grid-cols-3">

                              <!-- CARD 1: STARTER -->
                              <div
                                  class="bg-white rounded-3xl p-8 transition duration-300 hover:-translate-y-3 border border-gray-200 shadow-sm flex flex-col justify-between h-[520px]">
                                  <div class="space-y-4 ">
                                      <h3 class="text-2xl font-bold text-gray-900">Starter</h3>
                                      <p class="text-sm leading-relaxed text-gray-600">Cocok untuk pedagang kaki lima &
                                          UMKM baru.</p>
                                      <div class="text-[#006C4E] font-bold text-xl flex items-baseline gap-1">
                                          <span class="text-2xl">Rp 0</span>
                                          <span class="text-sm font-normal text-gray-500">/selamanya</span>
                                      </div>
                                      <ul class="pt-2 space-y-3 text-sm text-gray-700">
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>100 Transaksi / Bulan</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Manajemen Stok Dasar</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Laporan Harian</span>
                                          </li>
                                          <li class="flex items-center gap-3 text-gray-400">
                                              <i class="text-lg fa-regular fa-circle-xmark"></i>
                                              <span>Analitik AI</span>
                                          </li>
                                      </ul>
                                  </div>
                                  <button
                                      class="w-full py-3 rounded-xl border-2 border-[#006C4E] text-[#006C4E] font-bold text-sm hover:bg-[#006C4E] hover:text-white transition">
                                      Pilih Starter
                                  </button>
                              </div>

                              <!-- CARD 2: GROWTH (PALING POPULER) -->
                              <div
                                  class="duration-300 hover:-translate-y-3 relative bg-white rounded-3xl p-8 border-2 border-[#006C4E] shadow-xl flex flex-col justify-between h-[560px]">
                                  <!-- Badge Paling Populer -->
                                  <div
                                      class="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#006C4E] text-white text-xs font-bold px-4 py-1.5 rounded-full tracking-wider uppercase">
                                      Paling Populer
                                  </div>

                                  <div class="space-y-4">
                                      <h3 class="text-2xl font-bold text-gray-900">Growth</h3>
                                      <p class="text-sm leading-relaxed text-gray-600">Untuk toko yang mulai berkembang
                                          pesat.</p>
                                      <div class="text-[#006C4E] font-bold text-xl flex items-baseline gap-1">
                                          <span class="text-2xl">Rp 149rb</span>
                                          <span class="text-sm font-normal text-gray-500">/bulan</span>
                                      </div>
                                      <ul class="pt-2 space-y-3 text-sm text-gray-700">
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Transaksi Tanpa Batas</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Manajemen Stok Lanjut</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>CRM & Loyalitas</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Support 24/7 Chat</span>
                                          </li>
                                      </ul>
                                  </div>
                                  <button
                                      class="w-full py-3 rounded-xl bg-[#006C4E] text-white font-bold text-sm hover:bg-[#00523B] shadow-md transition">
                                      Coba 14 Hari Gratis
                                  </button>
                              </div>

                              <!-- CARD 3: SCALE -->
                              <div
                                  class="duration-300 hover:-translate-y-3 bg-white rounded-3xl p-8 border border-gray-200 shadow-sm flex flex-col justify-between h-[520px]">
                                  <div class="space-y-4">
                                      <h3 class="text-2xl font-bold text-gray-900">Scale</h3>
                                      <p class="text-sm leading-relaxed text-gray-600">Solusi perusahaan untuk bisnis
                                          multi-cabang.</p>
                                      <div class="text-[#006C4E] font-bold text-xl flex items-baseline gap-1">
                                          <span class="text-2xl">Rp 499rb</span>
                                          <span class="text-sm font-normal text-gray-500">/bulan</span>
                                      </div>
                                      <ul class="pt-2 space-y-3 text-sm text-gray-700">
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Hingga 10 Outlet</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Analitik AI Eksklusif</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Integrasi API Terbuka</span>
                                          </li>
                                          <li class="flex items-center gap-3">
                                              <i class="fa-regular fa-circle-check text-[#006C4E] text-lg"></i>
                                              <span>Account Manager Pribadi</span>
                                          </li>
                                      </ul>
                                  </div>
                                  <button
                                      class="w-full py-3 rounded-xl border-2 border-[#006C4E] text-[#006C4E] font-bold text-sm hover:bg-[#006C4E] hover:text-white transition">
                                      Hubungi Sales
                                  </button>
                              </div>

                          </div>
                      </div>
                  </div>
              </div>
          </section>

          <section id="us" class="py-24">
              <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                  <div class="relative bg-[#16805F] rounded-[3rem] overflow-hidden p-12 md:p-20 text-center">
                      <div class="absolute inset-0 opacity-10">
                          <svg class="w-full h-full" fill="none" viewBox="0 0 100 100"
                              preserveAspectRatio="none">
                              <circle cx="100" cy="0" r="40" fill="white" />
                              <circle cx="0" cy="100" r="30" fill="white" />
                          </svg>
                      </div>
                      <div class="relative z-10">
                          <h2 class="mb-6 text-3xl font-extrabold text-white md:text-5xl">Siap Menumbuhkan Bisnis Anda?
                          </h2>
                          <p class="max-w-xl mx-auto mb-10 text-lg font-medium text-blue-100">Bergabunglah dengan
                              ribuan pengusaha Indonesia lainnya yang telah beralih ke kasir digital modern.</p>
                          <div class="flex items-center justify-center space-x-8">
                              <a href="{{ route('register') }}"
                                  class="inline-block px-12 py-4 w-[300px] text-lg font-bold text-[#4B2800] transition transform bg-[#D37C0E] shadow-2xl rounded-2xl hover:bg-[#F3952D] hover:scale-105 active:scale-95">
                                  Mulai Gratis Sekarang
                              </a>
                              <a href="{{ route('register') }}"
                                  class="inline-block px-12 py-4 text-lg w-[300px] font-bold text-white transition transform bg-[#003827] shadow-2xl rounded-2xl hover:bg-[#002115] hover:scale-105 active:scale-95">
                                  Konsultasi Gratis
                              </a>
                          </div>
                      </div>
                  </div>
              </div>
          </section>

          <footer class=" bg-[#7BD8B2]/20">
              <div class="grid grid-cols-1 gap-6 py-10 mx-10 md:grid-cols-12">
                  <div class=" w-60 md:col-span-3">
                      <div class="flex items-center mb-5">
                          <img src="{{ asset('img/growpos_logo.png') }}" class="w-10" alt="">
                          <div class="text-sm font-bold text text-[#16805F]">GrowPOS</div>
                      </div>
                      <div class="text-sm">
                          Solusi Kasir Digital No.1 di Indonesia untuk UMKM yang visioner.
                      </div>
                  </div>
                  <div class="md:col-span-3">
                      <div class="text-sm font-bold text-[#16805F] mb-5">Produk</div>
                      <div class="space-y-3 font-medium">
                          <div class="">Fitur POS</div>
                          <div class="">Inventaris</div>
                          <div class="">Laporan Keuangan</div>
                          <div class="">Hardware Kasir</div>
                      </div>
                  </div>
                  <div class="md:col-span-3">
                      <div class="text-sm font-bold text-[#16805F] mb-5">Perusahaan</div>
                      <div class="space-y-3 font-medium">
                          <div class="">Tentang Kami</div>
                          <div class="">Karir</div>
                          <div class="">Blog</div>
                          <div class="">Kontak</div>
                      </div>
                  </div>
                  <div class="md:col-span-3">
                      <div class="text-sm font-bold text-[#16805F] mb-5">Bantuan</div>
                      <div class="space-y-3 font-medium">
                          <div class="">Pusat Bantuan</div>
                          <div class="">Kebijakan Privasi</div>
                          <div class="">Syarat dan Ketentuan</div>
                          <div class="">Keamanan Data</div>
                      </div>
                  </div>
              </div>
              <hr>

              <div class="py-5 mx-10 text-sm font-thickness ">
                  © 2024 GrowPOS Indonesia. Solusi Kasir Digital UMKM.
              </div>

          </footer>

      </div>

  </body>

  </html> --}}
  <!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>GrowPOS - Solusi Kasir Digital untuk UMKM</title>

      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link
          href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
          rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="antialiased text-gray-900" x-cloak>
      <div class="relative min-h-screen bg-white">

          <x-landing.navbar />

          <x-landing.hero />

          {{-- ===================== FITUR ===================== --}}
          <section id="fitur" class="py-20 bg-brand-mint-2/10">
              <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">

                  <div class="flex flex-col items-center justify-center py-16 space-y-4 text-center" data-aos="fade-up">
                      <h2 class="text-3xl font-bold md:text-4xl text-brand-dark">Fitur Unggulan GrowPOS</h2>
                      <p class="max-w-xl font-normal text-brand-dark/70 text-md">
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
                              bg="bg-brand text-white" icon-bg="bg-white/15" icon-color="text-white" />
                      </div>

                      <div class="md:col-span-4">
                          <x-landing.feature-card variant="solid" icon="fa-chart-line" title="Analitik Berbasis AI"
                              desc="Dapatkan rekomendasi stok dan prediksi penjualan bulan depan berdasarkan data historis bisnis Anda secara otomatis."
                              bg="bg-warning text-warning-dark" icon-bg="bg-warning-dark/20"
                              icon-color="text-warning-dark" />
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
          <section id="harga" class="py-20 bg-[#F2FAF6]">
              <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-10">

                  <div class="flex flex-col items-center justify-center py-16 space-y-4 text-center" data-aos="fade-up">
                      <h2 class="text-3xl font-bold md:text-4xl text-brand-dark">Pilih Paket Pertumbuhan Anda</h2>
                      <p class="font-normal text-brand-dark/70 text-md">Tanpa biaya tersembunyi. Batalkan kapan saja.
                      </p>
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
