<x-app-layout>
    @section('title', 'Langganan & Penagihan')

    <div class="w-full px-4 py-8 mx-auto space-y-8 sm:px-6 lg:px-8 max-w-7xl">

        {{-- Header Section --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold font-heading text-ink-900">Langganan & Penagihan</h1>
                <p class="text-sm font-body text-ink-600">Kelola paket berlangganan toko Anda untuk menikmati seluruh
                    fitur operasional GrowPOS.</p>
            </div>
        </div>

        {{-- Flash Session Message --}}
        @if (session('warning'))
            <div
                class="flex items-center gap-3 p-4 text-sm font-semibold border rounded-xl text-amber-800 bg-amber-50 border-amber-200">
                <i class="text-lg fa-solid fa-triangle-exclamation text-amber-600 shrink-0"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div
                class="flex items-center gap-3 p-4 text-sm font-semibold border rounded-xl text-emerald-800 bg-emerald-50 border-emerald-200">
                <i class="text-lg fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Status Langganan Saat Ini --}}
        <div class="p-6 border shadow-sm bg-surface-0 border-border-200 rounded-2xl">
            <h2 class="flex items-center gap-2 mb-4 text-lg font-bold text-ink-900">
                <i class="fa-solid fa-shield-halved text-primary-600"></i>
                Status Berlangganan Toko Saat Ini
            </h2>

            @if ($currentSubscription && $currentSubscription->status === 'active' && $currentSubscription->end_date >= now())
                <div
                    class="grid grid-cols-1 gap-4 p-4 border md:grid-cols-3 rounded-xl bg-primary-50 border-primary-200">
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-primary-700">Paket Aktif</span>
                        <p class="text-xl font-bold text-primary-900">
                            {{ $currentSubscription->plan->name ?? 'Custom Plan' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-primary-700">Status
                            Pembayaran</span>
                        <p
                            class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <i class="fa-solid fa-circle-check mr-1.5 text-[10px]"></i> Aktif
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-primary-700">Berlaku
                            Sampai</span>
                        <p class="mt-1 text-sm font-bold text-primary-900">
                            {{ \Carbon\Carbon::parse($currentSubscription->end_date)->translatedFormat('d F Y (H:i)') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 p-4 border md:grid-cols-3 rounded-xl bg-rose-50 border-rose-200">
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-rose-700">Paket Aktif</span>
                        <p class="text-xl font-bold text-rose-900">Belum Berlangganan / Kadaluarsa</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-rose-700">Status</span>
                        <p
                            class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            <i class="fa-solid fa-circle-xmark mr-1.5 text-[10px]"></i> Non-Aktif
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-rose-700">Aksi
                            Diperlukan</span>
                        <p class="mt-1 text-xs font-medium text-rose-800">Pilih salah satu paket di bawah untuk
                            mengaktifkan kembali layanan POS.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Grid Pilihan Paket Langganan --}}
        <div>
            <div class="max-w-xl mx-auto mb-8 text-center">
                <h2 class="text-2xl font-bold text-ink-900">Pilih Paket Pertumbuhan Bisnis</h2>
                <p class="mt-1 text-sm text-ink-600">Tingkatkan atau perpanjang paket langganan Anda kapan saja tanpa
                    biaya tersembunyi.</p>
            </div>

            <div class="grid items-stretch grid-cols-1 gap-6 md:grid-cols-3">
                @forelse ($plans as $plan)
                    @php
                        $isPopular = $plan->slug === 'growth' || $plan->slug === 'pro-monthly';
                        $isCurrent =
                            $currentSubscription &&
                            $currentSubscription->plan_id === $plan->id &&
                            $currentSubscription->status === 'active';
                    @endphp

                    <div @class([
                        'bg-surface-0 rounded-2xl p-6 transition-all duration-300 flex flex-col justify-between relative',
                        'border-2 border-primary-600 shadow-lg scale-100 md:scale-105 z-10' => $isPopular,
                        'border border-border-200 shadow-sm hover:border-primary-300' => !$isPopular,
                    ])>
                        @if ($isPopular)
                            <div
                                class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-primary-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-sm">
                                Paling Populer
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-ink-900">{{ $plan->name }}</h3>
                                @if ($isCurrent)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-full">Paket
                                        Anda</span>
                                @endif
                            </div>

                            <p class="text-xs leading-relaxed text-ink-600">{{ $plan->description }}</p>

                            <div class="flex items-baseline gap-1 font-bold text-primary-600">
                                <span class="text-3xl font-extrabold">
                                    {{ $plan->price == 0 ? 'Rp 0' : 'Rp ' . number_format($plan->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-normal text-ink-400">/ {{ $plan->duration_days }} Hari</span>
                            </div>

                            <hr class="border-border-100">

                            {{-- Fitur Paket --}}
                            <ul class="space-y-2.5 text-xs text-ink-700">
                                <li class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-emerald-600"></i>
                                    <span>Maksimal {{ $plan->max_users }} Kasir / Karyawan</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-emerald-600"></i>
                                    <span>Maksimal {{ $plan->max_products }} SKU Produk</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-emerald-600"></i>
                                    <span>Maksimal {{ $plan->max_outlets }} Outlet Toko</span>
                                </li>

                                @if (is_array($plan->features))
                                    @foreach ($plan->features as $featureKey => $featureVal)
                                        @php
                                            $text = is_numeric($featureKey) ? $featureVal : $featureKey;
                                            $active = is_bool($featureVal) ? $featureVal : true;
                                        @endphp
                                        <li
                                            class="flex items-center gap-2 {{ !$active ? 'text-ink-400 line-through' : '' }}">
                                            <i
                                                class="fa-solid {{ $active ? 'fa-check text-emerald-600' : 'fa-xmark text-rose-400' }}"></i>
                                            <span>{{ $text }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        {{-- Form Action Subscribe --}}
                        <form action="{{ route('billing.subscribe') }}" method="POST" class="mt-8">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" @class([
                                'w-full py-3 rounded-xl font-bold text-xs transition shadow-sm flex items-center justify-center gap-2',
                                'bg-primary-600 hover:bg-primary-700 text-white' =>
                                    $isPopular || !$isCurrent,
                                'bg-surface-200 text-ink-700 hover:bg-surface-300' =>
                                    $isCurrent && !$isPopular,
                            ])>
                                <i class="fa-solid fa-credit-card"></i>
                                <span>{{ $plan->price == 0 ? 'Pilih Paket Gratis' : ($isCurrent ? 'Perpanjang Paket Ini' : 'Pilih Paket Ini') }}</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center border bg-surface-0 rounded-2xl border-border-200">
                        <i class="mb-3 text-4xl fa-solid fa-box-open text-ink-300"></i>
                        <p class="text-sm font-semibold text-ink-700">Belum ada data paket berlangganan.</p>
                        <p class="text-xs text-ink-400">Silakan jalankan seeder `PlanSeeder` di terminal server.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
