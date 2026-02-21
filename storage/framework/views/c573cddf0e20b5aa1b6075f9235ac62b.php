<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Azventory — Solusi Manajemen Stok Azzahra Computer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo e(asset('favicon.png')); ?>" type="image/png">

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans text-secondary-900 antialiased bg-white overflow-x-hidden selection:bg-primary-100 selection:text-primary-900" x-data="scrollReveal()">
    
    <!-- Navbar -->
    <nav class="glass-nav px-4 sm:px-6 py-4 flex items-center justify-center relative z-50">
        <div class="max-w-7xl w-full flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-lg sm:text-xl shadow-lg shadow-primary-500/30">
                    A
                </div>
                <span class="text-xl sm:text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-700 to-primary-500 text-primary-600">
                    Azventory
                </span>
            </a>
            
            <div class="flex items-center">
                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('login')),'class' => 'px-5 py-2']); ?>
                    Masuk
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $attributes = $__attributesOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $component = $__componentOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__componentOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-[85vh] flex flex-col items-center justify-center pt-10 sm:pt-20 pb-20 text-center px-4 sm:px-6 z-10">
        <div class="max-w-4xl mx-auto w-full">
            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black tracking-tight leading-[1.15] sm:leading-[1.1] text-secondary-900 mb-6 reveal-on-scroll delay-100">
                Solusi Manajemen Stok<br>
                <span class="text-primary-500">Azzahra Computer</span>
            </h1>

            <!-- Subheadline -->
            <p class="text-lg lg:text-xl text-secondary-500 max-w-2xl mx-auto mb-10 leading-relaxed reveal-on-scroll delay-200">
                Platform terintegrasi untuk mengelola stok komputer, laptop, spareparts di Azzahra Computer Tegal.
            </p>
            
            <!-- CTAs -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center reveal-on-scroll delay-300 w-full max-w-sm sm:max-w-none mx-auto sm:mx-0 items-center">
                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('login')),'class' => 'px-8 sm:px-10 py-3.5 sm:py-4 text-base sm:text-lg rounded-xl shadow-xl shadow-primary-500/25 group gap-2']); ?>
                    Masuk Aplikasi
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $attributes = $__attributesOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $component = $__componentOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__componentOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click.prevent' => 'document.getElementById(\'features\').scrollIntoView({ behavior: \'smooth\' })','href' => '#features','class' => 'px-8 sm:px-10 py-3.5 sm:py-4 text-base sm:text-lg rounded-xl']); ?>
                    Fitur Utama
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $attributes = $__attributesOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $component = $__componentOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__componentOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-24 sm:py-32 z-10 bg-white border-t border-secondary-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 lg:gap-10 text-left">
                <!-- Card 1 -->
                <div class="p-8 sm:p-10 rounded-[28px] border-2 border-secondary-100 bg-white shadow-xl shadow-secondary-200/20 hover:shadow-2xl hover:shadow-primary-500/15 hover:border-primary-200 transition-all duration-500 group reveal-on-scroll delay-100 flex flex-col items-start">
                    <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 mb-8 group-hover:bg-primary-600 group-hover:text-white transition-all duration-300 shadow-inner relative overflow-hidden">
                        <div class="group-hover:animate-bounce">
                            <?php if (isset($component)) { $__componentOriginalc867a7d0834788820c9284b3decea570 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc867a7d0834788820c9284b3decea570 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.inventory','data' => ['class' => 'w-7 h-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.inventory'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-7 h-7']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc867a7d0834788820c9284b3decea570)): ?>
<?php $attributes = $__attributesOriginalc867a7d0834788820c9284b3decea570; ?>
<?php unset($__attributesOriginalc867a7d0834788820c9284b3decea570); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc867a7d0834788820c9284b3decea570)): ?>
<?php $component = $__componentOriginalc867a7d0834788820c9284b3decea570; ?>
<?php unset($__componentOriginalc867a7d0834788820c9284b3decea570); ?>
<?php endif; ?>
                        </div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-secondary-900 mb-4 sm:mb-5">Manajemen Stok Terpusat</h3>
                    <p class="text-secondary-500 leading-relaxed text-sm sm:text-base">Pantau ketersediaan barang di berbagai lokasi gudang secara real-time.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 sm:p-10 rounded-[28px] border-2 border-secondary-100 bg-white shadow-xl shadow-secondary-200/20 hover:shadow-2xl hover:shadow-primary-500/15 hover:border-primary-200 transition-all duration-500 group reveal-on-scroll delay-200 flex flex-col items-start">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-8 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-inner relative overflow-hidden">
                        <?php if (isset($component)) { $__componentOriginal4a23095320439a806c351cc06b90a972 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4a23095320439a806c351cc06b90a972 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.scan-qr','data' => ['class' => 'w-7 h-7 relative z-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.scan-qr'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-7 h-7 relative z-10']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4a23095320439a806c351cc06b90a972)): ?>
<?php $attributes = $__attributesOriginal4a23095320439a806c351cc06b90a972; ?>
<?php unset($__attributesOriginal4a23095320439a806c351cc06b90a972); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4a23095320439a806c351cc06b90a972)): ?>
<?php $component = $__componentOriginal4a23095320439a806c351cc06b90a972; ?>
<?php unset($__componentOriginal4a23095320439a806c351cc06b90a972); ?>
<?php endif; ?>
                        <!-- Laser Effect -->
                        <div class="absolute inset-0 bg-purple-400/30 -translate-y-full group-hover:animate-scan z-0"></div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-secondary-900 mb-4 sm:mb-5">QR Code Scanner</h3>
                    <p class="text-secondary-500 leading-relaxed text-sm sm:text-base">Identifikasi aset instan dengan teknologi pemindaian QR Code.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 sm:p-10 rounded-[28px] border-2 border-secondary-100 bg-white shadow-xl shadow-secondary-200/20 hover:shadow-2xl hover:shadow-primary-500/15 hover:border-primary-200 transition-all duration-500 group reveal-on-scroll delay-300 flex flex-col items-start">
                    <div class="w-14 h-14 bg-success-100 rounded-xl flex items-center justify-center text-success-600 mb-8 group-hover:bg-success-600 group-hover:text-white transition-all duration-300 shadow-inner relative">
                        <svg class="w-7 h-7 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <!-- Pulse Effect -->
                        <div class="absolute inset-0 rounded-xl bg-success-400/20 animate-ping group-hover:block hidden"></div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-secondary-900 mb-4 sm:mb-5">Monitoring Aktivitas</h3>
                    <p class="text-secondary-500 leading-relaxed text-sm sm:text-base">Rekam jejak digital lengkap untuk setiap pergerakan barang (masuk/keluar) oleh pengguna.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary-50/30 py-12 text-center border-t border-secondary-100 z-10 relative">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-secondary-500 text-xs sm:text-sm font-medium">
                &copy; <?php echo e(date('Y')); ?> Azzahra Computer, dibuat oleh : <span class="text-secondary-900 font-bold">Dimas Arkaan</span>
            </p>
        </div>
    </footer>

    <style>
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-on-scroll.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }

        @keyframes scan {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(200%); }
        }
        .animate-scan {
            animation: scan 1.5s linear infinite;
        }
    </style>

    <script>
        function scrollReveal() {
            return {
                init() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('revealed');
                            }
                        });
                    }, { threshold: 0.1 });

                    document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
                        observer.observe(el);
                    });
                }
            }
        }
    </script>
</body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/welcome.blade.php ENDPATH**/ ?>