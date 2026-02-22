<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Azventory')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="<?php echo e(asset('logo.svg')); ?>?v=2" type="image/svg+xml">

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans text-secondary-900 antialiased bg-white">
        <div class="min-h-screen flex">
            <!-- Left Side - Content/Branding -->
            <div class="hidden lg:flex w-1/2 bg-primary-600 relative overflow-hidden items-center justify-center p-12">
                 <!-- Background Circle Decoration -->
                <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary-500 opacity-50 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-[500px] h-[500px] rounded-full bg-primary-700 opacity-50 blur-3xl"></div>
                
                <div class="relative z-10 text-white max-w-lg">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 shadow-lg">
                            <?php if (isset($component)) { $__componentOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.logo','data' => ['variant' => 'white','class' => 'h-7 w-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'white','class' => 'h-7 w-7']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9)): ?>
<?php $attributes = $__attributesOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9; ?>
<?php unset($__attributesOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9)): ?>
<?php $component = $__componentOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9; ?>
<?php unset($__componentOriginal2b6f9fe004ca6dd33a48f4f6eb431ad9); ?>
<?php endif; ?>
                        </div>
                        <h1 class="text-4xl font-bold tracking-tight">Azventory</h1>
                    </div>
                    <h2 class="text-3xl font-bold leading-tight mb-6"><?php echo e(__('ui.guest_welcome_title')); ?></h2>
                    <p class="text-primary-100 text-lg leading-relaxed">
                        <?php echo e(__('ui.guest_welcome_desc')); ?>

                    </p>
                    
                     <div class="mt-12 flex gap-4 text-sm font-medium text-primary-200">
                        <div class="flex items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <span><?php echo e(__('ui.guest_feature_stock')); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <span><?php echo e(__('ui.guest_feature_qr')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-background">
                <div class="w-full max-w-md">
                     <!-- Mobile Logo (Visible only on small screens) -->
                    <div class="flex lg:hidden justify-center mb-8">
                         <a href="/" class="flex items-center">
                            <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'h-6 sm:h-7 w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-6 sm:h-7 w-auto']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                        </a>
                    </div>
                    
                    <?php echo e($slot); ?>

                    
                    <div class="mt-8 text-center text-sm text-secondary-400">
                        &copy; <?php echo e(date('Y')); ?> Azventory Project.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/layouts/guest.blade.php ENDPATH**/ ?>