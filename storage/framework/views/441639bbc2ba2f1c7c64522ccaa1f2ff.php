<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aktivitas Sistem</title>
    <?php echo $__env->make('reports.partials.pdf_style', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc; display: inline-block; }
        .badge-info { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .badge-danger { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .badge-success { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .meta { font-size: 9pt; margin-bottom: 15px; color: #444; }
    </style>
</head>
<body>
    <?php
        // Construct Title and Period Strings for the Header Partial
        $title = mb_strtoupper(__('ui.report_activity_title'));
        
        $request = $request ?? request(); // Ensure request is available from Job or live View
        
        if($request->get('start_date') && $request->get('end_date')) {
            $period = \Carbon\Carbon::parse($request->get('start_date'))->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($request->get('end_date'))->translatedFormat('d F Y');
            $startDate = \Carbon\Carbon::parse($request->get('start_date'));
            $endDate = \Carbon\Carbon::parse($request->get('end_date'));
        } elseif($request->get('start_date')) {
            $period = __('ui.since_date', ['date' => \Carbon\Carbon::parse($request->get('start_date'))->translatedFormat('d F Y')]);
            $startDate = \Carbon\Carbon::parse($request->get('start_date'));
            $endDate = null;
        } elseif($request->get('end_date')) {
            $period = __('ui.until_date', ['date' => \Carbon\Carbon::parse($request->get('end_date'))->translatedFormat('d F Y')]);
            $startDate = null;
            $endDate = \Carbon\Carbon::parse($request->get('end_date'));
        } else {
            $period = __('ui.all_history');
            $startDate = null;
            $endDate = null;
        }
        
        $type = 'activity_log';
    ?>

    <?php echo $__env->make('reports.partials.pdf_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="meta">
        <?php if($request->get('role') && $request->get('role') !== 'Semua Role'): ?>
            <strong><?php echo e(__('ui.role_filter')); ?>:</strong> <?php echo e(ucfirst($request->get('role'))); ?> &nbsp; | &nbsp;
        <?php endif; ?>
        <?php if($request->get('user_id')): ?>
            <strong>User ID:</strong> <?php echo e($request->get('user_id')); ?> &nbsp; | &nbsp;
        <?php endif; ?>
        <?php if($request->get('action')): ?>
            <strong><?php echo e(__('ui.action_type')); ?>:</strong> <?php echo e($request->get('action')); ?> &nbsp; | &nbsp;
        <?php endif; ?>
        <?php if($request->get('search')): ?>
            <strong><?php echo e(__('ui.keyword_label')); ?></strong> "<?php echo e($request->get('search')); ?>"
        <?php endif; ?>
    </div>

    <?php
        $isPdf = $isPdf ?? true;
    ?>

    <table style="width: <?php echo e($isPdf ? '100%' : 'auto'); ?>;">
        <thead>
            <tr>
                <th style="width: 15%"><?php echo e(__('ui.time_header')); ?></th>
                <th style="width: 20%"><?php echo e(__('ui.user_header')); ?></th>
                <th style="width: 10%"><?php echo e(__('ui.role_filter')); ?></th>
                <th style="width: 20%"><?php echo e(__('ui.action_header')); ?></th>
                <th style="width: 35%"><?php echo e(__('ui.description_header')); ?></th>
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
</body>
</html>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/reports/activity_logs/pdf.blade.php ENDPATH**/ ?>