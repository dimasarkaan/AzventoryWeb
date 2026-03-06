<div x-show="showLocationModal" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-secondary-900/60 backdrop-blur-sm" @click="showLocationModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-secondary-100"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="px-6 py-4 bg-white border-b border-secondary-100 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary-50 rounded-xl text-primary-600">
                        <?php if (isset($component)) { $__componentOriginalb6952584d5242e7a54c1423e310b3baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb6952584d5242e7a54c1423e310b3baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.location','data' => ['class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.location'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb6952584d5242e7a54c1423e310b3baa)): ?>
<?php $attributes = $__attributesOriginalb6952584d5242e7a54c1423e310b3baa; ?>
<?php unset($__attributesOriginalb6952584d5242e7a54c1423e310b3baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb6952584d5242e7a54c1423e310b3baa)): ?>
<?php $component = $__componentOriginalb6952584d5242e7a54c1423e310b3baa; ?>
<?php unset($__componentOriginalb6952584d5242e7a54c1423e310b3baa); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-secondary-900">Manajemen Lokasi</h3>
                        <p class="text-xs text-secondary-500">Kelola master data lokasi penyimpanan barang</p>
                    </div>
                </div>
                <button @click="showLocationModal = false" class="p-2 text-secondary-400 hover:text-danger-600 hover:bg-danger-50 rounded-xl transition-colors">
                    <?php if (isset($component)) { $__componentOriginalef2fdc0184b79387088ad139caabd0f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef2fdc0184b79387088ad139caabd0f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.close','data' => ['class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.close'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
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

            <!-- Body -->
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto bg-secondary-50/30">
                
                <div class="mb-6 bg-white p-4 rounded-2xl border border-primary-100 shadow-sm">
                    <h4 class="text-xs font-bold text-secondary-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal6315a526d124ee5b3ba861082d11f72e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6315a526d124ee5b3ba861082d11f72e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.plus','data' => ['class' => 'w-3 h-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.plus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3 h-3']); ?>
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
                        Tambah Lokasi Baru
                    </h4>
                    <div class="flex gap-2">
                        <div class="relative flex-grow">
                            <input type="text" 
                                   x-model="newLocationName"
                                   @keydown.enter="addLocation()"
                                   placeholder="Ketik Cabang Baru Di Sini" 
                                   class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm font-bold text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none">
                        </div>
                        <button @click="addLocation()" 
                                :disabled="isAddingLocation || !newLocationName.trim()"
                                class="btn btn-primary px-5 rounded-xl flex items-center gap-2 shadow-lg shadow-primary-200/50 disabled:opacity-50 disabled:shadow-none transition-all">
                            <template x-if="isAddingLocation">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span class="font-bold text-sm" x-text="isAddingLocation ? 'Menyimpan...' : 'Tambah'"></span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-3 px-1">
                    <h4 class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Daftar Lokasi</h4>
                    <span class="text-[10px] text-secondary-400 font-medium px-2 py-0.5 bg-secondary-100 rounded-full" x-text="locationsList.length + ' Lokasi'"></span>
                </div>

                <div x-show="isLoadingLocations" class="flex flex-col items-center justify-center py-12 gap-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    <p class="text-sm text-secondary-500">Memuat data lokasi...</p>
                </div>

                <div x-show="!isLoadingLocations" class="relative">
                    <div class="grid gap-3">
                        <template x-for="loc in locationsList" :key="loc.id">
                            <div class="bg-white p-4 rounded-2xl border border-secondary-100 shadow-sm hover:shadow-md transition-all group flex items-center justify-between gap-4"
                                 :class="{'ring-2 ring-primary-500 border-transparent bg-primary-50/10': editingId === loc.id}">
                                
                                <div class="flex-grow min-w-0">
                                    
                                    <div x-show="editingId !== loc.id">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-secondary-900 truncate" x-text="loc.name"></h4>
                                            <template x-if="loc.is_default">
                                                <span class="px-2 py-0.5 text-[10px] bg-primary-50 text-primary-600 font-bold rounded-full border border-primary-100 uppercase tracking-wider">Default</span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-secondary-500 flex items-center gap-1">
                                            <span class="font-bold text-secondary-900" x-text="loc.items_count"></span> Barang di lokasi ini
                                        </p>
                                    </div>

                                    
                                    <div x-show="editingId === loc.id" x-cloak>
                                        <input type="text" 
                                               :id="'edit-input-' + loc.id"
                                               x-model="editingName" 
                                               @keydown.enter="saveEdit(loc.id)"
                                               @keydown.escape="cancelEdit()"
                                               class="w-full bg-white border border-primary-300 rounded-xl px-3 py-2 text-sm font-bold text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none"
                                               placeholder="Nama lokasi...">
                                        <p class="text-[10px] text-secondary-400 mt-1 ml-1 font-medium italic">Tekan Enter untuk simpan, Esc untuk batal</p>
                                    </div>
                                </div>

                                
                                <div x-show="editingId !== loc.id" class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button @click="startEdit(loc)"
                                            class="p-2 text-secondary-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all"
                                            title="Ubah Nama">
                                        <?php if (isset($component)) { $__componentOriginal3548e6b20d063684038f9cfd02dbf314 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3548e6b20d063684038f9cfd02dbf314 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.edit','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.edit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3548e6b20d063684038f9cfd02dbf314)): ?>
<?php $attributes = $__attributesOriginal3548e6b20d063684038f9cfd02dbf314; ?>
<?php unset($__attributesOriginal3548e6b20d063684038f9cfd02dbf314); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3548e6b20d063684038f9cfd02dbf314)): ?>
<?php $component = $__componentOriginal3548e6b20d063684038f9cfd02dbf314; ?>
<?php unset($__componentOriginal3548e6b20d063684038f9cfd02dbf314); ?>
<?php endif; ?>
                                    </button>

                                    <button @click="askDelete(loc)"
                                            class="p-2 text-secondary-400 hover:text-danger-600 hover:bg-danger-50 rounded-xl transition-all"
                                            title="Hapus Lokasi">
                                        <?php if (isset($component)) { $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.trash','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
                                    </button>
                                </div>

                                
                                <div x-show="editingId === loc.id" x-cloak class="flex items-center gap-2">
                                    <button @click="saveEdit(loc.id)"
                                            class="p-2 bg-success-500 text-white hover:bg-success-600 rounded-xl shadow-sm hover:shadow-md transition-all"
                                            title="Simpan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </button>

                                    <button @click="cancelEdit()"
                                            class="p-2 bg-secondary-100 text-secondary-600 hover:bg-secondary-200 rounded-xl transition-all"
                                            title="Batal">
                                        <?php if (isset($component)) { $__componentOriginalef2fdc0184b79387088ad139caabd0f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef2fdc0184b79387088ad139caabd0f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.close','data' => ['class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.close'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
                            </div>
                        </template>
                    </div>

                    
                    <div x-show="confirmDeleteId" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-x-0 -inset-y-4 bg-white/95 backdrop-blur-[2px] z-[20] flex items-center justify-center p-6 text-center"
                         x-cloak>
                        <div class="w-full max-w-sm">
                            <div class="w-16 h-16 bg-danger-50 text-danger-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-danger-100">
                                <?php if (isset($component)) { $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.trash','data' => ['class' => 'w-8 h-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8']); ?>
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
                            </div>
                            <h4 class="text-secondary-900 font-bold text-lg mb-1">Hapus Lokasi?</h4>
                            <p class="text-sm text-secondary-500 mb-4">
                                Anda yakin ingin menghapus lokasi <span class="font-bold text-secondary-900" x-text="confirmDeleteName"></span>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                            
                            <div x-show="deleteLocationError" x-cloak
                                 class="mb-4 flex items-start gap-2 bg-danger-50 border border-danger-200 text-danger-700 rounded-xl px-4 py-3 text-sm font-medium text-left">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="deleteLocationError"></span>
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <button @click="cancelDelete()" 
                                        :disabled="isDeleting"
                                        class="btn btn-secondary px-6 py-2.5 rounded-2xl font-bold disabled:opacity-50 transition-all">Batal</button>
                                <button @click="deleteLocation(confirmDeleteId)" 
                                        :disabled="isDeleting"
                                        class="btn btn-danger px-6 py-2.5 rounded-2xl font-bold shadow-lg shadow-danger-200 disabled:opacity-50 transition-all flex items-center gap-2">
                                    <template x-if="isDeleting">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="locationsList.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-4 text-secondary-400">
                            <?php if (isset($component)) { $__componentOriginalb6952584d5242e7a54c1423e310b3baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb6952584d5242e7a54c1423e310b3baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.location','data' => ['class' => 'w-8 h-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.location'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb6952584d5242e7a54c1423e310b3baa)): ?>
<?php $attributes = $__attributesOriginalb6952584d5242e7a54c1423e310b3baa; ?>
<?php unset($__attributesOriginalb6952584d5242e7a54c1423e310b3baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb6952584d5242e7a54c1423e310b3baa)): ?>
<?php $component = $__componentOriginalb6952584d5242e7a54c1423e310b3baa; ?>
<?php unset($__componentOriginalb6952584d5242e7a54c1423e310b3baa); ?>
<?php endif; ?>
                        </div>
                        <h4 class="text-secondary-900 font-bold">Belum Ada Lokasi</h4>
                        <p class="text-sm text-secondary-500">Lokasi akan otomatis bertambah saat Anda menyimpan barang baru.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/dashboard/_location_modal.blade.php ENDPATH**/ ?>