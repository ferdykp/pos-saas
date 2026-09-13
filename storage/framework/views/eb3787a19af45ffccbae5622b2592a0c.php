<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startSection('title', 'Ringkasan Usaha'); ?>

    <div class="min-h-screen px-4 py-8 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8 bg-surface-100">

        <!-- Header & Quick CTA -->
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold tracking-wider uppercase text-ink-400">
                    <span><?php echo e(auth()->user()->tenant->name); ?></span>
                    <span>/</span>
                    <span class="text-primary-600">Ringkasan Pemilik</span>
                </div>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl text-ink-900">
                    Bagaimana usahamu hari ini?
                </h1>
                <p class="flex items-center gap-2 mt-1 text-xs sm:text-sm text-ink-700">
                    <i class="fa-regular fa-clock text-ink-400"></i>
                    <span><?php echo e($start->translatedFormat('d M Y')); ?> — <?php echo e($end->translatedFormat('d M Y')); ?></span>
                    <span class="text-ink-400">•</span>
                    <span class="text-ink-700">Transaksi lunas, diperbarui saat dimuat.</span>
                </p>
            </div>
            <a href="<?php echo e(route('pos.index')); ?>"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm shadow-sm transition-all duration-150 group">
                <i class="text-base transition-transform fa-solid fa-cash-register group-hover:scale-110"></i>
                <span>Buka Kasir</span>
                <i class="text-xs opacity-75 fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>

        <!-- Onboarding Banner -->
        <?php if(!$hasMenu || !$hasSale): ?>
            <a href="<?php echo e(route('getting-started')); ?>"
                class="relative flex flex-col items-start justify-between p-6 transition-all duration-200 border shadow-sm group sm:flex-row sm:items-center rounded-xl bg-primary-50 border-primary-100 hover:border-primary-500/30">
                <div class="space-y-1">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-700">
                        <i class="fa-solid fa-flag text-[9px]"></i> Langkah Pertama
                    </span>
                    <h2 class="text-base font-bold transition-colors text-ink-900 group-hover:text-primary-600">
                        <?php echo e(!$hasMenu ? 'Siapkan menu, lalu mulai berjualan.' : 'Menu sudah siap. Yuk, buat transaksi pertama.'); ?>

                    </h2>
                    <p class="text-xs text-ink-700">Panduan singkat, contoh menu, dan fitur impor Excel.</p>
                </div>
                <span
                    class="inline-flex items-center gap-2 mt-4 text-xs font-semibold transition-transform sm:mt-0 text-primary-600 group-hover:translate-x-1">
                    Lanjutkan setup <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
        <?php endif; ?>

        <!-- Date Filter Card -->
        <div class="p-4 border shadow-sm bg-surface-0 border-border-200 rounded-xl">
            <form method="get" class="flex flex-wrap items-end gap-3 sm:gap-4">
                <div class="flex-1 min-w-[140px]">
                    <label class="block mb-1 text-xs font-semibold text-ink-700">Mulai</label>
                    <div class="relative">
                        <input type="date" name="start_date" value="<?php echo e($start->toDateString()); ?>" required
                            class="w-full py-2 pl-3 pr-2 text-xs border rounded-lg outline-none sm:text-sm text-ink-900 bg-surface-0 border-border-200 focus:ring-2 focus:ring-primary-600 focus:border-primary-600">
                    </div>
                </div>
                <div class="flex-1 min-w-[140px]">
                    <label class="block mb-1 text-xs font-semibold text-ink-700">Sampai</label>
                    <div class="relative">
                        <input type="date" name="end_date" value="<?php echo e($end->toDateString()); ?>" required
                            class="w-full py-2 pl-3 pr-2 text-xs border rounded-lg outline-none sm:text-sm text-ink-900 bg-surface-0 border-border-200 focus:ring-2 focus:ring-primary-600 focus:border-primary-600">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="px-4 py-2 text-xs font-semibold text-white transition-colors rounded-lg bg-primary-600 hover:bg-primary-700 sm:text-sm">
                        Tampilkan
                    </button>
                    <a href="<?php echo e(route('dashboard')); ?>"
                        class="px-4 py-2 text-xs font-semibold transition-colors rounded-lg bg-surface-100 hover:bg-border-200 text-ink-700 sm:text-sm">
                        Hari ini
                    </a>
                </div>
            </form>
        </div>

        <?php echo $__env->make('reports.partials.cash-summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Metric 1: Revenue -->
            <div class="flex flex-col justify-between p-5 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-ink-400">Penjualan Lunas</span>
                        <span class="p-2 rounded-lg bg-primary-50 text-primary-600">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-ink-900">Rp<?php echo e(number_format($revenue, 0, ',', '.')); ?></p>
                </div>
                <div class="pt-3 mt-3 text-xs border-t border-border-200">
                    <?php if($change === null): ?>
                        <span class="text-ink-400">Belum ada pembanding</span>
                    <?php else: ?>
                        <span class="font-bold <?php echo e($change >= 0 ? 'text-semantic-success' : 'text-semantic-danger'); ?>">
                            <i class="fa-solid <?php echo e($change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'); ?> text-[10px]"></i>
                            <?php echo e(abs($change)); ?>%
                        </span>
                        <span class="text-ink-700"> vs periode lalu</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metric 2: Orders -->
            <div class="flex flex-col justify-between p-5 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-ink-400">Pesanan Selesai</span>
                        <span class="p-2 rounded-lg bg-primary-50 text-primary-600">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-ink-900"><?php echo e(number_format($orderCount)); ?></p>
                </div>
                <div class="pt-3 mt-3 text-xs border-t border-border-200 text-ink-700">
                    Rata-rata: <span
                        class="font-semibold text-ink-900">Rp<?php echo e(number_format($orderCount ? $revenue / $orderCount : 0, 0, ',', '.')); ?></span>
                    / nota
                </div>
            </div>

            <!-- Metric 3: Gross Profit -->
            <div class="flex flex-col justify-between p-5 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-ink-400">Estimasi Laba Kotor</span>
                        <span class="p-2 rounded-lg bg-accent-100 text-accent-700">
                            <i class="fa-solid fa-chart-line"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-ink-900">
                        <?php echo e($grossProfit === null ? '—' : 'Rp' . number_format($grossProfit, 0, ',', '.')); ?>

                    </p>
                </div>
                <div class="pt-3 mt-3 text-xs border-t border-border-200 text-ink-700">
                    <?php echo e($grossProfit === null ? 'Lengkapi data modal produk.' : 'Penjualan bersih dikurangi modal HPP.'); ?>

                </div>
            </div>

            <!-- Metric 4: Cash Variance -->
            <div class="flex flex-col justify-between p-5 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-ink-400">Selisih Kas Shift</span>
                        <span
                            class="p-2 rounded-lg <?php echo e($cashDifference != 0 ? 'bg-amber-100 text-semantic-warning' : 'bg-surface-100 text-ink-400'); ?>">
                            <i class="fa-solid fa-scale-unbalanced"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-ink-900">Rp<?php echo e(number_format($cashDifference, 0, ',', '.')); ?>

                    </p>
                </div>
                <div class="pt-3 mt-3 text-xs border-t border-border-200">
                    <a href="<?php echo e(route('shifts.index')); ?>"
                        class="inline-flex items-center gap-1 font-semibold text-primary-600 hover:text-primary-700">
                        <span><?php echo e($shiftIssueCount); ?> shift perlu diperiksa</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Section: Insights & Top Selling -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Warnings & Alerts -->
            <div class="flex flex-col justify-between p-6 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-200">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-semantic-warning"></i>
                            <h2 class="text-base font-bold text-ink-900">Perlu Diperhatikan</h2>
                        </div>
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-surface-100 text-ink-700 border border-border-200">
                            Peringatan Otomatis
                        </span>
                    </div>

                    <div class="space-y-3">
                        <?php if($change !== null && $change < 0): ?>
                            <a href="<?php echo e(route('reports.index', ['start_date' => $start->toDateString(), 'end_date' => $end->toDateString()])); ?>"
                                class="flex items-start gap-3 p-3 transition-colors border border-red-100 rounded-lg bg-red-50/50 hover:bg-red-50">
                                <i class="mt-1 fa-solid fa-arrow-trend-down text-semantic-danger"></i>
                                <div class="text-xs">
                                    <p class="font-bold text-ink-900">Penjualan turun <?php echo e(abs($change)); ?>%</p>
                                    <p class="text-ink-700 mt-0.5">Periode sebelumnya
                                        Rp<?php echo e(number_format($previousRevenue, 0, ',', '.')); ?>. Periksa perbandingan item
                                        terjual.</p>
                                </div>
                            </a>
                        <?php endif; ?>

                        <?php $__currentLoopData = $lowStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('inventory.index')); ?>"
                                class="flex items-start gap-3 p-3 transition-colors border rounded-lg bg-amber-50/50 hover:bg-amber-50 border-amber-100">
                                <i class="mt-1 fa-solid fa-box-open text-semantic-warning"></i>
                                <div class="text-xs">
                                    <p class="font-bold text-ink-900">Stok Menipis: <?php echo e($product->product_name); ?></p>
                                    <p class="text-ink-700 mt-0.5">Sisa <?php echo e($product->stock); ?> (Batas minimal
                                        <?php echo e($product->min_stock); ?>).</p>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php $__currentLoopData = $lowMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('materials.index')); ?>"
                                class="flex items-start gap-3 p-3 transition-colors border rounded-lg bg-amber-50/50 hover:bg-amber-50 border-amber-100">
                                <i class="mt-1 fa-solid fa-cubes text-semantic-warning"></i>
                                <div class="text-xs">
                                    <p class="font-bold text-ink-900">Bahan Baku Menipis: <?php echo e($material->name); ?></p>
                                    <p class="text-ink-700 mt-0.5">Sisa <?php echo e($material->stock); ?> <?php echo e($material->unit); ?>.
                                        Segera jadwalkan pengadaan.</p>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php $__currentLoopData = $shiftIssues->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('shifts.index')); ?>"
                                class="flex items-start gap-3 p-3 transition-colors rounded-lg bg-surface-100 hover:bg-border-200">
                                <i class="mt-1 fa-solid fa-user-clock text-ink-400"></i>
                                <div class="text-xs">
                                    <p class="font-bold text-ink-900">Selisih Kas Shift: <?php echo e($shift->user?->name); ?></p>
                                    <p class="text-ink-700 mt-0.5">Terdapat perbedaan kas sebesar
                                        Rp<?php echo e(number_format($shift->cash_difference, 0, ',', '.')); ?>.</p>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($lowStock->isEmpty() && $lowMaterials->isEmpty() && $shiftIssues->isEmpty() && !($change !== null && $change < 0)): ?>
                            <div class="py-8 text-center text-ink-400">
                                <i class="mb-2 text-2xl fa-solid fa-circle-check text-semantic-success"></i>
                                <p class="text-xs font-semibold">Semua aman! Tidak ada kendala stok atau kas shift.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="flex flex-col justify-between p-6 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-200">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-fire text-accent-500"></i>
                            <h2 class="text-base font-bold text-ink-900">Produk Terlaris</h2>
                        </div>
                        <span class="text-xs text-ink-400">Berdasarkan volume</span>
                    </div>

                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $percent = ($product->quantity / max(1, $topProducts->max('quantity'))) * 100;
                            ?>
                            <div>
                                <div class="flex items-center justify-between mb-1 text-xs">
                                    <span class="font-semibold text-ink-900 truncate max-w-[200px] sm:max-w-[280px]">
                                        <?php echo e($product->product_name); ?>

                                    </span>
                                    <span class="font-bold text-primary-600"><?php echo e($product->quantity); ?> terjual</span>
                                </div>
                                <div class="w-full h-2 overflow-hidden rounded-full bg-surface-100">
                                    <div class="h-full transition-all duration-500 rounded-full bg-primary-600"
                                        style="width: <?php echo e($percent); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="py-8 text-center text-ink-400">
                                <i class="mb-2 text-2xl fa-solid fa-chart-bar"></i>
                                <p class="text-xs">Belum ada transaksi pada periode ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Recent Transactions & Payment Channels -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Transactions -->
            <div class="p-6 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-200">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-ink-400"></i>
                        <h2 class="text-base font-bold text-ink-900">Transaksi Terbaru</h2>
                    </div>
                    <a href="<?php echo e(route('orders.index')); ?>"
                        class="flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700">
                        Semua Nota <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-border-200">
                    <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('orders.show', $order)); ?>"
                            class="flex items-center justify-between px-2 py-3 transition-colors rounded-lg hover:bg-surface-100/50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-bold rounded-full bg-primary-50 text-primary-600">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink-900">
                                        <?php echo e($order->customer?->name ?? 'Pelanggan Umum'); ?></p>
                                    <p class="text-[11px] text-ink-400">
                                        <?php echo e($order->user?->name); ?> •
                                        <?php echo e(($order->sold_at ?? $order->created_at)->format('d M H:i')); ?>

                                    </p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-ink-900">
                                Rp<?php echo e(number_format($order->grand_total, 0, ',', '.')); ?>

                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="py-8 text-center text-ink-400">
                            <p class="text-xs">Belum ada transaksi lunas pada periode ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="flex flex-col justify-between p-6 border shadow-sm bg-surface-0 rounded-xl border-border-200">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-200">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-credit-card text-ink-400"></i>
                            <h2 class="text-base font-bold text-ink-900">Pembayaran Masuk</h2>
                        </div>
                        <span class="text-xs text-ink-400">Berdasarkan Kanal</span>
                    </div>

                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div
                                class="flex items-center justify-between p-3 border rounded-lg border-border-200 bg-surface-100/30">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="p-2 text-xs border rounded-lg bg-surface-0 border-border-200 text-primary-600">
                                        <i
                                            class="fa-solid <?php echo e($method->payment_method === 'cash' ? 'fa-money-bill-wave' : 'fa-qrcode'); ?>"></i>
                                    </span>
                                    <span class="text-xs font-bold text-ink-900">
                                        <?php echo e($method->payment_method === 'cash' ? 'Tunai' : 'QRIS'); ?>

                                    </span>
                                </div>
                                <span class="text-xs font-bold text-ink-900">
                                    Rp<?php echo e(number_format($method->amount, 0, ',', '.')); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="py-8 text-center text-ink-400">
                                <p class="text-xs">Belum ada pembayaran masuk.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-4 mt-6 border-t border-border-200">
                    <p class="text-[11px] text-ink-400 leading-relaxed">
                        <i class="fa-solid fa-circle-info"></i> Angka penjualan termasuk pajak. Saldo pencairan QRIS
                        dapat berbeda karena potongan MDR/komisi.
                    </p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-finance')): ?>
                        <a href="<?php echo e(route('finance.index')); ?>"
                            class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 mt-3 text-xs font-semibold transition-colors rounded-lg bg-surface-100 hover:bg-border-200 text-ink-900">
                            <span>Lihat Dompet Toko</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/ferdy/project-fl/pos-saas/resources/views/dashboard/index.blade.php ENDPATH**/ ?>