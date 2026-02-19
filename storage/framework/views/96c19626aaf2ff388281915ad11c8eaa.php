

<?php $__env->startSection('title', __('ui.error_404_title')); ?>

<?php $__env->startSection('image'); ?>
    <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mx-auto text-primary-500 shadow-xl border-4 border-white relative z-10">
        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('code', '404'); ?>
<?php $__env->startSection('message', __('ui.error_404_title')); ?>
<?php $__env->startSection('description', __('ui.error_404_desc')); ?>

<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/errors/404.blade.php ENDPATH**/ ?>