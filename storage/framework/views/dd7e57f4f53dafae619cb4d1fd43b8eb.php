<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e(config('app.name', 'Azventory')); ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans text-secondary-900 antialiased bg-gray-50 flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            
            <!-- Top Pattern/Color Section -->
            <div class="relative h-40 bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center overflow-hidden">
                <!-- Decorative Circles in Header -->
                <div class="absolute top-0 left-0 w-32 h-32 bg-white opacity-20 rounded-full mix-blend-overlay -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-primary-200 opacity-20 rounded-full mix-blend-multiply translate-x-1/3 translate-y-1/3"></div>
                
                <!-- Wave SVG Separator -->
                <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10">
                    <svg class="relative block w-[calc(100%+1.3px)] h-[50px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-white"></path>
                    </svg>
                </div>
            </div>

            <!-- Main Content -->
            <div class="relative px-8 pb-10 text-center">
                
                <!-- Overlapping Icon -->
                <div class="relative -mt-20 mb-6 flex justify-center z-20">
                     <?php echo $__env->yieldContent('image'); ?>
                </div>

                <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-800 mb-2 font-outfit tracking-tight">
                    <?php echo $__env->yieldContent('code'); ?>
                </h1>
                
                <h2 class="text-2xl font-bold text-gray-800 mb-3 px-4">
                    <?php echo $__env->yieldContent('message'); ?>
                </h2>
                
                <p class="text-gray-500 mb-8 max-w-sm mx-auto leading-relaxed text-sm lg:text-base">
                    <?php echo $__env->yieldContent('description'); ?>
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full px-4">
                    <a href="<?php echo e(url()->previous()); ?>" class="group relative inline-flex items-center justify-center px-6 py-3 text-base font-medium text-gray-700 transition-all duration-200 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <?php echo e(__('ui.error_btn_back')); ?>

                    </a>
                    
                    <a href="/" class="group relative inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white transition-all duration-200 bg-primary-600 border border-transparent rounded-xl hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 shadow-lg shadow-primary-600/30 hover:shadow-primary-600/50 w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <?php echo e(__('ui.error_btn_home')); ?>

                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/layouts/error.blade.php ENDPATH**/ ?>