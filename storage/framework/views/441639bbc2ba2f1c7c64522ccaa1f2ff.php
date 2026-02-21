<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Aktivitas Sistem</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #1e40af; }
        .header p { margin: 5px 0; color: #666; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; font-size: 9pt; }
        th { background-color: #4b5563; color: #ffffff; font-weight: bold; text-transform: uppercase; text-align: center; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc; display: inline-block; }
        .badge-info { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .badge-danger { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .badge-success { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
        .meta { font-size: 9pt; margin-bottom: 15px; color: #444; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-transform: uppercase;"><?php echo e(__('ui.report_activity_title')); ?></h1>
        <p>
            <?php echo e(__('ui.period_label')); ?> 
            <?php if(request('start_date') && request('end_date')): ?>
                <?php echo e(\Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y')); ?> - <?php echo e(\Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y')); ?>

            <?php elseif(request('start_date')): ?>
                <?php echo e(__('ui.since_date', ['date' => \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y')])); ?>

            <?php elseif(request('end_date')): ?>
                <?php echo e(__('ui.until_date', ['date' => \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y')])); ?>

            <?php else: ?>
                <?php echo e(__('ui.all_history')); ?>

            <?php endif; ?>
        </p>
    </div>

    <div class="meta">
        <?php if(request('role') && request('role') !== 'Semua Role'): ?>
            <strong><?php echo e(__('ui.role_filter')); ?>:</strong> <?php echo e(ucfirst(request('role'))); ?> &nbsp; | &nbsp;
        <?php endif; ?>
        <?php if(request('user_id')): ?>
            <strong>User ID:</strong> <?php echo e(request('user_id')); ?> &nbsp; | &nbsp;
        <?php endif; ?>
        <?php if(request('action')): ?>
            <strong><?php echo e(__('ui.action_type')); ?>:</strong> <?php echo e(request('action')); ?> &nbsp; | &nbsp;
        <?php endif; ?>
        <?php if(request('search')): ?>
            <strong><?php echo e(__('ui.keyword_label')); ?></strong> "<?php echo e(request('search')); ?>"
        <?php endif; ?>
    </div>

    <?php
        $isPdf = $isPdf ?? true;
    ?>

    <table style="width: <?php echo e($isPdf ? '100%' : 'auto'); ?>;">
        <thead>
            <tr>
                <th style="<?php echo e($isPdf ? 'width: 15%' : 'width: 120px'); ?>"><?php echo e(__('ui.time_header')); ?></th>
                <th style="<?php echo e($isPdf ? 'width: 20%' : 'width: 150px'); ?>"><?php echo e(__('ui.user_header')); ?></th>
                <th style="<?php echo e($isPdf ? 'width: 10%' : 'width: 100px'); ?>"><?php echo e(__('ui.role_filter')); ?></th>
                <th style="<?php echo e($isPdf ? 'width: 20%' : 'width: 180px'); ?>"><?php echo e(__('ui.action_header')); ?></th>
                <th style="<?php echo e($isPdf ? 'width: 35%' : 'width: 400px'); ?>"><?php echo e(__('ui.description_header')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($log->created_at->format('d/m/Y H:i')); ?></td>
                    <td><?php echo e($log->user->name ?? __('ui.system_user')); ?></td>
                    <td style="text-align: center;">
                        <?php
                            $role = $log->user->role ?? null;
                            $badgeClass = match($role) {
                                \App\Enums\UserRole::SUPERADMIN => 'badge-danger',
                                \App\Enums\UserRole::ADMIN => 'badge-warning',
                                \App\Enums\UserRole::OPERATOR => 'badge-info',
                                default => 'badges'
                            };
                            $roleLabel = $role instanceof \App\Enums\UserRole ? $role->label() : ($role ?? '-');
                        ?>
                        <span class="badges <?php echo e($badgeClass); ?>"><?php echo e($roleLabel); ?></span>
                    </td>
                    <td>
                        <span style="font-weight: bold; color: #4b5563;"><?php echo e($log->action); ?></span>
                    </td>
                    <td>
                        <?php echo e($log->description); ?>

                        <?php if($log->properties && is_array($log->properties)): ?>
                            <div style="margin-top: 5px; font-size: 8.5pt; color: #555; background: #fff; padding: 4px; border: 1px dashed #ccc;">
                                <strong><?php echo e(__('ui.change_details')); ?></strong><br>
                                <?php $__currentLoopData = $log->properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(is_array($change) && isset($change['old'], $change['new'])): ?>
                                        &bull; <?php echo e(ucfirst($key)); ?>: <span style="text-decoration: line-through; color: #ef4444;"><?php echo e($change['old']); ?></span> &rarr; <span style="color: #10b981; font-weight: bold;"><?php echo e($change['new']); ?></span><br>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;"><?php echo e(__('ui.no_data_filtered')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Azventory &bull; <?php echo e(__('ui.report_footer_printed_by', ['name' => auth()->user()->name, 'date' => now()->translatedFormat('d F Y H:i')])); ?></p>
    </div>
</body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/reports/activity_logs/pdf.blade.php ENDPATH**/ ?>