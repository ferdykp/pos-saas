  <!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">

      {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
      <title>GrowPOS</title>


      <!-- Fonts -->
      {{-- <link rel="preconnect" href="https://fonts.bunny.net">
      <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}

      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://gstatic.com" crossorigin>
      <link href="https://googleapis.com" rel="stylesheet">

      <!-- Scripts -->
      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="antialiased text-gray-900 ">
      <div class="relative min-h-screen antialiased bg-white">
          <nav class="sticky top-0 z-50 border-b border-gray-100 bg-white/80 backdrop-blur-md">
              <div class="flex items-center justify-between px-4 py-5 mx-3 max-w-8xl sm:px-6 lg:px-8">
                  <div class="flex items-center space-x-10">
                      <a href="/"> <span
                              class="text-3xl font-extrabold tracking-tight text-[#005236] hover:text-[#00885D] duration-500 transition-all">GrowPOS
                      </a>
                      <div class="flex items-center space-x-6 text-[15px] font-semibold ">

                          <a href="#fitur"
                              class="group relative hover:text-[#00885D] transition duration-300 inline-block">
                              <span>Fitur</span>
                              <!-- Perubahan ada di class w-0, group-hover:w-full, dan left-0 -->
                              <span
                                  class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#00885D]"></span>
                          </a>
                          <a href="#"
                              class="group relative hover:text-[#00885D] transition duration-300 inline-block">
                              <span>Harga</span>
                              <span
                                  class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#00885D]"></span>
                          </a>
                          <a href="#"
                              class="group relative hover:text-[#00885D] transition duration-300 inline-block"><span>Tentang
                                  Kami</span>
                              <span
                                  class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#00885D]"></span>
                          </a>
                      </div>
                  </div>
                  <div class="flex items-center space-x-8 text-[15px] font-semibold text-gray-600">
                      <a href="{{ route('login') }}"
                          class="group relative hover:text-[#00885D] transition duration-300 inline-block">Masuk<span
                              class="absolute left-0 bottom-[-6px] w-0 group-hover:w-full transition-all duration-500 h-0.5 bg-[#00885D]"></span></a>
                      <a href="{{ route('register') }}"
                          class="relative bg-[#00A572] text-white px-5 py-2.5 rounded-[15px] hover:bg-[#006C49] transition shadow-md shadow-blue-100">Mulai
                          Gratis</a>
                  </div>
              </div>
          </nav>
          <div class="py-32 border-[5px]">
              <div class="px-2 mx-10 max-w-7xl">
                  <div class="w-40 py-6">
                      <div
                          class="text-sm rounded-xl tracking-wider text-[#003824] text-center font-semibold px-2 py-1.5 bg-[#00885D]/30 ">
                          All in One
                          Solution</div>
                  </div>
                  <div class="flex items-center justify-between w-full gap-8">
                      <div class="w-full max-w-xl">
                          <div class="relative">
                              <div class="py-2 text-5xl font-bold text-uppercase">Everything Your Buisness Needs to
                                  <span class="text-[#00885D]">Grow</span>
                              </div>
                              <div class="py-2 text-md">Spend less time managing your business and more time growing it.
                                  <span class="text-[#00885D] font-semibold">GrowPOS</span> keeps your sales, inventory,
                                  and reports organized in one easy-to-use system.
                              </div>

                              <div class="py-6 text-sm font-semibold text-white ">
                                  <a href=""
                                      class="py-3 px-1.5 bg-[#00885D] border-[#00885D] border-[1px] rounded-md  text-white hover:bg-white hover:text-[#00885D] transition-all inline-block">Start
                                      Free
                                      Trial</a>
                                  <a href=""
                                      class="px-1.5 py-3 rounded-md text-[#00885D] border-[#00885D] border-[1px] mx-3 hover:bg-[#00885D] hover:text-white transition-all inline-block">Book
                                      a
                                      Demo</a>
                              </div>
                          </div>
                      </div>
                      <div class="flex justify-end">
                          <div class="relative group">
                              <div
                                  class="absolute -inset-1 bg-gradient-to-r from-[#003824] to-[#00885D] rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-1000">
                              </div>
                              <div
                                  class="relative w-[450px] bg-white border border-gray-100 rounded-[2rem] shadow-2xl overflow-hidden p-1.5">
                                  <div
                                      class="rounded-[1.5rem] overflow-hidden bg-gray-50 border border-gray-100 aspect-[16/9] flex items-center justify-center">
                                      <div class="flex flex-col items-center">
                                          <img class="object-cover w-full h-full" src="{{ asset('img/hero.png') }}"
                                              alt="">
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          {{-- <div class="relative pt-20 pb-32 overflow-hidden">
              <div class="absolute inset-0 z-0">
                  <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-50 rounded-full blur-[120px] opacity-60"></div>
                  <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-50 rounded-full blur-[120px] opacity-60">
                  </div>
              </div>

              <div class="relative z-10 px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                  <div
                      class="inline-flex items-center px-4 py-1.5 mb-8 rounded-full bg-blue-50 border border-blue-100/50">
                      <span class="relative flex w-2 h-2 mr-3">
                          <span
                              class="absolute inline-flex w-full h-full bg-blue-400 rounded-full opacity-75 animate-ping"></span>
                          <span class="relative inline-flex w-2 h-2 bg-blue-600 rounded-full"></span>
                      </span>
                      <span class="text-xs font-bold tracking-wider text-blue-700 uppercase">Versi 2.0 Kini
                          Tersedia</span>
                  </div>

                  <h1
                      class="text-5xl md:text-7xl lg:text-8xl font-black text-gray-900 tracking-tighter leading-[1.1] mb-8">
                      Bisnis Tumbuh <br>
                      <span
                          class="text-transparent bg-clip-text bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600">Lebih
                          Cepat.</span>
                  </h1>

                  <p class="max-w-2xl mx-auto mb-12 text-lg leading-relaxed text-gray-500 md:text-xl">
                      Lupakan pencatatan manual. Kelola penjualan, stok, dan laporan keuangan UMKM Anda dengan satu
                      sistem
                      kasir yang intuitif dan sangat cepat.
                  </p>

                  <div class="flex flex-col items-center justify-center gap-5 sm:flex-row">
                      <a href="{{ route('register') }}"
                          class="relative inline-flex items-center justify-center px-10 py-4 font-bold text-white transition-all duration-200 bg-blue-600 shadow-2xl group rounded-2xl hover:bg-blue-700 focus:outline-none shadow-blue-200">
                          Daftar Sekarang - Gratis
                          <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none"
                              stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
                          </svg>
                      </a>
                      <a href="#fitur"
                          class="px-10 py-4 font-bold text-gray-700 transition-all bg-white border border-gray-200 rounded-2xl hover:bg-gray-50">
                          Eksplor Fitur
                      </a>
                  </div>

                  <div class="relative max-w-6xl mx-auto mt-24 group">
                      <div
                          class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-1000">
                      </div>
                      <div
                          class="relative bg-white border border-gray-100 rounded-[2rem] shadow-2xl overflow-hidden p-3">
                          <div
                              class="rounded-[1.5rem] overflow-hidden bg-gray-50 border border-gray-100 aspect-[16/9] flex items-center justify-center">
                              <div class="flex flex-col items-center">
                                  <svg class="w-16 h-16 mb-4 text-blue-100" fill="currentColor" viewBox="0 0 20 20">
                                      <path fill-rule="evenodd"
                                          d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                          clip-rule="evenodd" />
                                  </svg>
                                  <span class="text-xs font-medium tracking-widest text-gray-400 uppercase">Preview
                                      Dashboard Interface</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div> --}}

          <section id="fitur" class="py-32 bg-white border-[5px]">
              <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                  <div class="max-w-2xl mb-20">
                      <h2 class="text-blue-600 font-bold uppercase tracking-[0.2em] text-xs mb-4">Fitur Utama</h2>
                      <p class="text-4xl font-extrabold tracking-tight text-gray-900">Didesain untuk Efisiensi Tanpa
                          Batas.</p>
                  </div>

                  <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                      <div
                          class="md:col-span-8 bg-gray-50 rounded-[2rem] p-10 border border-gray-100 flex flex-col justify-between hover:bg-blue-50/50 transition-colors duration-500">
                          <div class="max-w-md">
                              <div
                                  class="flex items-center justify-center w-12 h-12 mb-8 text-white bg-blue-600 shadow-lg rounded-2xl shadow-blue-100">
                                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m0 10V4m-4 6h4" />
                                  </svg>
                              </div>
                              <h3 class="mb-4 text-2xl font-bold text-gray-900">Multi-Tenant Architecture</h3>
                              <p class="leading-relaxed text-gray-500">Sistem SaaS yang memungkinkan Anda mengelola
                                  ratusan outlet dengan basis data yang terisolasi secara aman. Satu dashboard untuk
                                  kendali penuh atas seluruh kerajaan bisnis Anda.</p>
                          </div>
                          <div class="mt-12 overflow-hidden border border-gray-200 rounded-xl">
                              <div
                                  class="flex items-center justify-center h-40 p-4 text-sm italic text-gray-300 bg-white">
                                  Visual Ilustrasi Multi-Outlet</div>
                          </div>
                      </div>

                      <div
                          class="md:col-span-4 bg-gray-900 rounded-[2rem] p-10 text-white flex flex-col hover:shadow-2xl transition-all duration-500">
                          <div
                              class="flex items-center justify-center w-12 h-12 mb-8 text-white bg-green-500 shadow-lg rounded-2xl shadow-green-900/20">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                              </svg>
                          </div>
                          <h3 class="mb-4 text-2xl font-bold">Stok Real-time</h3>
                          <p class="leading-relaxed text-gray-400">Ucapkan selamat tinggal pada selisih stok.
                              Sinkronisasi
                              instan antara gudang dan meja kasir.</p>
                      </div>

                      <div
                          class="md:col-span-4 bg-indigo-600 rounded-[2rem] p-10 text-white flex flex-col hover:shadow-2xl transition-all duration-500">
                          <div
                              class="flex items-center justify-center w-12 h-12 mb-8 text-indigo-600 bg-white shadow-lg rounded-2xl shadow-indigo-900/20">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                              </svg>
                          </div>
                          <h3 class="mb-4 text-2xl font-bold">Laporan Akurat</h3>
                          <p class="leading-relaxed text-indigo-100">Visualisasi data yang cantik untuk memantau laba
                              rugi secara harian.</p>
                      </div>

                      <div
                          class="md:col-span-8 bg-gray-50 rounded-[2rem] p-10 border border-gray-100 hover:bg-blue-50/50 transition-all duration-500">
                          <div class="flex flex-col items-center gap-10 md:flex-row">
                              <div class="flex-1">
                                  <h3 class="mb-4 text-2xl font-bold text-gray-900">Analisis Pintar</h3>
                                  <p class="leading-relaxed text-gray-500">Tentukan strategi diskon dan promosi
                                      berdasarkan jam tersibuk yang dianalisa secara otomatis oleh sistem kami.</p>
                              </div>
                              <div
                                  class="flex items-center justify-center w-full h-32 text-gray-300 bg-white border border-gray-100 md:w-1/2 rounded-2xl">
                                  Chart Graph Preview
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </section>

          <section class="py-24">
              <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                  <div class="relative bg-blue-600 rounded-[3rem] overflow-hidden p-12 md:p-20 text-center">
                      <div class="absolute inset-0 opacity-10">
                          <svg class="w-full h-full" fill="none" viewBox="0 0 100 100"
                              preserveAspectRatio="none">
                              <circle cx="100" cy="0" r="40" fill="white" />
                              <circle cx="0" cy="100" r="30" fill="white" />
                          </svg>
                      </div>
                      <div class="relative z-10">
                          <h2 class="mb-6 text-3xl font-extrabold text-white md:text-5xl">Siap Mengubah Bisnis Anda?
                          </h2>
                          <p class="max-w-xl mx-auto mb-10 text-lg font-medium text-blue-100">Beralih ke digital hari
                              ini. Tanpa instalasi rumit, tanpa biaya tersembunyi.</p>
                          <a href="{{ route('register') }}"
                              class="inline-block px-12 py-4 text-lg font-bold text-blue-600 transition transform bg-white shadow-2xl rounded-2xl hover:bg-gray-50 hover:scale-105 active:scale-95">
                              Dapatkan Akun Gratis
                          </a>
                      </div>
                  </div>
              </div>
          </section>

          <footer class="py-16 bg-white border-t border-gray-100">
              <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                  <div class="flex flex-col items-center justify-between gap-8 md:flex-row">
                      <div class="flex items-center space-x-2">
                          <div class="flex items-center justify-center w-8 h-8 bg-blue-600 rounded-lg">
                              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                  viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z" />
                              </svg>
                          </div>
                          <span class="text-xl font-bold text-gray-900">POS<span
                                  class="font-medium text-blue-600">SaaS</span></span>
                      </div>
                      <p class="text-sm font-medium text-gray-400">
                          &copy; {{ date('Y') }} POSSaaS Solution. Crafted with precision for UMKM.
                      </p>
                      <div class="flex space-x-8">
                          <a href="#" class="text-gray-400 transition hover:text-blue-600">Twitter</a>
                          <a href="#" class="text-gray-400 transition hover:text-blue-600">LinkedIn</a>
                          <a href="#" class="text-gray-400 transition hover:text-blue-600">Contact</a>
                      </div>
                  </div>
              </div>
          </footer>
      </div>

  </body>

  </html>
