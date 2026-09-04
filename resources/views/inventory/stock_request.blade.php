<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('ui.request_stock_change_for') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">Item: <span class="font-semibold text-secondary-900">{{ $sparepart->name }}</span> ({{ $sparepart->part_number }})</p>
            </div>

            <div class="card p-6 overflow-visible" x-data="{ isSubmitting: false, type: '{{ old('type', 'masuk') }}', quantity: {{ old('quantity', 1) }} }">
                <form action="{{ route('inventory.stock.request.store', $sparepart) }}" method="POST" @submit="isSubmitting = true" novalidate>
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Tipe Transaksi (Cards) -->
                        <div>
                            <label class="input-label mb-3 block">{{ __('ui.transaction_type') }} <span class="text-danger-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Stok Masuk -->
                                <label class="cursor-pointer border rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-success-50 transition-all duration-200" 
                                       :class="{ 'border-success-500 bg-success-50 ring-2 ring-success-500 ring-offset-1': type === 'masuk', 'border-secondary-200 bg-white': type !== 'masuk' }">
                                    <input type="radio" name="type" value="masuk" x-model="type" class="sr-only">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors"
                                         :class="{ 'bg-success-100 text-success-600': type === 'masuk', 'bg-secondary-100 text-secondary-500': type !== 'masuk' }">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </div>
                                    <div class="text-center">
                                        <span class="block font-bold" :class="{ 'text-success-700': type === 'masuk', 'text-secondary-900': type !== 'masuk' }">{{ __('ui.stock_in_simple') }}</span>
                                        <span class="block text-xs text-secondary-500 mt-1">Menambah jumlah stok ke dalam gudang</span>
                                    </div>
                                </label>

                                <!-- Stok Keluar -->
                                <label class="cursor-pointer border rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-danger-50 transition-all duration-200" 
                                       :class="{ 'border-danger-500 bg-danger-50 ring-2 ring-danger-500 ring-offset-1': type === 'keluar', 'border-secondary-200 bg-white': type !== 'keluar' }">
                                    <input type="radio" name="type" value="keluar" x-model="type" class="sr-only">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors"
                                         :class="{ 'bg-danger-100 text-danger-600': type === 'keluar', 'bg-secondary-100 text-secondary-500': type !== 'keluar' }">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                    </div>
                                    <div class="text-center">
                                        <span class="block font-bold" :class="{ 'text-danger-700': type === 'keluar', 'text-secondary-900': type !== 'keluar' }">{{ __('ui.stock_out_simple') }}</span>
                                        <span class="block text-xs text-secondary-500 mt-1">Mengurangi jumlah stok untuk pemakaian</span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <label for="quantity" class="input-label block">{{ __('ui.quantity') }} <span class="text-danger-500">*</span></label>
                            <div class="flex items-center gap-2 mt-1">
                                <button type="button" @click="quantity > 1 ? quantity-- : null" class="w-12 h-11 flex items-center justify-center rounded-xl border border-secondary-200 bg-secondary-50 text-secondary-600 hover:bg-secondary-100 transition-colors font-bold select-none active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <input id="quantity" class="input-field text-center font-bold text-lg max-w-[120px]" type="number" name="quantity" x-model.number="quantity" min="1" autofocus @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" />
                                <button type="button" @click="quantity++" class="w-12 h-11 flex items-center justify-center rounded-xl border border-secondary-200 bg-secondary-50 text-secondary-600 hover:bg-secondary-100 transition-colors font-bold select-none active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- Alasan -->
                        <div>
                            <label for="reason" class="input-label block">{{ __('ui.reason') }} <span class="text-danger-500">*</span></label>
                            <input id="reason" class="input-field w-full mt-1" type="text" name="reason" value="{{ old('reason') }}" placeholder="{{ __('ui.reason_placeholder') }}" />
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                        <a href="{{ route('inventory.show', $sparepart) }}" class="btn btn-secondary">
                            {{ __('ui.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary" :disabled="isSubmitting" :class="{'opacity-75 cursor-not-allowed': isSubmitting}">
                            <span x-show="!isSubmitting">{{ __('ui.submit_request') }}</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

