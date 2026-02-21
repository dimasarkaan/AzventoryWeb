<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($title); ?></title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #1e40af; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #4b5563; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 9pt; text-align: center; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-transform: uppercase;"><?php echo e($title); ?></h1>
        <p style="font-size: 10pt; margin-top: 5px;">
            <?php if($startDate && $endDate): ?>
                <?php echo e(__('ui.period_label')); ?> <?php echo e($startDate->translatedFormat('d F Y')); ?> - <?php echo e($endDate->translatedFormat('d F Y')); ?>

            <?php else: ?>
                <?php echo e(__('ui.period_label')); ?> <?php echo e(__('ui.all_history')); ?>

            <?php endif; ?>
            &nbsp; | &nbsp;
            <?php echo e(__('ui.location_label')); ?> <?php echo e($location == 'all' ? __('ui.all_locations') : $location); ?>

        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;"><?php echo e(__('ui.no_column')); ?></th>
                <th style="width: 25%;"><?php echo e(__('ui.item_name_column')); ?></th>
                <th style="width: 15%;"><?php echo e(__('ui.category_column')); ?></th>
                <th style="width: 15%;"><?php echo e(__('ui.brand_column')); ?></th>
                <th style="width: 15%;"><?php echo e(__('ui.location_column')); ?></th>
                <th style="width: 10%;"><?php echo e(__('ui.stock_column')); ?></th>
                <th style="width: 15%;"><?php echo e(__('ui.status_column')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
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
                        <span class="badges badge-danger"><?php echo e(__('ui.status_out_of_stock')); ?></span>
                    <?php elseif($item->minimum_stock > 0 && $item->stock <= $item->minimum_stock): ?>
                        <span class="badges badge-warning"><?php echo e(__('ui.stock_low')); ?></span>
                    <?php else: ?>
                        <span class="badges badge-success"><?php echo e(__('ui.stock_safe')); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Azventory &bull; <?php echo e(__('ui.report_footer_printed_by', ['name' => auth()->user()->name, 'date' => now()->translatedFormat('d F Y H:i')])); ?></p>
    </div>
</body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/reports/pdf_inventory_list.blade.php ENDPATH**/ ?>