<x-app-layout>
    @section('title', 'Smart AI Business Advisor & Assistant')

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-desktop" x-data="aiChatHandler()">

        <!-- Header Halaman -->
        <div
            class="flex flex-col justify-between gap-4 pb-4 mb-6 border-b sm:flex-row sm:items-center border-border-200">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 text-lg text-white rounded-md shadow-sm bg-primary-600 shrink-0">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold leading-tight font-heading md:text-2xl text-ink-900">
                        Smart AI Advisor & Assistant
                    </h1>
                    <p class="font-body text-xs text-ink-700 mt-0.5">
                        Analisis otomatis performa outlet & asisten percakapan interaktif berbasis data riil 30 hari
                        terakhir.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700">
                    <span class="w-2 h-2 rounded-full bg-primary-600 animate-pulse"></span>
                    AI Engine Active
                </span>
            </div>
        </div>

        {{-- Disclaimer data terbatas: jujur ke pemilik toko kalau insight masih indikatif --}}
        @isset($dataCukup)
            @if (!$dataCukup)
                <div
                    class="flex items-center gap-2 p-3 mb-6 text-xs text-blue-800 border border-blue-200 rounded-lg bg-blue-50">
                    <i class="text-blue-500 fa-solid fa-circle-info"></i>
                    <span>Data transaksi Anda masih terbatas. Insight AI di bawah ini bersifat indikatif dan akan
                        semakin akurat seiring bertambahnya transaksi.</span>
                </div>
            @endif
        @endisset

        <!-- Metric Stat Banner -->
        <div class="grid grid-cols-2 gap-3 mb-6 lg:grid-cols-5">
            <div
                class="bg-surface-0 p-3.5 rounded-lg border border-border-200 shadow-sm flex items-center justify-between">
                <div>
                    <span
                        class="font-body text-[11px] font-semibold text-ink-700 uppercase tracking-wider block">Rata-rata
                        Omzet / Hari</span>
                    <p class="mt-1 font-mono text-base font-semibold md:text-lg text-primary-600">
                        Rp {{ number_format($rataRataOmsetHarian, 0, ',', '.') }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center w-8 h-8 text-xs rounded-md bg-primary-50 text-primary-600 shrink-0">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <div
                class="bg-surface-0 p-3.5 rounded-lg border border-border-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="font-body text-[11px] font-semibold text-ink-700 uppercase tracking-wider block">Hari
                        Teramai</span>
                    <p class="mt-1 text-sm font-semibold font-heading md:text-base text-accent-700">
                        {{ $hariTeramaiIndo }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center w-8 h-8 text-xs rounded-md bg-accent-100 text-accent-700 shrink-0">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
            </div>

            <div
                class="bg-surface-0 p-3.5 rounded-lg border border-border-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="font-body text-[11px] font-semibold text-ink-700 uppercase tracking-wider block">Avg
                        Basket Size</span>
                    <p class="mt-1 font-mono text-base font-semibold md:text-lg text-ink-900">
                        Rp {{ number_format($rataRataNilaiPerNota, 0, ',', '.') }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center w-8 h-8 text-xs rounded-md bg-primary-100 text-primary-700 shrink-0">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
            </div>

            <div
                class="bg-surface-0 p-3.5 rounded-lg border border-border-200 shadow-sm flex items-center justify-between">
                <div class="min-w-0 pr-2">
                    <span class="font-body text-[11px] font-semibold text-ink-700 uppercase tracking-wider block">Produk
                        Juara</span>
                    <p class="mt-1 text-xs font-semibold truncate font-heading md:text-sm text-accent-700"
                        title="{{ $produkJuara }}">
                        {{ $produkJuara }}
                    </p>
                </div>
                <div
                    class="flex items-center justify-center w-8 h-8 text-xs rounded-md bg-accent-100 text-accent-700 shrink-0">
                    <i class="fa-solid fa-crown"></i>
                </div>
            </div>

            {{-- Kartu baru: Tren 7 hari terakhir --}}
            <div
                class="bg-surface-0 p-3.5 rounded-lg border border-border-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="font-body text-[11px] font-semibold text-ink-700 uppercase tracking-wider block">Tren
                        7 Hari</span>
                    @isset($growthPercent)
                        @if ($growthPercent !== null)
                            <p
                                class="mt-1 font-mono text-base font-semibold md:text-lg {{ $growthPercent >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}%
                            </p>
                        @else
                            <p class="mt-1 text-xs font-semibold text-ink-400">Belum Cukup Data</p>
                        @endif
                    @else
                        <p class="mt-1 text-xs font-semibold text-ink-400">Belum Cukup Data</p>
                    @endisset
                </div>
                <div
                    class="flex items-center justify-center w-8 h-8 text-xs rounded-md shrink-0
                        {{ isset($growthPercent) && $growthPercent !== null && $growthPercent < 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                    <i
                        class="fa-solid {{ isset($growthPercent) && $growthPercent !== null && $growthPercent < 0 ? 'fa-arrow-trend-down' : 'fa-arrow-trend-up' }}"></i>
                </div>
            </div>
        </div>

        {{-- Panel Kombo Cerdas: fitur pembeda GrowPOS -- rekomendasi bundling
             berbasis data pembelian riil, bukan sekadar tebakan produk laris + sepi --}}
        @isset($comboPairs)
            @if ($comboPairs->isNotEmpty())
                <div class="p-4 mb-6 border rounded-lg bg-violet-50 border-violet-200">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="text-sm fa-solid fa-wand-magic-sparkles text-violet-600"></i>
                        <h3 class="text-xs font-bold tracking-wider uppercase font-heading text-violet-900">
                            Kombo Cerdas — Produk yang Sering Dibeli Bersamaan
                        </h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($comboPairs as $pair)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border rounded-full border-violet-200 text-violet-800">
                                {{ $pair->produk_a }} <i class="text-[10px] fa-solid fa-plus text-violet-400"></i>
                                {{ $pair->produk_b }}
                                <span class="ml-1 text-violet-500">({{ $pair->frekuensi }}x)</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        @endisset

        <!-- Split Screen Layout -->
        <div class="grid items-start grid-cols-1 gap-6 lg:grid-cols-12">

            <!-- Left Panel: AI Executive Analysis Report -->
            <div
                class="flex flex-col overflow-hidden border rounded-lg shadow-sm lg:col-span-7 bg-surface-0 border-border-200">
                <div class="h-1 bg-primary-600"></div>

                <div class="flex-1 p-5 md:p-6">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-border-200">
                        <div class="flex items-center gap-2">
                            <i class="text-sm fa-solid fa-file-contract text-primary-600"></i>
                            <h2 class="text-xs font-bold tracking-wider uppercase font-heading text-ink-900">
                                Executive AI Business Report
                            </h2>
                        </div>
                        <span class="font-mono text-[11px] text-ink-400">Diperbarui Hari Ini</span>
                    </div>

                    <div
                        class="font-body text-xs md:text-sm text-ink-900 leading-relaxed prose prose-emerald max-w-none
                                prose-headings:font-heading prose-headings:font-bold prose-headings:text-ink-900
                                prose-h3:text-sm prose-h3:mt-4 prose-h3:mb-2 prose-h3:text-primary-700
                                prose-p:text-ink-700 prose-p:mb-3 prose-p:text-xs md:prose-p:text-sm
                                prose-strong:text-ink-900 prose-strong:font-semibold
                                prose-ul:list-disc prose-ul:pl-4 prose-ul:my-2
                                prose-table:w-full prose-table:my-3 prose-table:border-collapse
                                prose-th:bg-surface-100 prose-th:px-3 prose-th:py-2 prose-th:text-left prose-th:text-[11px] prose-th:font-heading prose-th:text-ink-700 prose-th:uppercase
                                prose-td:px-3 prose-td:py-2 prose-td:border-b prose-td:border-border-200 prose-td:text-xs">

                        {!! $aiAnalysis !!}

                    </div>
                </div>

                <div class="px-5 py-3 text-center border-t bg-surface-100/60 border-border-200">
                    <p class="font-body text-[11px] text-ink-400 flex items-center justify-center gap-1.5">
                        <i class="text-xs fa-solid fa-shield-halved"></i>
                        <span>Rekomendasi dianalisis secara rahasia berdasarkan transaksi fisik toko Anda.</span>
                    </p>
                </div>
            </div>

            <!-- Right Panel: Interactive AI Chat Engine -->
            <div
                class="lg:col-span-5 bg-surface-0 border border-border-200 rounded-lg shadow-sm flex flex-col h-[680px] sticky top-6">

                <!-- Chat Header -->
                <div class="flex items-center justify-between p-4 border-b bg-surface-100 border-border-200">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white rounded-full bg-primary-600">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold leading-tight font-heading md:text-sm text-ink-900">
                                Tanya GrowPOS AI
                            </h3>
                            <span class="font-body text-[10px] text-primary-600 font-medium block">
                                Online • Mengingat konteks percakapan Anda
                            </span>
                        </div>
                    </div>

                    <button type="button" @click="clearChat()"
                        class="p-1.5 text-ink-400 hover:text-ink-900 text-xs rounded-md transition-colors"
                        title="Reset Percakapan">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>

                <!-- Quick Prompts -->
                <div
                    class="p-3 bg-surface-0 border-b border-border-200 overflow-x-auto custom-scrollbar flex items-center gap-1.5 shrink-0 whitespace-nowrap">
                    <button type="button" @click="sendQuickPrompt('Bagaimana cara meningkatkan omzet toko saya?')"
                        class="px-2.5 py-1 bg-surface-100 hover:bg-primary-50 hover:text-primary-700 border border-border-200 rounded-full font-body text-[11px] text-ink-700 transition-colors">
                        💡 Cara naikkan omzet
                    </button>
                    <button type="button" @click="sendQuickPrompt('Menu mana yang sebaiknya didiskon?')"
                        class="px-2.5 py-1 bg-surface-100 hover:bg-primary-50 hover:text-primary-700 border border-border-200 rounded-full font-body text-[11px] text-ink-700 transition-colors">
                        🏷️ Rekomendasi Promo
                    </button>
                    <button type="button" @click="sendQuickPrompt('Apa bahan baku yang stoknya sedang kritis?')"
                        class="px-2.5 py-1 bg-surface-100 hover:bg-primary-50 hover:text-primary-700 border border-border-200 rounded-full font-body text-[11px] text-ink-700 transition-colors">
                        📦 Analisis Restock
                    </button>
                </div>

                <!-- Chat Stream Message Area -->
                <div class="flex-1 p-4 overflow-y-auto custom-scrollbar space-y-3.5" id="chatStream">

                    <!-- Welcome Message (Fit Content) -->
                    <div class="flex items-start gap-2.5 max-w-[85%]">
                        <div
                            class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div
                            class="inline-block p-3 border rounded-lg rounded-tl-none bg-surface-100 border-border-200">
                            <p class="text-xs leading-relaxed font-body text-ink-900">
                                Halo Boss! Saya Asisten AI GrowPOS. Ada yang ingin ditanyakan terkait strategi
                                penjualan, menu terlaris, atau saran efisiensi toko Anda?
                            </p>
                            <span class="font-mono text-[9px] text-ink-400 mt-1 block">Baru Saja</span>
                        </div>
                    </div>

                    <!-- Dynamic Chat Items (Menggunakan w-fit agar bubble mengikuti isi teks) -->
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex items-start gap-2.5"
                            :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'">

                            <template x-if="msg.sender === 'ai'">
                                <div
                                    class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                    <i class="fa-solid fa-robot"></i>
                                </div>
                            </template>

                            <div class="p-3 rounded-lg border text-xs font-body leading-relaxed max-w-[80%] w-fit break-words"
                                :class="msg.sender === 'user' ?
                                    'bg-primary-600 text-white border-primary-600 rounded-tr-none' :
                                    'bg-surface-100 text-ink-900 border-border-200 rounded-tl-none'">
                                <p x-text="msg.text" class="whitespace-pre-line"></p>
                                <span class="font-mono text-[9px] mt-1 block"
                                    :class="msg.sender === 'user' ? 'text-white/70 text-right' : 'text-ink-400'"
                                    x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Loading Indicator -->
                    <div x-show="isLoading" class="flex items-center gap-2.5">
                        <div
                            class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-[10px] shrink-0">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div
                            class="bg-surface-100 p-2.5 rounded-lg border border-border-200 flex items-center gap-1.5 w-fit">
                            <span class="w-1.5 h-1.5 bg-primary-600 rounded-full animate-bounce"></span>
                            <span
                                class="w-1.5 h-1.5 bg-primary-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span
                                class="w-1.5 h-1.5 bg-primary-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                    </div>

                    <!-- Suggested Follow-up Chips: AI proaktif menyarankan pertanyaan lanjutan -->
                    <div x-show="suggestions.length > 0 && !isLoading" class="flex flex-wrap gap-1.5 pl-8">
                        <template x-for="(sug, sIndex) in suggestions" :key="sIndex">
                            <button type="button" @click="sendQuickPrompt(sug)"
                                class="px-2.5 py-1 bg-white hover:bg-primary-50 hover:text-primary-700 border border-primary-200 rounded-full font-body text-[11px] text-primary-600 transition-colors">
                                <i class="fa-solid fa-arrow-turn-up rotate-90 text-[9px] mr-1"></i>
                                <span x-text="sug"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Chat Input Area -->
                <div class="p-3 border-t bg-surface-100/50 border-border-200">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                        <input type="text" x-model="inputQuery" placeholder="Ketik pertanyaan strategi bisnis..."
                            class="flex-1 px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100">

                        <button type="submit" :disabled="!inputQuery.trim() || isLoading"
                            class="flex items-center justify-center text-white transition-colors rounded-md h-11 w-11 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 shrink-0">
                            <i class="text-xs fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <!-- JavaScript Handling AI Chat -->
    <script>
        function aiChatHandler() {
            return {
                inputQuery: '',
                isLoading: false,
                messages: [],
                suggestions: [],

                sendQuickPrompt(promptText) {
                    this.inputQuery = promptText;
                    this.sendMessage();
                },

                async sendMessage() {
                    if (!this.inputQuery.trim() || this.isLoading) return;

                    const userText = this.inputQuery.trim();
                    const now = new Date().toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Ambil snapshot histori SEBELUM pesan baru ditambahkan,
                    // supaya backend tahu urutan percakapan sebelumnya (untuk multi-turn context).
                    const historySnapshot = this.messages.slice(-6).map(m => ({
                        sender: m.sender,
                        text: m.text
                    }));

                    this.messages.push({
                        sender: 'user',
                        text: userText,
                        time: now
                    });
                    this.inputQuery = '';
                    this.isLoading = true;
                    this.suggestions = [];
                    this.scrollToBottom();

                    try {
                        const response = await fetch("{{ route('reports.ai-chat') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message: userText,
                                history: historySnapshot
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.reply) {
                            this.messages.push({
                                sender: 'ai',
                                text: data.reply,
                                time: new Date().toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                            });
                            this.suggestions = data.suggestions || [];
                        } else {
                            this.messages.push({
                                sender: 'ai',
                                text: data.reply || data.message ||
                                    'Maaf, terjadi kesalahan respon dari sistem AI.',
                                time: new Date().toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                            });
                            this.suggestions = data.suggestions || [];
                        }
                    } catch (error) {
                        console.error("AI Chat Error:", error);
                        this.messages.push({
                            sender: 'ai',
                            text: 'Gagal terhubung ke server AI. Pastikan koneksi internet aktif.',
                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        });
                    } finally {
                        this.isLoading = false;
                        this.scrollToBottom();
                    }
                },

                clearChat() {
                    this.messages = [];
                    this.suggestions = [];
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('chatStream');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
