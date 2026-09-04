@if(session('show_success_modal'))
    <div x-data="{ show: true }" 
         x-show="show" 
         @keydown.window.escape="show = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         x-cloak>
        
        <!-- Backdrop -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal Panel -->
                <div x-show="show" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-2xl bg-white px-4 pb-4 pt-5 shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:p-6 text-center border border-secondary-200"
                     @click.away="show = false">
                    
                    <!-- Close Button -->
                    <div class="absolute right-0 top-0 pr-4 pt-4">
                        <button type="button" @click="show = false" class="rounded-md bg-white text-secondary-400 hover:text-secondary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Animated Check Icon -->
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-success-100 mb-5 relative">
                        <div class="absolute inset-0 rounded-full bg-success-200 animate-ping opacity-75"></div>
                        <svg class="h-10 w-10 text-success-600 relative z-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-bold text-secondary-900 mb-2" id="modal-title">
                            Penyimpanan Sukses!
                        </h3>
                        <p class="text-sm text-secondary-500 mb-6 leading-relaxed">
                            Data inventaris <span class="font-bold text-secondary-800">"{{ session('item_name', 'Barang') }}"</span> berhasil ditambahkan ke dalam sistem. Apa yang ingin Anda lakukan selanjutnya?
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-5 sm:mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary w-full justify-center py-3">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Daftar
                        </a>
                        <button type="button" @click="show = false" class="btn btn-primary w-full justify-center py-3 group">
                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Barang Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
