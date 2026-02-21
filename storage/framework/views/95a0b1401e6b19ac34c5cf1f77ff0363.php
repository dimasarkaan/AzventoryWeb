<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-secondary-200">
    <?php if(isset($header)): ?>
        <div class="px-6 py-4 border-b border-secondary-200">
            <h3 class="font-semibold text-lg text-secondary-800 leading-tight">
                <?php echo e($header); ?>

            </h3>
        </div>
    <?php endif; ?>

    <div class="p-6 text-secondary-900">
        <?php echo e($slot); ?>

    </div>
</div><?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/card.blade.php ENDPATH**/ ?>