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
    <?php $__env->startSection('title', 'Manajemen Tenant SaaS'); ?>

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-desktop" x-data="{ showDeleteModal: false, deleteUrl: '', tenantName: '' }">

        <?php
            $tenantCount = $tenants->count();
            $currentPlan = auth()->user()->tenant?->currentPlan();
            $maxOutlets = $currentPlan?->max_outlets ?? 1;
            $isOutletFull = $tenantCount >= $maxOutlets;
        ?>

        <!-- Banner Flash Status/Notification -->
        <?php if(session('status')): ?>
            <div
                class="flex items-center gap-3 p-4 mb-6 text-sm font-medium border-l-4 rounded-md shadow-sm bg-primary-50 border-primary-600 text-ink-900">
                <i class="text-base fa-solid fa-circle-check text-primary-600"></i>
                <span><?php echo e(session('status')); ?></span>
            </div>
        <?php endif; ?>

        <!-- Banner Kuota Cabang/Tenant -->
        <div
            class="flex flex-col justify-between gap-3 p-4 mb-6 border rounded-lg shadow-sm sm:flex-row sm:items-center bg-surface-0 border-border-200">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-50 text-primary-600 shrink-0">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-ink-900">Kuota Cabang Bisnis (Paket
                        <?php echo e($currentPlan?->name ?? 'Starter'); ?>)</h4>
                    <p class="text-[11px] text-ink-700 mt-0.5">
                        Anda telah mendaftarkan <strong
                            class="<?php echo e($isOutletFull ? 'text-semantic-danger' : 'text-primary-600'); ?>"><?php echo e($tenantCount); ?></strong>
                        dari maksimal <strong class="text-ink-900"><?php echo e($maxOutlets); ?></strong> cabang/toko.
                    </p>
                </div>
            </div>

            <?php if($isOutletFull): ?>
                <a href="<?php echo e(route('billing.index')); ?>"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-md transition shrink-0">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Upgrade Cabang</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Header Halaman & Tombol Tambah Tenant -->
        <div
            class="flex flex-col justify-between gap-4 pb-6 mb-8 border-b sm:flex-row sm:items-center border-border-200">
            <div>
                <h1 class="font-heading font-bold text-2xl md:text-[28px] text-ink-900 leading-tight">
                    Manajemen Tenant (Cabang & Toko)
                </h1>
                <p class="mt-1 text-xs font-body md:text-sm text-ink-700">
                    Kelola outlet/cabang bisnis mitra atau buat tenant baru dalam ekosistem GrowPOS.
                </p>
            </div>

            <?php if($isOutletFull): ?>
                <button disabled title="Kuota cabang paket <?php echo e($currentPlan?->name); ?> sudah penuh"
                    class="inline-flex items-center justify-center gap-2 px-5 text-xs font-semibold border rounded-md cursor-not-allowed text-ink-400 bg-surface-100 border-border-200 h-11 opacity-60 font-body md:text-sm shrink-0">
                    <i class="text-xs fa-solid fa-lock"></i>
                    <span>Kuota Cabang Penuh</span>
                </button>
            <?php else: ?>
                <a href="<?php echo e(route('tenants.create')); ?>"
                    class="inline-flex items-center justify-center gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 active:bg-primary-900 font-body md:text-sm shrink-0">
                    <i class="text-xs fa-solid fa-plus"></i>
                    <span>Tambah Tenant Baru</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- 3 Cards Metric Statistik -->
        <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-3 md:gap-6">
            <div class="p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <p class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Total Tenant Mitra</p>
                <p class="mt-2 font-mono text-2xl font-semibold md:text-3xl text-ink-900"><?php echo e($tenants->count()); ?></p>
            </div>

            <div class="p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <p class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Pengguna / Staf Aktif
                </p>
                <p class="mt-2 font-mono text-2xl font-semibold md:text-3xl text-primary-600">
                    <?php echo e(\App\Models\User::count()); ?></p>
            </div>

            <div class="p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <p class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Pertumbuhan Bulan Ini
                </p>
                <p class="mt-2 font-mono text-2xl font-semibold md:text-3xl text-accent-500">
                    +<?php echo e($tenants->where('created_at', '>=', now()->startOfMonth())->count()); ?>

                </p>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-hidden border rounded-lg shadow-sm bg-surface-0 border-border-200">
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr
                            class="h-12 text-xs font-semibold tracking-wider uppercase border-b bg-surface-100 border-border-200 font-heading text-ink-700">
                            <th class="px-5 py-3">Nama Toko / Mitra</th>
                            <th class="px-5 py-3">Tipe Bisnis</th>
                            <th class="px-5 py-3 text-center">Jumlah Staf</th>
                            <th class="px-5 py-3 text-center">Status Sesi</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y font-body md:text-sm text-ink-900 divide-border-200">
                        <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr
                                class="h-12 transition-colors <?php echo e(auth()->user()->tenant_id == $tenant->id ? 'bg-primary-50/40' : 'hover:bg-surface-100/50'); ?>">

                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <?php if($tenant->img_logo): ?>
                                            <img src="<?php echo e(asset('storage/' . $tenant->img_logo)); ?>"
                                                class="object-cover border rounded-md w-9 h-9 border-border-200 shrink-0">
                                        <?php else: ?>
                                            <div
                                                class="flex items-center justify-center text-xs font-bold uppercase border rounded-md w-9 h-9 font-heading text-primary-700 border-primary-100 bg-primary-50 shrink-0">
                                                <?php echo e(substr($tenant->name, 0, 2)); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <span
                                                class="block font-semibold leading-tight truncate text-ink-900"><?php echo e($tenant->name); ?></span>
                                            <span
                                                class="font-mono text-[11px] text-ink-400 font-normal truncate block mt-0.5">
                                                ID: <?php echo e($tenant->id); ?> • <?php echo e($tenant->email); ?>

                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-3 font-medium text-ink-700">
                                    <?php echo e($tenant->business_type ?? 'Lainnya'); ?>

                                </td>

                                <td class="px-5 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold text-ink-700 bg-surface-100 border border-border-200 rounded-full">
                                        <?php echo e($tenant->users_count ?? $tenant->users()->count()); ?> Orang
                                    </span>
                                </td>

                                <td class="px-5 py-3 text-center">
                                    <?php if(auth()->user()->tenant_id == $tenant->id): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-semibold text-primary-700 bg-primary-100 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary-600 animate-pulse"></span>
                                            Aktif Digunakan
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold text-ink-400 bg-surface-100 rounded-full">
                                            Standby
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <?php if(auth()->user()->tenant_id != $tenant->id): ?>
                                            <form action="<?php echo e(route('tenants.switch', ['tenant' => $tenant->id])); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                    class="h-8 px-3 text-xs font-semibold transition-colors rounded-md text-primary-700 bg-primary-100 hover:bg-primary-600 hover:text-white">
                                                    Ganti Toko
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <a href="<?php echo e(route('tenants.edit', $tenant->id)); ?>"
                                            class="p-2 transition-colors rounded-md text-ink-700 hover:text-accent-700 bg-surface-100 hover:bg-accent-100"
                                            title="Edit Information">
                                            <i class="text-xs fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <button type="button"
                                            @click="showDeleteModal = true; deleteUrl = '<?php echo e(route('tenants.destroy', $tenant->id)); ?>'; tenantName = '<?php echo e($tenant->name); ?>'"
                                            class="p-2 transition-colors rounded-md text-ink-700 hover:text-semantic-danger bg-surface-100 hover:bg-red-50"
                                            title="Hapus Tenant">
                                            <i class="text-xs fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="mb-2 text-3xl fa-solid fa-store-slash text-ink-400"></i>
                                        <p class="text-sm font-semibold font-body text-ink-900">Belum ada tenant
                                            terdaftar</p>
                                        <p class="font-body text-xs text-ink-700 mt-0.5">Mulai dengan menambahkan outlet
                                            atau bisnis pertama Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Delete Confirmation -->
        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/40 backdrop-blur-sm" x-cloak>

            <div class="w-full p-6 border rounded-lg shadow-lg max-w-modal-sm bg-surface-0 border-border-200"
                @click.away="showDeleteModal = false">

                <div
                    class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-50 text-semantic-danger">
                    <i class="text-xl fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="mb-6 text-center">
                    <h3 class="text-lg font-semibold font-heading text-ink-900">Hapus Cabang Bisnis?</h3>
                    <p class="mt-2 text-xs leading-relaxed font-body text-ink-700">
                        Apakah Anda yakin ingin menghapus <span class="font-semibold text-ink-900"
                            x-text="tenantName"></span>? Seluruh data produk, kategori, dan histori transaksi toko ini
                        akan terhapus secara permanen.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="flex-1 px-4 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                        Batal
                    </button>

                    <form :action="deleteUrl" method="POST" class="flex-1">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                            class="w-full px-4 text-xs font-semibold text-white transition-colors rounded-md h-11 bg-semantic-danger hover:bg-red-700 font-body">
                            Ya, Hapus
                        </button>
                    </form>
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
<?php /**PATH /Users/ferdy/project-fl/pos-saas/resources/views/tenants/index.blade.php ENDPATH**/ ?>