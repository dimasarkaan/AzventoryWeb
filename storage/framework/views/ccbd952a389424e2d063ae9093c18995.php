<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        <?php echo e(__('ui.notification_title')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">
                        <?php echo e(__('ui.notification_desc')); ?>

                    </p>
                </div>
                 <?php if(!$notifications->isEmpty()): ?>
                <form action="<?php echo e(route('notifications.markAllRead')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-secondary text-xs">
                        <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <?php echo e(__('ui.notification_mark_all_read')); ?>

                    </button>
                </form>
                <?php endif; ?>
            </div>

            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div x-data="{ 
                            read: <?php echo e($notification->read_at ? 'true' : 'false'); ?>,
                            markRead() {
                                if (this.read) return;
                                axios.patch('<?php echo e(route('notifications.read', $notification->id)); ?>')
                                    .then(() => { 
                                        this.read = true; 
                                        window.dispatchEvent(new CustomEvent('notification-read'));
                                    })
                                    .catch(err => console.error(err));
                            }
                        }"
                        }"
                        @click="markRead()"
                        class="card group relative transition-all duration-200 overflow-hidden cursor-pointer"
                        :class="read ? 'bg-white opacity-60 border border-secondary-100' : 'bg-white shadow-md border-l-4 border-l-primary-500 border-y border-r border-secondary-100'"
                    >
                        <div class="p-4 flex items-start justify-between gap-4">
                            <div class="flex gap-4 w-full">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-sm font-semibold text-secondary-900" :class="{ 'font-normal text-secondary-600': read }">
                                            <?php echo e($notification->data['title'] ?? __('ui.notification_default_title')); ?>

                                        </h4>
                                    </div>
                                    <p class="text-sm text-secondary-600 mb-2 leading-relaxed" :class="{ 'text-secondary-400': read }">
                                        <?php echo e($notification->data['message'] ?? __('ui.notification_default_message')); ?>

                                    </p>
                                    <p class="text-xs text-secondary-400">
                                        <?php echo e($notification->created_at->diffForHumans()); ?> &bull; <?php echo e($notification->created_at->format('d M Y, H:i')); ?>

                                    </p>
                                </div>
                            </div>

                            <!-- Action Button (Detail) -->
                            <div class="flex-shrink-0 pointer-events-auto self-center" @click.stop>
                                <form action="<?php echo e(route('notifications.read', $notification->id)); ?>" method="POST" <?php echo e($notification->type === 'App\Notifications\ReportReadyNotification' ? 'target=_blank' : ''); ?>>
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-secondary-200 text-xs font-medium rounded-lg text-secondary-600 bg-white hover:bg-secondary-50 hover:text-primary-600 transition-colors shadow-sm">
                                        <?php echo e(__('ui.notification_action_detail')); ?>

                                        <svg class="ml-1.5 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="card p-12 text-center flex flex-col items-center">
                         <div class="h-12 w-12 bg-secondary-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-base font-medium text-secondary-900"><?php echo e(__('ui.notification_empty_title')); ?></h3>
                        <p class="text-sm text-secondary-500 mt-1"><?php echo e(__('ui.notification_empty_desc')); ?></p>
                    </div>
                <?php endif; ?>

                <div class="mt-6">
                    <?php echo e($notifications->links()); ?>

                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/notifications/index.blade.php ENDPATH**/ ?>