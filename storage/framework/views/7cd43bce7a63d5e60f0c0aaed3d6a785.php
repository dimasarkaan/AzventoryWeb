<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($title); ?></title>
    <?php echo $__env->make('reports.partials.pdf_style', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <?php echo $__env->make('reports.partials.pdf_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;"><?php echo e(__('ui.no_column')); ?></th>
                <th style="width: 26%;"><?php echo e(__('ui.item_name_column')); ?></th>
                <th style="width: 13%;"><?php echo e(__('ui.category_column')); ?></th>
                <th style="width: 13%;"><?php echo e(__('ui.brand_column')); ?></th>
                <th style="width: 15%;"><?php echo e(__('ui.location_column')); ?></th>
                <th style="width: 10%;"><?php echo e(__('ui.stock_column')); ?></th>
                <th style="width: 18%;"><?php echo e(__('ui.status_column')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="text-align:center;"><?php echo e($index + 1); ?></td>
                <td>
                    <strong><?php echo e($item->name); ?></strong><br>
                    <small style="color: #666;">PN: <?php echo e($item->part_number); ?></small>
                </td>
                <td><?php echo e($item->category); ?></td>
                <td><?php echo e($item->brand); ?></td>
                <td><?php echo e($item->location); ?></td>
                <td style="text-align: center;"><?php echo e($item->stock); ?> <?php echo e($item->unit); ?></td>
                <td>
                    <?php if($item->stock == 0): ?>
                        <span class="badge badge-danger"><?php echo e(__('ui.status_out_of_stock')); ?></span>
                    <?php elseif($item->minimum_stock > 0 && $item->stock <= $item->minimum_stock): ?>
                        <span class="badge badge-warning"><?php echo e(__('ui.stock_low')); ?></span>
                    <?php elseif($item->minimum_stock > 0 && $item->stock <= round($item->minimum_stock * 1.5)): ?>
                        <span class="badge badge-warning" style="background:#fff7ed;color:#92400e;border-color:#fbbf24;">Mendekati Minimum</span>
                    <?php else: ?>
                        <span class="badge badge-success"><?php echo e(__('ui.stock_safe')); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/reports/pdf_inventory_list.blade.php ENDPATH**/ ?>