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
    <?php $__env->startSection('title', 'Edit Informasi Tenant'); ?>

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-modal-lg">

        <!-- Breadcrumb & Title Section -->
        <div class="mb-6">
            <a href="<?php echo e(route('tenants.index')); ?>"
                class="inline-flex items-center gap-2 mb-2 text-xs font-semibold font-body text-primary-600 hover:text-primary-700">
                <i class="text-xs fa-solid fa-arrow-left"></i>
                <span>Kembali ke Manajemen Tenant</span>
            </a>
            <h1 class="font-heading font-bold text-2xl md:text-[28px] text-ink-900 leading-tight">
                Edit Profil Tenant
            </h1>
            <p class="mt-1 text-xs font-body md:text-sm text-ink-700">
                Perbarui rincian profil & kontak operasional untuk outlet <span
                    class="font-semibold text-ink-900"><?php echo e($tenant->name); ?></span>.
            </p>
        </div>

        <div x-data="{ isSubmitting: false }">
            <form action="<?php echo e(route('tenants.update', $tenant->id)); ?>" method="POST" enctype="multipart/form-data"
                class="space-y-6" @submit="isSubmitting = true">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Card Content utama (Radius-lg = 16px, Padding = 24px) -->
                <div class="p-6 space-y-5 border rounded-lg shadow-sm bg-surface-0 border-border-200">

                    <!-- Logo Upload Field -->
                    <div x-data="{ imgPreview: '<?php echo e($tenant->img_logo ? asset('storage/' . $tenant->img_logo) : null); ?>' }">
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Logo Toko / Outlet (Opsional)
                        </label>

                        <div
                            class="flex items-center gap-4 p-4 border border-dashed rounded-md border-border-200 bg-surface-100">
                            <div
                                class="flex items-center justify-center w-16 h-16 overflow-hidden border rounded-md bg-surface-0 border-border-200 shrink-0">
                                <template x-if="imgPreview">
                                    <img :src="imgPreview" class="object-cover w-full h-full">
                                </template>
                                <template x-if="!imgPreview">
                                    <i class="text-xl fa-solid fa-store text-ink-400"></i>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0">
                                <input type="file" name="img_logo" accept="image/*"
                                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imgPreview = e.target.result; }; reader.readAsDataURL(file); }"
                                    class="block w-full text-xs transition-all cursor-pointer text-ink-700 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-100 file:text-primary-700 hover:file:bg-primary-600 hover:file:text-white" />
                                <p class="mt-1.5 text-[11px] text-ink-400">Format: JPG, PNG, WEBP. Maks. 2MB.</p>
                            </div>
                        </div>
                        <?php $__errorArgs = ['img_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs font-semibold text-semantic-danger"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Nama Toko Field (Height 44px, Radius-sm = 6px) -->
                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Nama Bisnis / Outlet <span class="text-semantic-danger">*</span>
                        </label>
                        <input type="text" name="name" value="<?php echo e(old('name', $tenant->name)); ?>"
                            class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100"
                            required />
                    </div>

                    <!-- Tipe Bisnis Field -->
                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Kategori / Jenis Bisnis <span class="text-semantic-danger">*</span>
                        </label>
                        <select name="business_type" required
                            class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100">
                            <?php $__currentLoopData = \App\Support\BusinessProfile::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($value); ?>" <?php if(old('business_type', $tenant->businessType()) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
                    </div>

                    <!-- Email & Phone Grid -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                                Email Bisnis <span class="text-semantic-danger">*</span>
                            </label>
                            <input type="email" name="email" value="<?php echo e(old('email', $tenant->email)); ?>"
                                class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100"
                                required />
                        </div>

                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                                Nomor WhatsApp <span class="text-semantic-danger">*</span>
                            </label>
                            <input type="text" name="phone" value="<?php echo e(old('phone', $tenant->phone)); ?>"
                                class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100"
                                required />
                        </div>
                    </div>

                    <!-- Alamat Outlet -->
                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Alamat Operasional Outlet <span class="text-semantic-danger">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                            class="w-full p-3 text-xs transition-all border rounded-sm outline-none font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100"
                            placeholder="Alamat lengkap outlet bisnis Anda..."><?php echo e(old('address', $tenant->address)); ?></textarea>
                    </div>
                </div>

                <!-- Action Button Submit -->
                <div class="pt-2">
                    <button type="submit" :disabled="isSubmitting"
                        class="inline-flex items-center justify-center w-full gap-2 px-6 text-xs font-semibold text-white transition-all rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 active:bg-primary-900 font-body md:text-sm disabled:opacity-60 disabled:cursor-not-allowed">
                        <i x-show="isSubmitting" class="text-sm fa-solid fa-circle-notch fa-spin" x-cloak></i>
                        <span x-text="isSubmitting ? 'Menyimpan Perubahan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
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
<?php /**PATH /Users/ferdy/project-fl/pos-saas/resources/views/tenants/edit.blade.php ENDPATH**/ ?>