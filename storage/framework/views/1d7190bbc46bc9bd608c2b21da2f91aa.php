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
            <!-- Flash Messages -->

            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        <?php echo e(__('ui.inventory_management')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-secondary-500"><?php echo e(__('ui.inventory_management_desc')); ?></p>
                </div>
                <div class="flex items-center gap-2">
                     <!-- Legend Popover -->
                    <div x-data="{ showLegend: false }" class="relative z-30">
                        <button @click="showLegend = !showLegend" class="btn btn-secondary flex items-center justify-center p-2.5" title="<?php echo e(__('ui.legend_title')); ?>">
                            <?php if (isset($component)) { $__componentOriginal00f7be49f205890a8649dcc0b84d98f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal00f7be49f205890a8649dcc0b84d98f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.info','data' => ['class' => 'w-5 h-5 text-secondary-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-secondary-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal00f7be49f205890a8649dcc0b84d98f0)): ?>
<?php $attributes = $__attributesOriginal00f7be49f205890a8649dcc0b84d98f0; ?>
<?php unset($__attributesOriginal00f7be49f205890a8649dcc0b84d98f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal00f7be49f205890a8649dcc0b84d98f0)): ?>
<?php $component = $__componentOriginal00f7be49f205890a8649dcc0b84d98f0; ?>
<?php unset($__componentOriginal00f7be49f205890a8649dcc0b84d98f0); ?>
<?php endif; ?>
                        </button>

                        <div x-show="showLegend" 
                             @click.away="showLegend = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-secondary-200 p-4 z-50 text-left"
                             style="display: none;">
                            <div class="flex items-center justify-between mb-3 border-b border-secondary-100 pb-2">
                                <h3 class="font-bold text-sm text-secondary-900"><?php echo e(__('ui.legend_title')); ?></h3>
                                <button @click="showLegend = false" class="text-secondary-400 hover:text-secondary-600">
                                    <?php if (isset($component)) { $__componentOriginalef2fdc0184b79387088ad139caabd0f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef2fdc0184b79387088ad139caabd0f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.close','data' => ['class' => 'w-4 h-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.close'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalef2fdc0184b79387088ad139caabd0f5)): ?>
<?php $attributes = $__attributesOriginalef2fdc0184b79387088ad139caabd0f5; ?>
<?php unset($__attributesOriginalef2fdc0184b79387088ad139caabd0f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalef2fdc0184b79387088ad139caabd0f5)): ?>
<?php $component = $__componentOriginalef2fdc0184b79387088ad139caabd0f5; ?>
<?php unset($__componentOriginalef2fdc0184b79387088ad139caabd0f5); ?>
<?php endif; ?>
                                </button>
                            </div>
                            
                            <!-- Tipe Barang -->
                            <div class="mb-4">
                                <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider block mb-2"><?php echo e(__('ui.legend_type')); ?></span>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1 h-6 rounded-full bg-blue-600"></div>
                                        <span class="text-xs text-secondary-700 font-medium"><?php echo e(__('ui.legend_asset')); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-1 h-6 rounded-full bg-green-600"></div>
                                        <span class="text-xs text-secondary-700 font-medium"><?php echo e(__('ui.legend_sale')); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Dot -->
                            <div>
                                <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider block mb-2"><?php echo e(__('ui.legend_status')); ?></span>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-success-500 border border-white ring-1 ring-secondary-100"></div>
                                        <span class="text-xs text-secondary-700 font-medium"><?php echo e(__('ui.legend_active')); ?></span>
                                    </div>
                                     <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-danger-500 border border-white ring-1 ring-secondary-100"></div>
                                        <span class="text-xs text-secondary-700 font-medium"><?php echo e(__('ui.legend_damaged')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <!-- Trash Toggle Button -->
                     <a href="<?php echo e(request('trash') ? route('inventory.index') : route('inventory.index', ['trash' => 'true'])); ?>" 
                        class="btn flex items-center justify-center p-2.5 <?php echo e(request('trash') ? 'btn-danger' : 'btn-secondary'); ?>" 
                        title="<?php echo e(request('trash') ? __('ui.exit_trash') : __('ui.view_trash')); ?>">
                        <?php if(request('trash')): ?>
                            <!-- Icon: Arrow Left / Back -->
                            <?php if (isset($component)) { $__componentOriginal7b0e623fee9a946cdd61cb6cb2889c0b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b0e623fee9a946cdd61cb6cb2889c0b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.back','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.back'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b0e623fee9a946cdd61cb6cb2889c0b)): ?>
<?php $attributes = $__attributesOriginal7b0e623fee9a946cdd61cb6cb2889c0b; ?>
<?php unset($__attributesOriginal7b0e623fee9a946cdd61cb6cb2889c0b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b0e623fee9a946cdd61cb6cb2889c0b)): ?>
<?php $component = $__componentOriginal7b0e623fee9a946cdd61cb6cb2889c0b; ?>
<?php unset($__componentOriginal7b0e623fee9a946cdd61cb6cb2889c0b); ?>
<?php endif; ?>
                        <?php else: ?>
                            <!-- Icon: Trash -->
                            <?php if (isset($component)) { $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.trash','data' => ['class' => 'w-5 h-5 text-secondary-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-secondary-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8)): ?>
<?php $attributes = $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8; ?>
<?php unset($__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8)): ?>
<?php $component = $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8; ?>
<?php unset($__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </a>
                    
                    <?php if(!request('trash')): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Sparepart::class)): ?>
                    <a href="<?php echo e(route('inventory.create')); ?>" class="btn btn-primary flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal6315a526d124ee5b3ba861082d11f72e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6315a526d124ee5b3ba861082d11f72e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.plus','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.plus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6315a526d124ee5b3ba861082d11f72e)): ?>
<?php $attributes = $__attributesOriginal6315a526d124ee5b3ba861082d11f72e; ?>
<?php unset($__attributesOriginal6315a526d124ee5b3ba861082d11f72e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6315a526d124ee5b3ba861082d11f72e)): ?>
<?php $component = $__componentOriginal6315a526d124ee5b3ba861082d11f72e; ?>
<?php unset($__componentOriginal6315a526d124ee5b3ba861082d11f72e); ?>
<?php endif; ?>
                        <?php echo e(__('ui.add_inventory')); ?>

                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(request('trash')): ?>
                    <!-- Trash Mode Indicator & Bulk Actions -->
                    <div class="mb-4 relative">
                        <div class="rounded-lg bg-danger-50 p-4 border border-danger-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                             <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <?php if (isset($component)) { $__componentOriginald962e0f6ed6702a76cb54d60420ad1a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald962e0f6ed6702a76cb54d60420ad1a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.warning','data' => ['class' => 'h-5 w-5 text-danger-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.warning'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 text-danger-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald962e0f6ed6702a76cb54d60420ad1a1)): ?>
<?php $attributes = $__attributesOriginald962e0f6ed6702a76cb54d60420ad1a1; ?>
<?php unset($__attributesOriginald962e0f6ed6702a76cb54d60420ad1a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald962e0f6ed6702a76cb54d60420ad1a1)): ?>
<?php $component = $__componentOriginald962e0f6ed6702a76cb54d60420ad1a1; ?>
<?php unset($__componentOriginald962e0f6ed6702a76cb54d60420ad1a1); ?>
<?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-danger-800"><?php echo e(__('ui.trash_mode')); ?></h3>
                                    <div class="text-sm text-danger-700 mt-1">
                                        <?php echo e(__('ui.trash_mode_desc')); ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Bulk Action Bar -->
                        <!-- Floating Bulk Action Bar (Styled like Users) -->
                        <div id="bulk-action-bar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-xl border border-secondary-200 px-6 py-3 flex items-center gap-6 z-50 transition-all duration-300 translate-y-24 opacity-0">
                            <div class="flex items-center gap-2 border-r border-secondary-200 pr-6">
                                <span class="font-bold text-lg text-primary-600" id="selected-count">0</span>
                                <span class="text-sm text-secondary-500 font-medium"><?php echo e(__('ui.selected')); ?></span>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <form id="bulk-restore-form" action="<?php echo e(route('inventory.bulk-restore')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div id="bulk-restore-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkRestore()" class="btn btn-white text-secondary-700 hover:text-primary-600 flex items-center gap-2 border-0 bg-transparent hover:bg-secondary-50">
                                        <?php if (isset($component)) { $__componentOriginald4bd00a03a971114f20525c4c2f7903f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4bd00a03a971114f20525c4c2f7903f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.restore','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.restore'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4bd00a03a971114f20525c4c2f7903f)): ?>
<?php $attributes = $__attributesOriginald4bd00a03a971114f20525c4c2f7903f; ?>
<?php unset($__attributesOriginald4bd00a03a971114f20525c4c2f7903f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4bd00a03a971114f20525c4c2f7903f)): ?>
<?php $component = $__componentOriginald4bd00a03a971114f20525c4c2f7903f; ?>
<?php unset($__componentOriginald4bd00a03a971114f20525c4c2f7903f); ?>
<?php endif; ?>
                                        <span class="font-medium"><?php echo e(__('ui.restore')); ?></span>
                                    </button>
                                </form>

                                <form id="bulk-delete-form" action="<?php echo e(route('inventory.bulk-force-delete')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <div id="bulk-delete-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkDelete()" class="btn btn-danger flex items-center gap-2 px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition-all">
                                        <?php if (isset($component)) { $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.trash','data' => ['class' => 'w-4 h-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8)): ?>
<?php $attributes = $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8; ?>
<?php unset($__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8)): ?>
<?php $component = $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8; ?>
<?php unset($__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8); ?>
<?php endif; ?>
                                        <span><?php echo e(__('ui.force_delete')); ?></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            <?php endif; ?>

            <!-- Filters & Search -->
            <div class="mb-4 card p-4 overflow-visible" x-data="{ showFilters: false }">
                    <form id="inventory-filter-form" method="GET" action="<?php echo e(route('inventory.index')); ?>">
                    <input type="hidden" name="trash" value="<?php echo e(request('trash')); ?>">
                    <!-- Top: Search Bar & Filter Toggle -->
                    <div class="mb-4 flex gap-2">
                        <div class="relative w-full">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <?php if (isset($component)) { $__componentOriginal60b104b2fde947186a9c15caab3ac427 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60b104b2fde947186a9c15caab3ac427 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.search','data' => ['class' => 'w-5 h-5 text-secondary-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-secondary-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal60b104b2fde947186a9c15caab3ac427)): ?>
<?php $attributes = $__attributesOriginal60b104b2fde947186a9c15caab3ac427; ?>
<?php unset($__attributesOriginal60b104b2fde947186a9c15caab3ac427); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal60b104b2fde947186a9c15caab3ac427)): ?>
<?php $component = $__componentOriginal60b104b2fde947186a9c15caab3ac427; ?>
<?php unset($__componentOriginal60b104b2fde947186a9c15caab3ac427); ?>
<?php endif; ?>
                            </div>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input-field pl-10 w-full" placeholder="<?php echo e(__('ui.search_inventory_placeholder')); ?>" onchange="this.form.submit()">
                        </div>
                        <button type="button" @click="showFilters = !showFilters" class="btn btn-secondary md:hidden flex items-center justify-center w-12 flex-shrink-0" title="<?php echo e(__('ui.show_filter')); ?>">
                            <?php if (isset($component)) { $__componentOriginal9a7aeb2c7e031a07e02f7fd0ddb74958 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a7aeb2c7e031a07e02f7fd0ddb74958 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.filter','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a7aeb2c7e031a07e02f7fd0ddb74958)): ?>
<?php $attributes = $__attributesOriginal9a7aeb2c7e031a07e02f7fd0ddb74958; ?>
<?php unset($__attributesOriginal9a7aeb2c7e031a07e02f7fd0ddb74958); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a7aeb2c7e031a07e02f7fd0ddb74958)): ?>
<?php $component = $__componentOriginal9a7aeb2c7e031a07e02f7fd0ddb74958; ?>
<?php unset($__componentOriginal9a7aeb2c7e031a07e02f7fd0ddb74958); ?>
<?php endif; ?>
                        </button>
                    </div>

                    <!-- Bottom: Filters & Sort -->
                    <div class="flex-col md:flex-row flex-wrap gap-3" :class="showFilters ? 'flex' : 'hidden md:flex'">
                        <?php
                            $categoryOptions = $categories->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $brandOptions = $brands->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $locationOptions = $locations->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $colorOptions = $colors->mapWithKeys(fn($item) => [$item => $item])->toArray();
                        ?>

                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <?php
                                $typeOptions = [
                                    'sale' => 'Barang Dijual (Sale)',
                                    'asset' => 'Inventaris (Asset)',
                                ];
                            ?>
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'type','options' => $typeOptions,'selected' => request('type'),'placeholder' => ''.e(__('ui.all_types')).'','submitOnChange' => true,'width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typeOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('type')),'placeholder' => ''.e(__('ui.all_types')).'','submitOnChange' => true,'width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'category','options' => $categoryOptions,'selected' => request('category'),'placeholder' => ''.e(__('ui.all_categories')).'','submitOnChange' => true,'width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'category','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('category')),'placeholder' => ''.e(__('ui.all_categories')).'','submitOnChange' => true,'width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'brand','options' => $brandOptions,'selected' => request('brand'),'placeholder' => ''.e(__('ui.all_brands')).'','submitOnChange' => true,'width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'brand','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('brand')),'placeholder' => ''.e(__('ui.all_brands')).'','submitOnChange' => true,'width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'location','options' => $locationOptions,'selected' => request('location'),'placeholder' => ''.e(__('ui.all_locations')).'','submitOnChange' => true,'width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'location','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($locationOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('location')),'placeholder' => ''.e(__('ui.all_locations')).'','submitOnChange' => true,'width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'color','options' => $colorOptions,'selected' => request('color'),'placeholder' => ''.e(__('ui.all_colors')).'','submitOnChange' => true,'width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'color','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($colorOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('color')),'placeholder' => ''.e(__('ui.all_colors')).'','submitOnChange' => true,'width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                        </div>
                        <?php
                            $sortOptions = [
                                'newest' => __('ui.sort_newest'),
                                'oldest' => __('ui.sort_oldest'),
                                'name_asc' => __('ui.sort_name_asc'),
                                'name_desc' => __('ui.sort_name_desc'),
                                'stock_asc' => __('ui.sort_stock_asc'),
                                'stock_desc' => __('ui.sort_stock_desc'),
                                'price_asc' => __('ui.sort_price_asc'),
                                'price_desc' => __('ui.sort_price_desc'),
                            ];
                        ?>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'sort','options' => $sortOptions,'selected' => request('sort', 'newest'),'placeholder' => ''.e(__('ui.sort')).'','submitOnChange' => true,'width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sort','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sortOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('sort', 'newest')),'placeholder' => ''.e(__('ui.sort')).'','submitOnChange' => true,'width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                        </div>
                        
                        <a href="<?php echo e(route('inventory.index')); ?>" id="reset-filters" class="btn btn-secondary flex items-center justify-center gap-2" title="<?php echo e(__('ui.reset_filter')); ?>">
                            <?php if (isset($component)) { $__componentOriginald4bd00a03a971114f20525c4c2f7903f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4bd00a03a971114f20525c4c2f7903f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.restore','data' => ['class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.restore'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4bd00a03a971114f20525c4c2f7903f)): ?>
<?php $attributes = $__attributesOriginald4bd00a03a971114f20525c4c2f7903f; ?>
<?php unset($__attributesOriginald4bd00a03a971114f20525c4c2f7903f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4bd00a03a971114f20525c4c2f7903f)): ?>
<?php $component = $__componentOriginald4bd00a03a971114f20525c4c2f7903f; ?>
<?php unset($__componentOriginald4bd00a03a971114f20525c4c2f7903f); ?>
<?php endif; ?>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            <?php echo $__env->make('inventory.partials.mobile-list', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Desktop Table View -->
            <?php echo $__env->make('inventory.partials.desktop-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/pages/superadmin/inventory/index.js'); ?>
    <?php $__env->stopPush(); ?>

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
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/inventory/index.blade.php ENDPATH**/ ?>