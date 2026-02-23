<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Azventory</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo e(asset('logo.svg')); ?>?v=2" type="image/svg+xml">

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans text-secondary-900 antialiased bg-white overflow-x-hidden selection:bg-primary-100 selection:text-primary-900" x-data="{ ...scrollReveal(), scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    
    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300"
         :class="{ 'bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm py-0': scrolled, 'bg-transparent py-2': !scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex items-center justify-between h-16">
            <a href="/" class="flex items-center shrink-0">
                <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'h-5 lg:h-6 w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 lg:h-6 w-auto']); ?>
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
            
            <div class="flex items-center shadow-md rounded-lg text-sm">
                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('login')),'class' => 'px-5 py-2 hover:-translate-y-0.5 transition-transform duration-200']); ?>
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
    <section class="relative flex flex-col items-center justify-center min-h-[100dvh] pt-20 pb-10 text-center px-4 sm:px-6 z-10 bg-white overflow-hidden">
        <!-- Grid/Dot Pattern Background -->
        <div class="absolute inset-0 z-0 opacity-[0.3]" style="background-image: radial-gradient(#94a3b8 1px, transparent 1px); background-size: 32px 32px;"></div>
        
        <!-- Ambient Glows -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-300/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-300/20 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

        <div class="max-w-4xl mx-auto w-full relative z-10">
            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black tracking-tight leading-[1.15] sm:leading-[1.1] text-secondary-900 mb-6 reveal-on-scroll delay-100">
                Solusi Manajemen Stok<br>
                <span class="text-primary-600">Azzahra Computer</span>
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
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('login')),'class' => 'px-7 sm:px-8 py-3 text-base font-semibold rounded-lg shadow-lg shadow-primary-500/25 group gap-2 hover:-translate-y-1 transition-all duration-300']); ?>
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
<?php $component->withAttributes(['@click.prevent' => 'document.getElementById(\'features\').scrollIntoView({ behavior: \'smooth\' })','href' => '#features','class' => 'px-7 sm:px-8 py-3 text-base font-semibold rounded-lg shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 bg-white border-2 border-primary-50 text-secondary-600 hover:text-primary-600']); ?>
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

    <!-- Spacer to push Logo Card out of initial viewport -->
    <div class="h-40 sm:h-56 bg-white relative z-10 w-full"></div>

    <!-- Features Section -->
    <section id="features" class="relative pt-32 sm:pt-40 pb-16 sm:pb-20 z-20 bg-[#cceaff] rounded-t-[2rem] sm:rounded-t-[3rem] shadow-[0_-10px_30px_-15px_rgba(0,0,0,0.1)] text-secondary-900 border-t border-white/50">
        
        <!-- Overlapping Logo Card -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-5xl px-4 sm:px-6 lg:px-8 z-30">
            <div class="bg-white rounded-[2rem] shadow-[0_15px_40px_rgb(0,0,0,0.06)] px-6 py-6 sm:px-10 sm:py-8 border border-gray-100 reveal-on-scroll delay-300">
                <p class="text-center text-[10px] sm:text-xs font-bold text-secondary-400 mb-4 sm:mb-2 tracking-widest uppercase">Dikembangkan sebagai bagian dari Tugas Akhir untuk:</p>
                <div class="flex flex-col md:flex-row justify-center items-center divide-y md:divide-y-0 md:divide-x divide-gray-100 min-h-[80px]">
                    <!-- Telkom -->
                    <div class="flex-1 py-4 md:py-0 px-4 md:px-8 flex justify-center items-center h-full w-full">
                        <img src="<?php echo e(asset('images/logo/logo_telkomuniversity.png')); ?>" alt="Telkom University" class="h-10 sm:h-12 w-auto object-contain">
                    </div>
                    <!-- SI -->
                    <div class="flex-1 py-4 md:py-0 px-4 md:px-8 flex justify-center items-center h-full w-full">
                        <img src="<?php echo e(asset('images/logo/logo_sisteminformasi.png')); ?>" alt="Sistem Informasi Telkom University" class="h-8 sm:h-10 w-auto object-contain">
                    </div>
                    <!-- Azzahra -->
                    <div class="flex-1 py-4 md:py-0 px-4 md:px-8 flex justify-center items-center h-full w-full">
                        <img src="<?php echo e(asset('images/logo/logo_azzahracomputer.png')); ?>" alt="Azzahra Computer" class="h-10 sm:h-12 w-auto object-contain">
                    </div>
                </div>
            </div>
        </div>

        <!-- Inner Pattern for Blue Section -->
        <div class="absolute inset-0 z-0 opacity-15" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 mb-8 sm:mb-10 text-center mt-32 sm:mt-0">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.15] sm:leading-[1.1] text-secondary-900 mb-3 sm:mb-4 reveal-on-scroll">
                Mengapa Memilih <br class="hidden sm:block" />
                <span class="text-primary-600">Azventory?</span>
            </h2>
            <p class="text-secondary-500 text-sm sm:text-base leading-relaxed reveal-on-scroll delay-100 max-w-xl mx-auto">
                Desain modern yang dipadukan dengan performa handal untuk manajemen stok.
            </p>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Connecting Wavy Line (hidden on mobile) -->
            <div class="absolute top-[64px] left-0 right-0 h-[2px] -z-10 hidden md:block opacity-60">
                <svg width="100%" height="150" viewBox="0 0 100 100" preserveAspectRatio="none" class="overflow-visible">
                     <!-- A smooth curve passing through the center of cards vertically -->
                     <path d="M-10,0 C25,0 30,65 50,65 C70,65 75,0 110,0" fill="none" stroke="#93c5fd" stroke-width="1.5" vector-effect="non-scaling-stroke" />
                </svg>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-6 lg:gap-10 items-start">
                <!-- Card 1 -->
                <div class="relative p-8 sm:px-10 sm:py-10 rounded-3xl bg-white text-secondary-900 hover:bg-primary-900 hover:text-white hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary-900/40 transition-all duration-500 group reveal-on-scroll delay-100 flex flex-col items-center text-center shadow-lg h-full cursor-pointer border border-gray-100">
                    <div class="w-16 h-16 bg-orange-50 group-hover:bg-white/10 rounded-full flex items-center justify-center text-orange-500 group-hover:text-white mb-6 group-hover:-translate-y-1 group-hover:scale-110 transition-all duration-500 shadow-sm group-hover:shadow-lg">
                        <?php if (isset($component)) { $__componentOriginalc867a7d0834788820c9284b3decea570 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc867a7d0834788820c9284b3decea570 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.inventory','data' => ['class' => 'w-8 h-8 opacity-90 group-hover:animate-bounce']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.inventory'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 opacity-90 group-hover:animate-bounce']); ?>
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
                    <h3 class="text-xl font-bold mb-3 tracking-wide transition-colors duration-500">Manajemen Terpusat</h3>
                    <p class="text-secondary-500 group-hover:text-primary-100 leading-relaxed text-sm transition-colors duration-500">Pantau ketersediaan barang di berbagai lokasi gudang secara real-time penuh.</p>
                </div>

                <!-- Card 2 (Lowered) -->
                <div class="relative p-8 sm:px-10 sm:py-10 rounded-3xl bg-white text-secondary-900 hover:bg-primary-900 hover:text-white hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary-900/40 transition-all duration-500 group reveal-on-scroll delay-200 flex flex-col items-center text-center shadow-lg h-full md:mt-16 cursor-pointer border border-gray-100">
                    <div class="w-16 h-16 bg-primary-50 group-hover:bg-white/10 rounded-full flex items-center justify-center text-primary-600 group-hover:text-white mb-6 group-hover:-translate-y-1 group-hover:scale-110 transition-all duration-500 relative overflow-hidden shadow-sm group-hover:shadow-lg">
                        <?php if (isset($component)) { $__componentOriginal4a23095320439a806c351cc06b90a972 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4a23095320439a806c351cc06b90a972 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.scan-qr','data' => ['class' => 'w-8 h-8 opacity-90 group-hover:animate-pulse relative z-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.scan-qr'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 opacity-90 group-hover:animate-pulse relative z-10']); ?>
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
                    </div>
                    <h3 class="text-xl font-bold mb-3 tracking-wide transition-colors duration-500">QR Code Scanner</h3>
                    <p class="text-secondary-500 group-hover:text-primary-100 leading-relaxed text-sm transition-colors duration-500">Identifikasi aset instan dengan teknologi pemindaian pintar menggunakan QR Code.</p>
                </div>

                <!-- Card 3 -->
                <div class="relative p-8 sm:px-10 sm:py-10 rounded-3xl bg-white text-secondary-900 hover:bg-primary-900 hover:text-white hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary-900/40 transition-all duration-500 group reveal-on-scroll delay-300 flex flex-col items-center text-center shadow-lg h-full cursor-pointer border border-gray-100">
                    <div class="w-16 h-16 bg-red-50 group-hover:bg-white/10 rounded-full flex items-center justify-center text-red-500 group-hover:text-white mb-6 group-hover:-translate-y-1 group-hover:scale-110 transition-all duration-500 shadow-sm group-hover:shadow-lg">
                        <svg class="w-8 h-8 opacity-90 group-hover:animate-bounce transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 tracking-wide transition-colors duration-500">Monitoring Aktivitas</h3>
                    <p class="text-secondary-500 group-hover:text-primary-100 leading-relaxed text-sm transition-colors duration-500">Rekam jejak digital lengkap untuk setiap pergerakan barang (masuk/keluar).</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#cceaff] py-6 text-center z-20 relative border-t border-primary-200">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-secondary-600 text-xs sm:text-sm font-medium">
                &copy; <?php echo e(date('Y')); ?> Azzahra Computer, dibuat oleh : <span class="text-secondary-900 font-bold tracking-wide">Dimas Arkaan</span>
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