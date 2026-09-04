            <!-- Evidence Modal -->
    <template x-teleport="body">
        <div x-show="evidenceModalOpen" 
             x-cloak
             class="fixed inset-0 z-[99]" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6">
                <!-- Backdrop -->
                <div x-show="evidenceModalOpen"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="evidenceModalOpen = false"
                     aria-hidden="true"></div>
        
                <!-- Modal Panel -->
                <div x-show="evidenceModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full flex flex-col max-h-[85vh]">
                    
                    <!-- Header -->
                    <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ __('ui.return_evidence') }}
                        </h3>
                        <button @click="evidenceModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        <!-- Image -->
                        <div class="aspect-video w-full rounded-lg overflow-hidden bg-gray-100 border border-gray-200 mb-4 relative group">
                            <template x-if="activeEvidence.image">
                                <img :src="activeEvidence.image" class="w-full h-full object-contain">
                            </template>
                            <template x-if="!activeEvidence.image">
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="text-sm">No Image</span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Details -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-secondary-500 uppercase font-bold tracking-wider">Tanggal Dikembalikan</span>
                                <p class="text-secondary-900 font-medium" x-text="activeEvidence.date || '-'"></p>
                            </div>
                            
                            <div>
                                <span class="text-xs text-secondary-500 uppercase font-bold tracking-wider">Kondisi Barang</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                          :class="getBadgeColor(activeEvidence.condition)">
                                        <span x-text="activeEvidence.condition === 'good' ? 'Baik' : (activeEvidence.condition === 'bad' ? 'Rusak' : 'Hilang')"></span>
                                    </span>
                                </p>
                            </div>

                            <div x-show="activeEvidence.notes && activeEvidence.notes !== '-'">
                                <span class="text-xs text-secondary-500 uppercase font-bold tracking-wider">Catatan</span>
                                <div class="bg-secondary-50 rounded p-3 mt-1 text-sm text-secondary-700 italic border border-secondary-100">
                                    <span x-text="activeEvidence.notes"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse border-t border-gray-200">
                        <button type="button" @click="evidenceModalOpen = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
