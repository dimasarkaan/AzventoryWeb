<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
             x-data="inventoryDetail()"
             x-init="console.log('Alpine Scope Initialized')"
             @open-return-modal.window="initReturn($event.detail)"
        >
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                            {{ $sparepart->name }}
                        </h2>
                        @if($sparepart->status === 'aktif')
                            <span class="badge badge-success">{{ __('ui.status_active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('ui.status_inactive') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-1.5 text-secondary-500 font-mono text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                        {{ $sparepart->part_number }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ __('ui.back') }}
                    </a>
                    @can('update', $sparepart)
                    <a href="{{ route('inventory.edit', $sparepart) }}" class="btn btn-warning">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        {{ __('ui.edit') }}
                    </a>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (Visual & Main Info) -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <!-- Image Card -->
                    <div class="card overflow-hidden">
                        <div class="aspect-video w-full bg-secondary-100 flex items-center justify-center relative">
                            @if($sparepart->image)
                                <img src="{{ asset('storage/' . $sparepart->image) }}" alt="{{ $sparepart->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-secondary-400 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-sm">{{ __('ui.no_image') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Details Card -->
                    <div class="card p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">{{ __('ui.detail_info') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.category') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium flex items-center gap-2">
                                    <span class="p-1.5 bg-primary-50 text-primary-600 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                    </span>
                                    {{ $sparepart->category }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.brand') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->brand ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.color') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->color ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.storage_location') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium flex items-center gap-2">
                                     <span class="p-1.5 bg-warning-50 text-warning-600 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </span>
                                    {{ $sparepart->location }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.age') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->age ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.condition') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->condition }}
                                </div>
                            </div>
                            @if($sparepart->type === 'sale')
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.unit_price') }}</span>
                                <div class="mt-1 text-secondary-900 font-bold text-lg">
                                    Rp {{ number_format($sparepart->price, 0, ',', '.') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column (Stock & QR) -->
                <div class="flex flex-col gap-6">
                    <!-- Stock Card -->
                    <div class="card p-6 border-t-4 border-primary-500">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.available_stock') }}</span>
                                <div class="mt-1 text-4xl font-extrabold text-secondary-900">
                                    {{ $sparepart->stock }}
                                    <span class="text-base font-medium text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                </div>
                            </div>
                            <div class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        
                        @if($sparepart->stock <= $sparepart->minimum_stock)
                        <div class="mt-4 bg-danger-50 text-danger-700 p-3 rounded-lg text-sm flex items-start gap-2 border border-danger-100">
                             <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                             <div>
                                 <span class="font-bold block">{{ __('ui.low_stock_alert') }}</span>
                                 {{ __('ui.low_stock_desc') }} ({{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }}).
                             </div>
                        </div>
                        @else
                         <div class="mt-4 bg-success-50 text-success-700 p-3 rounded-lg text-sm flex items-center gap-2 border border-success-100">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <span>{{ __('ui.stock_safe') }} (Min: {{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }})</span>
                        </div>
                        @endif

                        <!-- Actions Wrapper -->
                        <div>
                             <div class="mt-4 pt-4 border-t border-secondary-100 grid grid-cols-1 gap-3">
                                @if($sparepart->type === 'asset')
                                    @if($sparepart->condition === 'Baik')
                                        <button @click="borrowModalOpen = true" type="button" class="btn btn-primary w-full justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            {{ __('ui.borrow_item') }}
                                        </button>
                                    @else
                                        <div class="bg-warning-50 text-warning-700 p-3 rounded-lg text-sm flex items-start gap-2 border border-warning-200">
                                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <div>
                                                <span class="font-bold block">{{ __('ui.cannot_borrow') }}</span>
                                                {{ __('ui.cannot_borrow_desc', ['condition' => $sparepart->condition]) }}
                                            </div>
                                        </div>
                                    @endif
                                    <button @click="stockModalOpen = true" type="button" class="btn btn-secondary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        {{ __('ui.update_stock') }}
                                    </button>
                                @else
                                    <button @click="stockModalOpen = true" type="button" class="btn btn-primary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        {{ __('ui.request_stock_change') }}
                                    </button>
                                @endif
                            </div>

                            <!-- Stock Change Modal -->
                            <div x-show="stockModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="stockModalOpen" @click="stockModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="stockModalOpen" 
                                         x-data="{ 
                                            type: 'masuk', 
                                            quantity: '', 
                                            reason: '', 
                                            currentStock: {{ $sparepart->stock }},
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
                                        <form action="{{ route('inventory.stock.request.store', $sparepart) }}" method="POST" novalidate>
                                            @csrf
                                            <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 mx-0">
                                                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    </div>
                                                    <div class="ml-4 text-left">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            {{ __('ui.stock_change') }}
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">{{ __('ui.change_type') }} <span class="text-danger-500">*</span></label>
                                                                <div class="mt-2 grid grid-cols-2 gap-3">
                                                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors">
                                                                        <input type="radio" name="type" value="masuk" x-model="type" class="peer sr-only">
                                                                        <div class="w-full text-center peer-checked:text-primary-600 font-medium text-gray-500">
                                                                            <span class="block text-sm">{{ __('ui.stock_in') }}</span>
                                                                        </div>
                                                                        <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-primary-500 pointer-events-none transition-all"></div>
                                                                    </label>
                                                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors">
                                                                        <input type="radio" name="type" value="keluar" x-model="type" class="peer sr-only">
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
                                                                    <input type="number" name="quantity" id="quantity" x-model="quantity" min="1" class="form-input block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" placeholder="Contoh: 10">
                                                                </div>
                                                                <p x-show="isStockError" x-transition class="text-danger-500 text-xs mt-1 font-medium flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    {{ __('ui.quantity_exceeds_stock') }} ({{ $sparepart->stock }}).
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <label for="reason" class="block text-sm font-medium text-gray-700">{{ __('ui.reason') }} <span class="text-danger-500">*</span></label>
                                                                <div class="mt-1">
                                                                    <textarea name="reason" id="reason" x-model="reason" rows="3" class="form-textarea block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: Barang rusak, Stok opname, Pembelian baru..."></textarea>
                                                                </div>
                                                            </div>
                                            </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" 
                                                        :disabled="!isValid"
                                                        :class="{ 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400': !isValid, 'bg-primary-600 hover:bg-primary-700': isValid }"
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                                    {{ __('ui.save') }}
                                                </button>
                                                <button type="button" @click="stockModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    {{ __('ui.cancel') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Borrow Modal -->
                            <div x-show="borrowModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="borrowModalOpen" @click="borrowModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="borrowModalOpen" 
                                         x-data="{
                                            quantity: '',
                                            dueDate: '',
                                            maxStock: {{ $sparepart->stock }},
                                            get isValid() { return this.quantity > 0 && this.quantity <= this.maxStock && this.dueDate; },
                                            get isQuantityError() { return this.quantity !== '' && this.quantity > this.maxStock; }
                                         }"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <form action="{{ route('inventory.borrow.store', $sparepart) }}" method="POST" novalidate>
                                            @csrf
                                            <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 mx-0">
                                                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                                    </div>
                                                    <div class="ml-4 text-left">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            {{ __('ui.borrow_item') }}
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">{{ __('ui.borrower_name') }}</label>
                                                                <div class="mt-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 sm:text-sm">
                                                                    {{ Auth::user()->name }}
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label for="borrow_quantity" class="block text-sm font-medium text-gray-700">{{ __('ui.quantity') }} <span class="text-danger-500">*</span></label>
                                                                <input type="number" name="quantity" id="borrow_quantity" x-model="quantity" min="1" :max="maxStock" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: 1" required @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()">
                                                                <!-- Custom Error Message -->
                                                                <p x-show="isQuantityError" style="display: none;" class="text-danger-500 text-xs mt-1 font-medium flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                    {{ __('ui.quantity_exceeds_stock') }} ({{ $sparepart->stock }}).
                                                                </p>
                                                                <p x-show="!isQuantityError" class="text-secondary-400 text-xs mt-1">
                                                                    {{ __('ui.stock_available') }}: {{ $sparepart->stock }} {{ $sparepart->unit ?? 'Pcs' }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <label for="expected_return_at" class="block text-sm font-medium text-gray-700">{{ __('ui.expected_return_date') }} <span class="text-danger-500">*</span></label>
                                                                <input type="date" name="expected_return_at" id="expected_return_at" x-model="dueDate" min="{{ date('Y-m-d') }}" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                                            </div>

                                                            <div>
                                                                <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('ui.notes') }} <span class="text-secondary-400 font-normal">(Opsional)</span></label>
                                                                <textarea name="notes" id="notes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Keperluan..."></textarea>
                                                            </div>
                                            </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" 
                                                        :disabled="!isValid"
                                                        :class="{ 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400': !isValid, 'bg-primary-600 hover:bg-primary-700': isValid }"
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                                    {{ __('ui.confirm_borrow') }}
                                                </button>
                                                <button type="button" @click="borrowModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    {{ __('ui.cancel') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- QR Code Card -->
                    <div class="card p-6 flex-1 flex flex-col h-full"> 
                        <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wider flex-none">{{ __('ui.qr_identification') }}</h3>
                        
                        <div class="flex-1 flex flex-col items-center justify-center min-h-[200px]"> <!-- Centered Content area -->
                            @if ($sparepart->qr_code_path)
                                <div class="bg-white p-2 rounded-xl shadow-sm border border-secondary-100 mb-6">
                                     <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code" class="w-56 h-56">
                                </div>
                                <div class="grid grid-cols-2 gap-3 w-full max-w-xs">
                                    <a href="{{ route('inventory.qr.download', $sparepart) }}" class="btn btn-secondary justify-center text-sm py-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        {{ __('ui.download') }}
                                    </a>
                                     <a href="{{ route('inventory.qr.print', $sparepart) }}" target="_blank" class="btn btn-secondary justify-center text-sm py-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        {{ __('ui.print') }}
                                    </a>
                                </div>
                            @else
                                <div class="bg-secondary-50 w-full h-40 rounded-xl flex items-center justify-center text-secondary-400 mb-4">
                                    <span class="text-sm italic">{{ __('ui.no_qr') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-100 text-xs text-secondary-400 space-y-1 text-center flex-none">
                            <p>{{ __('ui.created_at') }}: {{ $sparepart->created_at->isoFormat('D MMMM Y HH:mm') }}</p>
                            <p>{{ __('ui.updated_at') }}: {{ $sparepart->updated_at->isoFormat('D MMMM Y HH:mm') }}</p>
                        </div>
                    </div>

                    <!-- Meta Info -->

                </div>

                <!-- Active Borrowings Section (Only for Assets) -->
                @if($sparepart->type === 'asset')
                <div class="col-span-1 lg:col-span-3">

                        <!-- History Card -->
                        <div class="card p-6">
                            <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">{{ __('ui.borrowing_history') }}</h3>

                            @if($borrowings->count() > 0)
                            <!-- Desktop Table -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-secondary-500 uppercase bg-secondary-50">
                                        <tr>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.borrower') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.quantity') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.borrow_date') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.expected_return_date') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.status') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap text-right">{{ __('ui.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-secondary-100">

                                    @foreach($borrowings as $borrowing)
                                        @php
                                            $user = auth()->user();
                                            $isSuperAdmin = $user->role === \App\Enums\UserRole::SUPERADMIN;
                                            $isAdmin = $user->role === \App\Enums\UserRole::ADMIN;
                                            $borrowerRole = $borrowing->user->role ?? null;
                                            $isOwn = $borrowing->user_id === $user->id;

                                            // Visibility Logic
                                            // Admin can see operators (if borrower is operator)
                                            // SuperAdmin sees all
                                            $canView = $isSuperAdmin
                                                || ($isAdmin && ($isOwn || ($borrowerRole === \App\Enums\UserRole::OPERATOR)))
                                                || $isOwn;
                                        @endphp

                                        @if($canView)
                                        <tr class="hover:bg-primary-50 transition-colors group cursor-pointer" onclick="window.location.href='{{ route('inventory.borrow.show', $borrowing) }}'">
                                            <td class="px-4 py-3 text-secondary-900 font-medium">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-secondary-200 overflow-hidden flex-shrink-0">
                                                        @if($borrowing->user && $borrowing->user->avatar)
                                                            <img src="{{ asset('storage/' . $borrowing->user->avatar) }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center text-secondary-500 text-xs font-bold">
                                                                {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span>{{ $borrowing->user->name ?? 'User Terhapus' }}</span>
                                                        <span class="text-xs text-secondary-500">{{ $borrowing->user->role ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-secondary-600">
                                                <span class="font-bold text-secondary-900 bg-secondary-100 px-2 py-1 rounded-lg text-xs">
                                                    {{ $borrowing->quantity }} {{ $sparepart->unit }}
                                                    @if($borrowing->remaining_quantity < $borrowing->quantity)
                                                        <span class="text-xs text-secondary-500 font-normal block mt-1">Sisa: {{ $borrowing->remaining_quantity }}</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-secondary-600">
                                                {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d F Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 text-secondary-600">
                                                {{ $borrowing->expected_return_at ? \Carbon\Carbon::parse($borrowing->expected_return_at)->translatedFormat('d F Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($borrowing->status === 'borrowed')
                                                    <span class="bg-warning-100 text-warning-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-warning-500"></span>
                                                        {{ __('ui.status_borrowed') }}
                                                    </span>
                                                @elseif($borrowing->status === 'returned')
                                                    <span class="bg-success-100 text-success-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-success-500"></span>
                                                        {{ __('ui.status_returned') }}
                                                    </span>
                                                @else
                                                    <span class="bg-danger-100 text-danger-800 text-xs font-bold px-2 py-1 rounded-lg">
                                                        {{ ucfirst($borrowing->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                @if(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->id() === $borrowing->user_id)
                                                    <!-- Return Button -->
                                                    <button
                                                        type="button"
                                                        @click.stop="$dispatch('open-return-modal', { maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                                                        class="bg-success-50 text-success-600 hover:bg-success-100 hover:text-success-700 font-bold py-1.5 px-3 rounded-lg text-xs transition-colors inline-flex items-center gap-1 group z-10 relative"
                                                    >
                                                        <span>{{ __('ui.return_item') }}</span>
                                                    </button>
                                                @else
                                                    <!-- View Evidence Button -->
                                                    @if($borrowing->return_evidence)

                                                    <button 
                                                        type="button"
                                                        @click.stop="
                                                            activeEvidence = {
                                                                image: '{{ asset('storage/' . $borrowing->return_evidence) }}',
                                                                notes: '{{ addslashes($borrowing->return_notes ?? '-') }}',
                                                                date: '{{ $borrowing->actual_return_date ? \Carbon\Carbon::parse($borrowing->actual_return_date)->translatedFormat('d F Y H:i') : '-' }}',
                                                                condition: '{{ $borrowing->return_condition ?? 'Baik' }}'
                                                            };
                                                            evidenceModalOpen = true;
                                                        "
                                                        class="text-secondary-400 hover:text-primary-600 transition-colors tooltip-trigger z-10 relative"
                                                        title="{{ __('ui.view_return_evidence') }}"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </button>
                                                    @endif
                                                @endif

                                                @if($borrowing->returns->count() > 0 || !(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->id() === $borrowing->user_id))
                                                    <a 
                                                        href="{{ route('inventory.borrow.show', $borrowing) }}"
                                                        @click.stop
                                                        class="mt-2 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 font-bold py-1.5 px-3 rounded-lg text-xs transition-colors inline-flex items-center gap-1"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ __('ui.history') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="md:hidden space-y-4">
                                @foreach($borrowings as $borrowing)
                                    @php
                                        $user = auth()->user();
                                        $isSuperAdmin = $user->role === \App\Enums\UserRole::SUPERADMIN;
                                        $isAdmin = $user->role === \App\Enums\UserRole::ADMIN;
                                        $borrowerRole = $borrowing->user->role ?? null;
                                        $isOwn = $borrowing->user_id === $user->id;

                                        $canView = $isSuperAdmin
                                            || ($isAdmin && ($isOwn || ($borrowerRole === \App\Enums\UserRole::OPERATOR)))
                                            || $isOwn;
                                    @endphp

                                    @if($canView)
                                    <div class="bg-white border border-secondary-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all cursor-pointer" onclick="window.location.href='{{ route('inventory.borrow.show', $borrowing) }}'">
                                        <!-- Header: User & Status -->
                                        <div class="flex items-start justify-between mb-3 border-b border-gray-100 pb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-secondary-200 overflow-hidden flex-shrink-0">
                                                    @if($borrowing->user && $borrowing->user->avatar)
                                                        <img src="{{ asset('storage/' . $borrowing->user->avatar) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-secondary-500 text-xs font-bold">
                                                            {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-secondary-900">{{ $borrowing->user->name ?? 'User Terhapus' }}</h4>
                                                    <span class="text-xs text-secondary-500 block">{{ $borrowing->user->role ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                 @if($borrowing->status === 'borrowed')
                                                    <span class="bg-warning-100 text-warning-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>
                                                        {{ __('ui.status_borrowed') }}
                                                    </span>
                                                @elseif($borrowing->status === 'returned')
                                                    <span class="bg-success-100 text-success-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>
                                                        {{ __('ui.status_returned') }}
                                                    </span>
                                                @else
                                                    <span class="bg-danger-100 text-danger-800 text-xs font-bold px-2 py-1 rounded-lg">
                                                        {{ ucfirst($borrowing->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Body: Details Grid -->
                                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4">
                                            <div>
                                                <span class="text-[10px] text-secondary-400 block uppercase tracking-wider font-semibold">{{ __('ui.quantity') }}</span>
                                                <div class="font-medium text-secondary-900 flex items-center gap-1">
                                                    {{ $borrowing->quantity }} {{ $sparepart->unit }}
                                                    @if($borrowing->remaining_quantity < $borrowing->quantity)
                                                        <span class="text-xs text-secondary-500 font-normal bg-secondary-100 px-1.5 rounded">(Sisa: {{ $borrowing->remaining_quantity }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-secondary-400 block uppercase tracking-wider font-semibold">{{ __('ui.borrow_date') }}</span>
                                                <span class="font-medium text-secondary-900">{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] text-secondary-400 block uppercase tracking-wider font-semibold">{{ __('ui.expected_return_date') }}</span>
                                                <span class="font-medium text-secondary-900">{{ $borrowing->expected_return_at ? \Carbon\Carbon::parse($borrowing->expected_return_at)->translatedFormat('d F Y') : '-' }}</span>
                                            </div>
                                        </div>

                                        <!-- Footer: Actions -->
                                        <div class="flex items-center justify-end border-t border-gray-100 pt-3 gap-3">
                                            @if(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->id() === $borrowing->user_id)
                                                <button
                                                    type="button"
                                                    @click.stop="$dispatch('open-return-modal', { maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                                                    class="bg-success-50 text-success-700 hover:bg-success-100 font-bold py-2 px-4 rounded-lg text-sm w-full text-center transition-colors"
                                                >
                                                    {{ __('ui.return_item') }}
                                                </button>
                                            @else
                                                 <!-- View Evidence Button (Mobile) -->
                                                @if($borrowing->return_evidence)
                                                <button 
                                                    type="button"
                                                    @click.stop="
                                                        activeEvidence = {
                                                            image: '{{ asset('storage/' . $borrowing->return_evidence) }}',
                                                            notes: '{{ addslashes($borrowing->return_notes ?? '-') }}',
                                                            date: '{{ $borrowing->actual_return_date ? \Carbon\Carbon::parse($borrowing->actual_return_date)->translatedFormat('d F Y H:i') : '-' }}',
                                                            condition: '{{ $borrowing->return_condition ?? 'Baik' }}'
                                                        };
                                                        evidenceModalOpen = true;
                                                    "
                                                    class="text-secondary-500 hover:text-primary-600 transition-colors flex items-center gap-1 text-sm bg-gray-50 px-3 py-2 rounded-lg"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <span>{{ __('ui.evidence') }}</span>
                                                </button>
                                                @endif

                                                @if($borrowing->returns->count() > 0 || !(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->id() === $borrowing->user_id))
                                                <a 
                                                    href="{{ route('inventory.borrow.show', $borrowing) }}"
                                                    @click.stop
                                                    class="bg-secondary-100 text-secondary-700 hover:bg-secondary-200 font-bold py-2 px-4 rounded-lg text-sm w-full text-center transition-colors flex items-center justify-center gap-2"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ __('ui.history') }}
                                                </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-4 px-4">
                                {{ $borrowings->links() }}
                            </div>
                            @else
                                <div class="text-center py-8 text-secondary-400 bg-secondary-50 rounded-lg border border-dashed border-secondary-200">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm">{{ __('ui.no_borrowing_history') }}</p>
                                </div>
                            @endif
                        </div>
                </div>
                @endif
            
                <!-- Similar Items Section -->
                <div class="col-span-1 lg:col-span-3">
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2 flex items-center justify-between">
                            <span>{{ __('ui.similar_items') }}</span>
                            @if(isset($similarItems) && $similarItems->count() > 0)
                                <span class="text-xs font-normal text-secondary-500 bg-secondary-100 px-2 py-1 rounded-full">{{ $similarItems->total() }} {{ __('ui.items_found') }}</span>
                            @endif
                        </h3>
                        
                        @if(isset($similarItems) && $similarItems->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($similarItems as $item)
                            <a href="{{ route('inventory.show', $item) }}" class="group block border border-secondary-200 rounded-xl hover:border-primary-500 hover:shadow-md transition-all duration-200 bg-white overflow-hidden">
                                <div class="flex items-start p-4 gap-4">
                                    <!-- Thumbnail -->
                                    <div class="flex flex-col gap-2 w-20 flex-shrink-0">
                                        <div class="w-20 h-20 bg-secondary-100 rounded-lg overflow-hidden relative">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="flex items-center justify-center h-full text-secondary-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="h-1 w-full rounded-full {{ $item->type === 'sale' ? 'bg-success-600' : 'bg-primary-600' }}"></div>
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-secondary-900 truncate group-hover:text-primary-600 transition-colors">{{ $item->name }}</h4>
                                        <p class="text-xs text-secondary-500 mb-2 truncate">{{ $item->brand ?? __('ui.no_brand') }} • {{ $item->category }}</p>
                                        
                                        <div class="grid grid-cols-2 gap-y-1 gap-x-2 text-xs">
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.color') }}</span>
                                                <span class="font-medium text-secondary-700">{{ $item->color ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.condition') }}</span>
                                                <span class="font-medium text-secondary-700">{{ $item->condition }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.location') }}</span>
                                                <span class="font-medium text-secondary-700 truncate">{{ $item->location }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.stock') }}</span>
                                                <span class="font-bold {{ $item->stock <= ($item->minimum_stock ?? 0) ? 'text-danger-600' : 'text-success-600' }}">
                                                    {{ $item->stock }} {{ $item->unit ?? 'Pcs' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $similarItems->links() }}
                        </div>
                        @else
                            <div class="text-center py-8 text-secondary-400 bg-secondary-50 rounded-lg border border-dashed border-secondary-200">
                                <svg class="w-10 h-10 mx-auto mb-2 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                    <p class="text-sm">{{ __('ui.no_similar_items') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Return Modal -->
            <template x-teleport="body">
            <div x-show="returnModalOpen" 
                 style="display: none;"
                 class="fixed inset-0 z-[99]" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                
                <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6">
                    <!-- Backdrop -->
                    <div x-show="returnModalOpen"
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                         @click="returnModalOpen = false"
                         aria-hidden="true"></div>
            
                    <!-- Modal Panel -->
                    <div x-show="returnModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full flex flex-col max-h-[85vh]">
                        
                        <!-- Header (Fixed) -->
                        <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-success-100 mx-0">
                                    <svg class="h-6 w-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-4 text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ __('ui.return_item_title') }}
                                    </h3>

                                </div>
                            </div>
                        </div>

                        <!-- Form (Scrollable Body) -->
                        <!-- Form (Scrollable Body) -->
                        <form :action="`/inventory/borrow/${selectedBorrowing}/return`" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0" @submit.prevent="submitReturn">
                            @csrf
                            
                            <!-- Scrollable Content -->
                            <div class="flex-1 overflow-y-auto px-4 pt-2 pb-4 sm:px-6 space-y-4 custom-scrollbar">
                                
                                <!-- Error Display -->
                                <div x-show="Object.keys(errors).length > 0" class="mb-4 bg-danger-50 border border-danger-200 text-danger-700 px-4 py-3 rounded relative">
                                    <strong class="font-bold">{{ __('ui.save_failed') }}</strong>
                                    <ul class="list-disc list-inside text-sm mt-1">
                                        <template x-for="(fieldErrors, field) in errors" :key="field">
                                            <template x-for="error in fieldErrors">
                                                <li x-text="error"></li>
                                            </template>
                                        </template>
                                    </ul>
                                </div>
                                <div x-show="successMessage" class="mb-4 bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded relative">
                                    <strong class="font-bold" x-text="successMessage"></strong>
                                </div>
                                <style>
                                    /* Custom Scrollbar for better UX hint */
                                    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                                    .custom-scrollbar::-webkit-scrollbar-track { bg-gray-100; }
                                    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #CBD5E0; border-radius: 3px; }
                                </style>

                                <!-- Quantity -->
                                <div>
                                    <label for="return_qty" class="block text-sm font-medium text-gray-700">{{ __('ui.return_quantity') }} <span class="text-danger-500">*</span></label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" 
                                               name="return_quantity" 
                                               id="return_qty" 
                                               x-model="returnQty"
                                               :max="maxReturnQty"
                                               min="1"
                                               required
                                               @keydown="if(['-','+','e','E','.'].includes($event.key)) $event.preventDefault()"
                                               @input="returnQty = returnQty.replace(/[^0-9]/g, '')"
                                               class="focus:ring-success-500 focus:border-success-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                               placeholder="Jumlah">
                                    </div>
                                    <p class="mt-1 text-xs" 
                                       :class="{'text-danger-600': !isValid, 'text-gray-500': isValid}">
                                        Maks: <span x-text="maxReturnQty"></span>
                                    </p>
                                </div>

                                <!-- Condition (Custom Dropdown) -->
                                <div @click.outside="dropdownOpen = false" class="relative z-50">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.condition') }} <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <button type="button" @click="dropdownOpen = !dropdownOpen"
                                                class="input-field w-full text-left flex justify-between items-center rounded-xl py-2.5 px-4 text-sm cursor-pointer border border-gray-300 hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white transition-all shadow-sm">
                                            <span x-text="conditionLabel" :class="{'text-gray-900': returnCondition, 'text-gray-500': !returnCondition}" class="truncate mr-2"></span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>

                                        <div x-show="dropdownOpen" 
                                             style="display: none;"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute z-50 mt-1 w-full min-w-[100%] bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden">
                                            <div class="max-h-60 overflow-y-auto p-1 space-y-0.5">
                                                <template x-for="option in conditionOptions" :key="option.value">
                                                    <div @click="selectCondition(option)" 
                                                         class="px-3 py-2 rounded-lg cursor-pointer text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors text-sm"
                                                         :class="{'bg-primary-50 text-primary-700 font-medium': returnCondition === option.value}">
                                                        <span x-text="option.label"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="return_condition" x-model="returnCondition">
                                </div>

                                <!-- Multiple Evidence Images & Camera (Hidden if Lost) -->
                                <div class="mt-4" x-show="returnCondition !== 'lost'" x-data="{ 
                                    files: [], 
                                    previews: [],
                                    cameraOpen: false,
                                    stream: null,
                                    
                                    addFiles(e) {
                                        const newFiles = Array.from(e.target.files);
                                        this.processFiles(newFiles);
                                    },

                                    processFiles(newFiles) {
                                        if (this.files.length + newFiles.length > 5) {
                                            alert('Maksimal 5 foto');
                                            return;
                                        }
                                        this.files = this.files.concat(newFiles);
                                        this.updateInput();
                                        newFiles.forEach(file => {
                                            const reader = new FileReader();
                                            reader.onload = (e) => { this.previews.push(e.target.result); };
                                            reader.readAsDataURL(file);
                                        });
                                        $dispatch('file-change', this.files.length);
                                    },

                                    removeFile(index) {
                                        this.files.splice(index, 1);
                                        this.previews.splice(index, 1);
                                        this.updateInput();
                                        $dispatch('file-change', this.files.length);
                                    },

                                    updateInput() {
                                        const dt = new DataTransfer();
                                        this.files.forEach(file => dt.items.add(file));
                                        $refs.fileInput.files = dt.files;
                                    },

                                    // Camera Logic
                                    async startCamera() {
                                        this.cameraOpen = true;
                                        this.$nextTick(async () => {
                                            try {
                                                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                                                this.$refs.video.srcObject = this.stream;
                                            } catch (err) {
                                                alert('Tidak dapat mengakses kamera: ' + err.message);
                                                this.cameraOpen = false;
                                            }
                                        });
                                    },

                                    stopCamera() {
                                        if (this.stream) {
                                            this.stream.getTracks().forEach(track => track.stop());
                                            this.stream = null;
                                        }
                                        this.cameraOpen = false;
                                    },

                                    capturePhoto() {
                                        const video = this.$refs.video;
                                        const canvas = this.$refs.canvas;
                                        canvas.width = video.videoWidth;
                                        canvas.height = video.videoHeight;
                                        const ctx = canvas.getContext('2d');
                                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                        
                                        canvas.toBlob(blob => {
                                            const file = new File([blob], 'camera_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                                            this.processFiles([file]);
                                            this.stopCamera();
                                        }, 'image/jpeg', 0.8);
                                    },

                                    triggerGallery() {
                                        $refs.galleryInput.click();
                                    }
                                }">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Foto <span class="text-danger-500">*</span></label>
                                    
                                    <!-- Split Buttons (Pill/Chip Style - Full Width) -->
                                    <div class="grid grid-cols-2 gap-3 mb-4">
                                        <button type="button" @click="startCamera" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 rounded-full bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 transition-colors duration-200 border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="text-sm font-semibold">Buka Kamera</span>
                                        </button>

                                        <button type="button" @click="triggerGallery" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 rounded-full bg-purple-50 text-purple-700 hover:bg-purple-100 hover:text-purple-800 transition-colors duration-200 border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm font-semibold">Pilih Galeri</span>
                                        </button>
                                    </div>

                                    <!-- Camera Overlay -->
                                    <div x-show="cameraOpen" 
                                         style="display: none;"
                                         class="fixed inset-0 z-[60] bg-black bg-opacity-90 flex flex-col items-center justify-center p-4">
                                        <div class="relative w-full max-w-lg bg-black rounded-lg overflow-hidden shadow-2xl">
                                            <video x-ref="video" autoplay playsinline class="w-full h-auto object-cover"></video>
                                            <canvas x-ref="canvas" class="hidden"></canvas>
                                            
                                            <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-6 items-center">
                                                <button type="button" @click="stopCamera" class="p-3 bg-white/20 hover:bg-white/30 rounded-full text-white backdrop-blur-sm transition-all">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button type="button" @click="capturePhoto" class="p-4 bg-white rounded-full text-primary-600 shadow-lg hover:scale-105 transition-transform">
                                                    <div class="w-12 h-12 rounded-full border-4 border-primary-600 flex items-center justify-center">
                                                        <div class="w-10 h-10 bg-primary-600 rounded-full"></div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-white mt-4 text-sm font-medium">Pose dan ambil foto</p>
                                    </div>

                                    <!-- Hidden Inputs -->
                                    <input type="file" x-ref="galleryInput" accept="image/*" multiple class="hidden" @change="addFiles($event)">
                                    <input type="file" name="return_photos[]" multiple class="hidden" x-ref="fileInput">

                                    <!-- Previews Grid -->
                                    <div class="grid grid-cols-3 gap-3" x-show="previews.length > 0">
                                        <template x-for="(preview, index) in previews" :key="index">
                                            <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 group bg-gray-50">
                                                <img :src="preview" class="w-full h-full object-cover">
                                                <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors z-10 w-6 h-6 flex items-center justify-center">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500" x-show="files.length > 0">Maksimal 5 foto (Wajib)</p>
                                    <input type="hidden" id="file_count" :value="files.length">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan <span class="text-secondary-400 font-normal text-xs">(Opsional)</span></label>
                                    <textarea name="return_notes" id="notes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Keterangan tambahan..."></textarea>
                                </div>
                            </div>

                            <!-- Footer Actions (Fixed) -->
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-3 border-t border-gray-200 flex-none z-10 shadow-[0_-2px_4px_rgba(0,0,0,0.05)]"
                                 x-data="{ hasFiles: false }" 
                                 @file-change.window="hasFiles = $event.detail > 0">
                                <button type="submit" 
                                        :disabled="!isValid || isSubmitting || (returnCondition !== 'lost' && !hasFiles)"
                                        :class="{ 'opacity-50 cursor-not-allowed': !isValid || isSubmitting || (returnCondition !== 'lost' && !hasFiles), 'hover:bg-primary-700': isValid && !isSubmitting && (returnCondition === 'lost' || hasFiles) }"
                                        class="flex-1 sm:flex-none sm:w-auto inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm transition-all">
                                    <span x-show="!isSubmitting">Konfirmasi</span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Proses...
                                    </span>
                                </button>
                                <button type="button" @click="returnModalOpen = false" class="flex-1 sm:flex-none sm:w-auto mt-0 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </template>
            <!-- Evidence Modal -->
    <template x-teleport="body">
        <div x-show="evidenceModalOpen" 
             style="display: none;"
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
                            <span class="sr-only">Close</span>
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

    </div>
    </div>
    @include('inventory.partials.alpine_script')
</x-app-layout>
