<x-app-layout>
    <div class="max-w-5xl px-4 py-8 mx-auto sm:px-6 lg:px-8">

        <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div
                    class="p-3 text-white shadow-xl bg-gradient-to-tr from-blue-600 to-purple-600 rounded-2xl shadow-blue-100">
                    <i class="text-xl fa-solid fa-robot"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-gray-900">Smart AI Business Advisor</h1>
                    <p class="text-xs font-medium text-gray-400">Analisis kecerdasan buatan multi-metrik berdasarkan data
                        riil 30 hari terakhir.</p>
                </div>
            </div>
            <div>
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-green-700 bg-green-50 border border-green-100 rounded-xl">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                    AI Engine Active
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4">

            <div class="p-5 bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Omset / Hari</span>
                    <div class="p-2 text-xs text-blue-600 bg-blue-50 rounded-xl"><i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <h3 class="text-lg font-black text-gray-900">
                    Rp {{ number_format($rataRataOmsetHarian, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-[11px] text-gray-400 font-medium">Rataan pendapatan harian</p>
            </div>

            <div class="p-5 bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Hari Teramai</span>
                    <div class="p-2 text-xs text-purple-600 bg-purple-50 rounded-xl"><i
                            class="fa-solid fa-calendar-day"></i></div>
                </div>
                <h3 class="text-lg font-black text-purple-700">
                    {{ $hariTeramaiIndo }}
                </h3>
                <p class="mt-1 text-[11px] text-gray-400 font-medium">Puncak traffic transaksi</p>
            </div>

            <div class="p-5 bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Avg Basket Size</span>
                    <div class="p-2 text-xs text-emerald-600 bg-emerald-50 rounded-xl"><i
                            class="fa-solid fa-basket-shopping"></i></div>
                </div>
                <h3 class="text-lg font-black text-gray-900">
                    Rp {{ number_format($rataRataNilaiPerNota, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-[11px] text-gray-400 font-medium">Rata-rata belanja per nota</p>
            </div>

            <div class="p-5 bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Produk Juara</span>
                    <div class="p-2 text-xs text-amber-600 bg-amber-50 rounded-xl"><i class="fa-solid fa-crown"></i>
                    </div>
                </div>
                <h3 class="text-sm font-black truncate text-amber-700" title="{{ $produkJuara }}">
                    {{ $produkJuara }}
                </h3>
                <p class="mt-1 text-[11px] text-gray-400 font-medium">Item paling sering dibeli</p>
            </div>
        </div>

        <div
            class="bg-white border border-gray-100 shadow-[0_15px_50px_rgb(0,0,0,0.02)] rounded-[2.5rem] overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600"></div>

            <div class="p-8 sm:p-10">
                <div class="flex items-center gap-2 pb-4 mb-6 border-b border-gray-50">
                    <span class="relative flex w-2 h-2">
                        <span
                            class="absolute inline-flex w-full h-full bg-blue-400 rounded-full opacity-75 animate-ping"></span>
                        <span class="relative inline-flex w-2 h-2 bg-blue-500 rounded-full"></span>
                    </span>
                    <h2 class="text-xs font-extrabold tracking-widest text-blue-600 uppercase">Executive AI Report</h2>
                </div>

                <div
                    class="max-w-none text-gray-600 leading-relaxed text-sm prose prose-blue 
                            prose-headings:text-gray-900 prose-headings:font-black prose-headings:tracking-tight
                            prose-h3:text-base prose-h3:mt-8 prose-h3:mb-3 prose-h3:flex prose-h3:items-center prose-h3:gap-2
                            prose-p:mb-4 prose-p:leading-relaxed prose-p:text-gray-500
                            prose-strong:text-gray-900 prose-strong:font-bold
                            prose-ul:list-disc prose-ul:pl-5 prose-ul:my-4 prose-li:my-1.5
                            
                            /* Custom Table Styling (Lebih Clean & Spacing Lega) */
                            prose-table:w-full prose-table:my-6 prose-table:border-hidden
                            prose-th:bg-gray-50/70 prose-th:px-5 prose-th:py-3.5 prose-th:text-left prose-th:text-xs prose-th:font-bold prose-th:text-gray-500 prose-th:uppercase prose-th:tracking-wider prose-th:first:rounded-l-2xl prose-th:last:rounded-r-2xl
                            prose-td:px-5 prose-td:py-4 prose-td:text-gray-600 prose-td:border-b prose-td:border-gray-50 prose-td:text-xs">

                    {!! $aiAnalysis !!}

                </div>

                <div class="pt-6 mt-10 text-center border-t border-gray-50">
                    <p class="text-[10px] font-medium text-gray-400 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-shield-halved"></i> Data ini bersifat rahasia dan dienkripsi khusus untuk
                        keamanan bisnis tenant.
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
