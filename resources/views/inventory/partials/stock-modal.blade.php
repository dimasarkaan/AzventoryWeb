                            <!-- Stock Change Modal -->
                            <template x-teleport="body">
                            <div x-show="stockModalOpen" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="stockModalOpen" @click="stockModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="stockModalOpen" 
                                         x-data="{ 
                                            type: 'masuk', 
                                            quantity: '', 
                                            reason: '', 
                                            isSubmitting: false,
                                            get currentStock() { return liveStock; },
                                            get isValid() {
                                                return this.quantity > 0 && 
                                                       this.reason.trim() !== '' && 
                                                       (this.type === 'masuk' || (this.type === 'keluar' && this.quantity <= this.currentStock));
                                            },
                                            get isStockError() {
                                                return this.type === 'keluar' && this.quantity > this.currentStock;
                                            }
                                         }"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <form action="{{ route('inventory.stock.request.store', $sparepart) }}" method="POST" @submit="isSubmitting = true" novalidate :class="{'opacity-70 pointer-events-none': isSubmitting}">
                                            @csrf
                                            <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 mx-0">
                                                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    </div>
                                                    <div class="ml-4 text-left">
                                                        <h2 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            {{ __('ui.stock_change') }}
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-t border-gray-100">
                                                
                                                <!-- Real-time Warning Banner -->
                                                <div x-show="liveUpdateShow" x-transition class="mb-4 bg-warning-50 border-l-4 border-warning-400 p-3 rounded shadow-sm flex items-start">
                                                    <svg class="w-5 h-5 text-warning-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span class="text-warning-800 text-sm font-medium" x-text="liveUpdateMessage"></span>
                                                </div>

                                                <div class="space-y-4">
                                                            <div>
                                                                <span class="block text-sm font-medium text-gray-700">{{ __('ui.change_type') }} <span class="text-danger-500">*</span></span>
                                                                <div class="mt-2 grid grid-cols-2 gap-3">
                                                                    <label for="type_masuk" class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors">
                                                                        <input type="radio" name="type" id="type_masuk" value="masuk" x-model="type" class="peer sr-only">
                                                                        <div class="w-full text-center peer-checked:text-primary-600 font-medium text-gray-500">
                                                                            <span class="block text-sm">{{ __('ui.stock_in') }}</span>
                                                                        </div>
                                                                        <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-primary-500 pointer-events-none transition-all"></div>
                                                                    </label>
                                                                    <label for="type_keluar" class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors">
                                                                        <input type="radio" name="type" id="type_keluar" value="keluar" x-model="type" class="peer sr-only">
                                                                         <div class="w-full text-center peer-checked:text-danger-600 font-medium text-gray-500">
                                                                            <span class="block text-sm">{{ __('ui.stock_out') }}</span>
                                                                        </div>
                                                                         <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-danger-500 pointer-events-none transition-all"></div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label for="quantity" class="block text-sm font-medium text-gray-700">{{ __('ui.quantity') }} <span class="text-danger-500">*</span></label>
                                                                <div class="relative mt-1 rounded-md shadow-sm">
                                                                    <input type="number" name="quantity" id="quantity" x-model="quantity" min="1" class="form-input block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm transition-colors" :class="{'pr-16': type === 'keluar'}" @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" placeholder="Contoh: 10" required>
                                                                    <div class="absolute inset-y-0 right-0 flex items-center" x-show="type === 'keluar'" x-cloak>
                                                                        <button type="button" @click="quantity = currentStock" class="h-full px-3 text-xs font-bold text-danger-600 hover:text-danger-800 hover:bg-danger-50 border-l border-gray-300 rounded-r-md transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-danger-500" tabindex="-1">
                                                                            MAX
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <p x-show="isStockError" x-transition class="text-danger-500 text-xs mt-1 font-medium flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    {{ __('ui.quantity_exceeds_stock') }} (<span x-text="currentStock"></span>).
                                                                </p>
                                                                <p x-show="!isStockError" class="text-secondary-400 text-xs mt-1">
                                                                    {{ __('ui.stock_available') }}: <span x-text="currentStock"></span> {{ $sparepart->unit ?? 'Pcs' }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <label for="reason" class="block text-sm font-medium text-gray-700">{{ __('ui.reason') }} <span class="text-danger-500">*</span></label>
                                                                <div class="mt-1">
                                                                    <textarea name="reason" id="reason" x-model="reason" rows="3" class="form-textarea block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: Barang rusak, Stok opname, Pembelian baru..." required></textarea>
                                                                </div>
                                                            </div>
                                            </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" 
                                                        data-testid="modal-submit-stock"
                                                        :disabled="!isValid || isSubmitting"
                                                        :class="{ 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400': (!isValid || isSubmitting), 'bg-primary-600 hover:bg-primary-700': isValid && !isSubmitting }"
                                                        class="w-full inline-flex justify-center items-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                                    <span x-show="!isSubmitting">{{ __('ui.save') }}</span>
                                                    <span x-show="isSubmitting" class="flex items-center gap-2" x-cloak>
                                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        Menyimpan...
                                                    </span>
                                                </button>
                                                <button type="button" @click="stockModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    {{ __('ui.cancel') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            </template>
