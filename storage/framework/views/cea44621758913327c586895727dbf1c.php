<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="<?php echo e(asset('logo.svg')); ?>?v=2" type="image/svg+xml">

        <!-- Skrip -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 transition-opacity duration-300 opacity-0"
             x-data="{}"
             x-init="$el.classList.remove('opacity-0')">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Judul Halaman -->
            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <!-- Konten Halaman -->
            <main>
                <?php echo e($slot); ?>

            </main>
        </div>
        
        <?php if (isset($component)) { $__componentOriginalf040c20db64ab702409000fc38889411 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf040c20db64ab702409000fc38889411 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.spotlight-search','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('spotlight-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf040c20db64ab702409000fc38889411)): ?>
<?php $attributes = $__attributesOriginalf040c20db64ab702409000fc38889411; ?>
<?php unset($__attributesOriginalf040c20db64ab702409000fc38889411); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf040c20db64ab702409000fc38889411)): ?>
<?php $component = $__componentOriginalf040c20db64ab702409000fc38889411; ?>
<?php unset($__componentOriginalf040c20db64ab702409000fc38889411); ?>
<?php endif; ?>

        <?php echo $__env->yieldPushContent('scripts'); ?>
        <script>
            // Teruskan Pesan Flash ke JS Kustom
            window.flashMessages = {
                <?php if(session('success')): ?>
                    success: "<?php echo e(session('success')); ?>",
                <?php endif; ?>
                <?php if(session('error')): ?>
                    error: "<?php echo e(session('error')); ?>",
                <?php endif; ?>
                <?php if(session('warning')): ?>
                    warning: "<?php echo e(session('warning')); ?>",
                <?php endif; ?>
            };
        </script>
    </body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/layouts/app.blade.php ENDPATH**/ ?>