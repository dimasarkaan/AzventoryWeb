<section>
    <header>
        <h2 class="text-lg font-medium text-secondary-900">
            <?php echo e(__('Buat Token Akses API Baru')); ?>

        </h2>
        <p class="mt-1 text-sm text-secondary-600">
            <?php echo e(__('Token API ini dapat digunakan untuk mengautentikasi aplikasi pihak ketiga (seperti web e-commerce atau sistem eksternal). Simpan token dengan aman karena hanya ditampilkan sekali.')); ?>

        </p>
    </header>

    <?php if(session('new_api_token')): ?>
        <div class="mt-4 p-4 border border-success-200 bg-success-50 rounded-lg shadow-sm">
            <p class="text-sm font-bold text-success-800 mb-2">Token Berhasil Dibuat!</p>
            <div class="flex items-center gap-2">
                <code class="px-3 py-2 bg-white border border-success-300 rounded-lg font-mono text-sm font-semibold break-all w-full select-all text-success-900"><?php echo e(session('new_api_token')); ?></code>
            </div>
            <p class="mt-2 text-xs font-medium text-success-700">Harap salin token ini sekarang. Anda tidak akan bisa melihatnya lagi setelah memuat ulang halaman.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('profile.api-tokens.store')); ?>" class="mt-6 space-y-4">
        <?php echo csrf_field(); ?>

        <div>
            <?php if (isset($component)) { $__componentOriginalfc65b5c26f06c97d11cb56c10510b4ae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfc65b5c26f06c97d11cb56c10510b4ae = $attributes; } ?>
<?php $component = App\View\Components\InputLabel::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputLabel::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'token_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nama Perangkat / Integrasi'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfc65b5c26f06c97d11cb56c10510b4ae)): ?>
<?php $attributes = $__attributesOriginalfc65b5c26f06c97d11cb56c10510b4ae; ?>
<?php unset($__attributesOriginalfc65b5c26f06c97d11cb56c10510b4ae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc65b5c26f06c97d11cb56c10510b4ae)): ?>
<?php $component = $__componentOriginalfc65b5c26f06c97d11cb56c10510b4ae; ?>
<?php unset($__componentOriginalfc65b5c26f06c97d11cb56c10510b4ae); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal3cf47f8fe36e9fecb8ab96f4432c11f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3cf47f8fe36e9fecb8ab96f4432c11f7 = $attributes; } ?>
<?php $component = App\View\Components\TextInput::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\TextInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'token_name','name' => 'token_name','type' => 'text','class' => 'mt-1 block w-full sm:w-1/2','required' => true,'placeholder' => 'Contoh: Web Ecommerce Utama']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3cf47f8fe36e9fecb8ab96f4432c11f7)): ?>
<?php $attributes = $__attributesOriginal3cf47f8fe36e9fecb8ab96f4432c11f7; ?>
<?php unset($__attributesOriginal3cf47f8fe36e9fecb8ab96f4432c11f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3cf47f8fe36e9fecb8ab96f4432c11f7)): ?>
<?php $component = $__componentOriginal3cf47f8fe36e9fecb8ab96f4432c11f7; ?>
<?php unset($__componentOriginal3cf47f8fe36e9fecb8ab96f4432c11f7); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal824404ceeb4a1e7de17bfcaedf377360 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360 = $attributes; } ?>
<?php $component = App\View\Components\InputError::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('token_name'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $attributes = $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $component = $__componentOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
            <p class="text-xs text-secondary-500 mt-1">Beri nama yang jelas agar Anda mudah mengenalinya.</p>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                <?php echo e(__('Generate Token')); ?>

            </button>
        </div>
    </form>

    <?php if($user->tokens->isNotEmpty()): ?>
        <div class="mt-8 pt-6 border-t border-secondary-200">
            <header class="mb-4">
                <h2 class="text-lg font-medium text-secondary-900">
                    <?php echo e(__('Daftar Token Aktif')); ?>

                </h2>
                <p class="mt-1 text-sm text-secondary-600">
                    <?php echo e(__('Token di bawah ini sedang memiliki izin akses ke sistem API. Jika ada integrasi yang sudah tidak dipakai, segera cabut aksesnya.')); ?>

                </p>
            </header>

            <div class="space-y-3">
                <?php $__currentLoopData = $user->tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-white rounded-lg border border-secondary-200 shadow-sm hover:border-primary-300 transition-colors">
                        <div>
                            <p class="font-bold text-secondary-900"><?php echo e($token->name); ?></p>
                            <p class="text-xs text-secondary-500 mt-0.5">
                                Dibuat: <?php echo e($token->created_at->format('d M Y, H:i')); ?>

                                <?php if($token->last_used_at): ?>
                                    &bull; Terakhir aktif: <?php echo e(\Carbon\Carbon::parse($token->last_used_at)->diffForHumans()); ?>

                                <?php else: ?>
                                    &bull; Belum pernah digunakan
                                <?php endif; ?>
                            </p>
                        </div>
                        <form method="post" action="<?php echo e(route('profile.api-tokens.destroy', $token->id)); ?>" class="shrink-0" onsubmit="return confirm('Apakah Anda yakin ingin mencabut token \'<?php echo e($token->name); ?>\'? Aplikasi yang menggunakannya akan langsung kehilangan akses secara permanen.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('delete'); ?>
                            <button type="submit" class="btn btn-danger py-1.5 px-3 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Cabut Akses
                            </button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Panduan Integrasi API -->
    <!-- Panduan Integrasi API -->
    <div class="mt-8 pt-6 border-t border-secondary-200">
        <details class="group bg-secondary-50 border border-secondary-200 rounded-lg overflow-hidden transition-all duration-300">
            <summary class="flex items-center justify-between p-4 cursor-pointer select-none bg-white hover:bg-secondary-50 group-open:bg-secondary-50 transition-colors">
                <div>
                    <h2 class="text-lg font-medium text-secondary-900">
                        <?php echo e(__('Dokumentasi Integrasi API')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-secondary-600">
                        Gunakan panduan singkat ini untuk menyambungkan web e-commerce atau layanan lain ke sistem Azventory Anda.
                    </p>
                </div>
                <!-- Icon Dropdown -->
                <div class="shrink-0 ml-4 text-secondary-400 group-open:rotate-180 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </summary>
            
            <div class="p-4 pt-0 text-sm space-y-4 font-mono text-secondary-800 overflow-x-auto border-t border-secondary-200">
                <br>
                <p class="font-bold text-secondary-900 mb-1">// Base URL</p>
                <code class="block bg-secondary-900 text-white p-2 rounded"><?php echo e(url('/api/v1')); ?></code>

                <p class="font-bold text-secondary-900 mt-4 mb-1">// Autentikasi (Sertakan di Headers request)</p>
                <code class="block bg-secondary-900 text-white p-2 rounded">Authorization: Bearer <span class="text-primary-300">{api_token_anda}</span><br>Accept: application/json</code>

                <p class="font-bold text-secondary-900 mt-4 mb-1">// Endpoint Utama (Inventaris)</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><span class="text-info-600 font-bold">GET</span> <code>/inventory</code> (Melihat daftar semua barang)</li>
                    <li><span class="text-success-600 font-bold">POST</span> <code>/inventory</code> (Menambahkan barang baru dari luar)</li>
                    <li><span class="text-info-600 font-bold">GET</span> <code>/inventory/{id}</code> (Melihat detail barang spesifik)</li>
                    <li><span class="text-warning-600 font-bold">PUT</span> <code>/inventory/{id}</code> (Update data informasi barang)</li>
                </ul>

                <p class="font-bold text-secondary-900 mt-4 mb-1">// Penyesuaian Stok (Krusial untuk Sinkronisasi E-commerce)</p>
                <p class="text-xs text-secondary-600 font-sans">Gunakan endpoint ini untuk memotong stok secara otomatis ketika ada barang yang terjual di web Toko.</p>
                <div class="bg-secondary-900 text-white p-3 rounded mt-2">
                    <span class="text-warning-400 font-bold">POST</span> <code>/inventory/{id}/adjust-stock</code>
                    <br><br>
                    <span class="text-secondary-400">// Payload JSON (Pengurangan / Pembelian via web)</span><br>
                    {<br>
                    &nbsp;&nbsp;"type": "decrement",<br>
                    &nbsp;&nbsp;"quantity": 1,<br>
                    &nbsp;&nbsp;"description": "Terjual dari Web Ecommerce via Order #INV-1234"<br>
                    }
                    <br><br>
                    <span class="text-secondary-400">// Payload JSON (Restock / Pasokan masuk)</span><br>
                    {<br>
                    &nbsp;&nbsp;"type": "increment",<br>
                    &nbsp;&nbsp;"quantity": 10,<br>
                    &nbsp;&nbsp;"description": "Restock otomatis dari sistem gudang cabang"<br>
                    }
                </div>
            </div>
        </details>
    </div>
</section>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/profile/partials/api-tokens-form.blade.php ENDPATH**/ ?>