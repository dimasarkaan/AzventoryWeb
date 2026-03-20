{{-- resources/views/dashboard/_activity_modal.blade.php --}}
<div x-show="showActivityModal" 
     role="dialog"
     aria-modal="true"
     aria-labelledby="activity-modal-title"
     class="fixed inset-0 z-[100] overflow-y-auto"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-secondary-900/60 backdrop-blur-sm" @click="showActivityModal = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block relative overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-secondary-100 flex items-center justify-between bg-secondary-50/50">
                <h3 id="activity-modal-title" class="text-base font-bold text-secondary-900">Detail Aktivitas</h3>
                <button @click="showActivityModal = false" 
                        aria-label="Tutup Modal"
                        class="text-secondary-400 hover:text-secondary-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-6" x-show="selectedActivity">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0 ring-4 ring-primary-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-lg font-semibold text-secondary-900 break-words leading-tight" x-text="selectedActivity?.description"></p>
                        <p class="text-sm text-secondary-500 mt-1" x-text="selectedActivity?.created_at_diff"></p>
                    </div>
                </div>

                <div class="bg-secondary-50 rounded-xl p-4 border border-secondary-100 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-secondary-500 font-medium">Pengguna</span>
                        <span class="text-secondary-900 font-bold" x-text="selectedActivity?.user_name || selectedActivity?.user?.name || 'Sistem'"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-secondary-500 font-medium">Role</span>
                        <span class="px-2 py-0.5 rounded-full bg-white border border-secondary-200 text-[10px] font-bold uppercase tracking-wider text-secondary-600" 
                              x-text="selectedActivity?.user?.role || '-'"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-secondary-500 font-medium">Waktu Presisi</span>
                        <span class="text-secondary-700 font-mono text-xs" x-text="selectedActivity?.created_at ? new Date(selectedActivity.created_at).toLocaleString('id-ID') : '-'"></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
