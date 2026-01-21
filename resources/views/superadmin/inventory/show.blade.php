<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                            {{ $sparepart->name }}
                        </h2>
                        @if($sparepart->status === 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Non-Aktif</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-1.5 text-secondary-500 font-mono text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                        {{ $sparepart->part_number }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('superadmin.inventory.index') }}" class="btn btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                    <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-warning">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:auto-rows-fr">
                <!-- Left Column (Visual & Main Info) -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <!-- Image Card -->
                    <div class="card overflow-hidden">
                        <div class="aspect-video w-full bg-secondary-100 flex items-center justify-center relative">
                            @if($sparepart->image)
                                <img src="{{ asset('storage/' . $sparepart->image) }}" alt="{{ $sparepart->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-secondary-400 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-sm">Tidak ada gambar</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Details Card -->
                    <div class="card p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Informasi Detail</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Kategori</span>
                                <div class="mt-1 text-secondary-900 font-medium flex items-center gap-2">
                                    <span class="p-1.5 bg-primary-50 text-primary-600 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                    </span>
                                    {{ $sparepart->category }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Merk</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->brand ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Warna</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->color ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Lokasi Penyimpanan</span>
                                <div class="mt-1 text-secondary-900 font-medium flex items-center gap-2">
                                     <span class="p-1.5 bg-warning-50 text-warning-600 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </span>
                                    {{ $sparepart->location }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Kondisi Barang</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->condition }}
                                </div>
                            </div>
                            @if($sparepart->type === 'sale')
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Harga Satuan</span>
                                <div class="mt-1 text-secondary-900 font-bold text-lg">
                                    Rp {{ number_format($sparepart->price, 0, ',', '.') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column (Stock & QR) -->
                <div class="flex flex-col gap-4">
                    <!-- Stock Card -->
                    <div class="card p-6 border-t-4 border-primary-500">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Stok Tersedia</span>
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
                                 <span class="font-bold block">Stok Menipis!</span>
                                 Stok berada di bawah batas minimum ({{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }}).
                             </div>
                        </div>
                        @else
                         <div class="mt-4 bg-success-50 text-success-700 p-3 rounded-lg text-sm flex items-center gap-2 border border-success-100">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <span>Stok Aman (Min: {{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }})</span>
                        </div>
                        @endif

                        <!-- Actions Wrapper with Alpine Data -->
                        <div x-data="{ stockModalOpen: false, borrowModalOpen: false }">
                             <div class="mt-6 pt-4 border-t border-secondary-100 grid grid-cols-1 gap-3">
                                @if($sparepart->type === 'asset')
                                    <button @click="borrowModalOpen = true" type="button" class="btn btn-primary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        Pinjam Barang
                                    </button>
                                    <button @click="stockModalOpen = true" type="button" class="btn btn-secondary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Update Stok
                                    </button>
                                @else
                                    <button @click="stockModalOpen = true" type="button" class="btn btn-primary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Ajukan Perubahan Stok
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
                                        <form action="{{ route('superadmin.inventory.stock.request.store', $sparepart) }}" method="POST" novalidate>
                                            @csrf
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 sm:mx-0 sm:h-10 sm:w-10 text-primary-600">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            Ajukan Perubahan Stok
                                                        </h3>
                                                        <div class="mt-4 space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Tipe Perubahan <span class="text-danger-500">*</span></label>
                                                                <div class="mt-2 grid grid-cols-2 gap-3">
                                                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors">
                                                                        <input type="radio" name="type" value="masuk" x-model="type" class="peer sr-only">
                                                                        <div class="w-full text-center peer-checked:text-primary-600 font-medium text-gray-500">
                                                                            <span class="block text-sm">Stok Masuk (+)</span>
                                                                        </div>
                                                                        <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-primary-500 pointer-events-none transition-all"></div>
                                                                    </label>
                                                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors">
                                                                        <input type="radio" name="type" value="keluar" x-model="type" class="peer sr-only">
                                                                         <div class="w-full text-center peer-checked:text-danger-600 font-medium text-gray-500">
                                                                            <span class="block text-sm">Stok Keluar (-)</span>
                                                                        </div>
                                                                         <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-danger-500 pointer-events-none transition-all"></div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah <span class="text-danger-500">*</span></label>
                                                                <div class="relative mt-1 rounded-md shadow-sm">
                                                                    <input type="number" name="quantity" id="quantity" x-model="quantity" min="1" class="form-input block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: 10">
                                                                </div>
                                                                <p x-show="isStockError" x-transition class="text-danger-500 text-xs mt-1 font-medium flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    Jumlah melebihi stok tersedia ({{ $sparepart->stock }}).
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <label for="reason" class="block text-sm font-medium text-gray-700">Alasan <span class="text-danger-500">*</span></label>
                                                                <div class="mt-1">
                                                                    <textarea name="reason" id="reason" x-model="reason" rows="3" class="form-textarea block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: Barang rusak, Stok opname, Pembelian baru..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" 
                                                        :disabled="!isValid"
                                                        :class="{ 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400': !isValid, 'bg-primary-600 hover:bg-primary-700': isValid }"
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                                    Simpan
                                                </button>
                                                <button type="button" @click="stockModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Batal
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
                                            get isQuantityValid() { return this.quantity > 0 && this.quantity <= this.maxStock; },
                                            get isQuantityError() { return this.quantity !== '' && this.quantity > this.maxStock; }
                                         }"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <form action="{{ route('superadmin.inventory.borrow.store', $sparepart) }}" method="POST" novalidate>
                                            @csrf
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 sm:mx-0 sm:h-10 sm:w-10 text-primary-600">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            Pinjam Barang
                                                        </h3>
                                                        <div class="mt-4 space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700">Nama Peminjam</label>
                                                                <div class="mt-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 sm:text-sm">
                                                                    {{ Auth::user()->name }}
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label for="borrow_quantity" class="block text-sm font-medium text-gray-700">Jumlah <span class="text-danger-500">*</span></label>
                                                                <input type="number" name="quantity" id="borrow_quantity" x-model="quantity" min="1" :max="maxStock" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: 1" required>
                                                                <!-- Custom Error Message -->
                                                                <p x-show="isQuantityError" style="display: none;" class="text-danger-500 text-xs mt-1 font-medium flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                    Jumlah tidak boleh melebihi stok tersedia ({{ $sparepart->stock }}).
                                                                </p>
                                                                <p x-show="!isQuantityError" class="text-secondary-400 text-xs mt-1">
                                                                    Stok tersedia: {{ $sparepart->stock }} {{ $sparepart->unit ?? 'Pcs' }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <label for="expected_return_at" class="block text-sm font-medium text-gray-700">Estimasi Kembali <span class="text-secondary-400 font-normal">(Opsional)</span></label>
                                                                <input type="date" name="expected_return_at" id="expected_return_at" min="{{ date('Y-m-d') }}" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                                            </div>

                                                            <div>
                                                                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan <span class="text-secondary-400 font-normal">(Opsional)</span></label>
                                                                <textarea name="notes" id="notes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Keperluan..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" 
                                                        :disabled="!isQuantityValid"
                                                        :class="{ 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400': !isQuantityValid, 'bg-primary-600 hover:bg-primary-700': isQuantityValid }"
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                                    Konfirmasi Pinjam
                                                </button>
                                                <button type="button" @click="borrowModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Batal
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
                        <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wider flex-none">Identifikasi QR Code</h3>
                        
                        <div class="flex-1 flex flex-col items-center justify-center min-h-[200px]"> <!-- Centered Content area -->
                            @if ($sparepart->qr_code_path)
                                <div class="bg-white p-2 rounded-xl shadow-sm border border-secondary-100 mb-6">
                                     <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code" class="w-56 h-56">
                                </div>
                                <div class="grid grid-cols-2 gap-3 w-full max-w-xs">
                                    <a href="{{ route('superadmin.inventory.qr.download', $sparepart) }}" class="btn btn-secondary justify-center text-sm py-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        Unduh
                                    </a>
                                     <a href="{{ route('superadmin.inventory.qr.print', $sparepart) }}" target="_blank" class="btn btn-secondary justify-center text-sm py-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        Cetak
                                    </a>
                                </div>
                            @else
                                <div class="bg-secondary-50 w-full h-40 rounded-xl flex items-center justify-center text-secondary-400 mb-4">
                                    <span class="text-sm italic">Belum ada QR Code</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-auto pt-6 border-t border-gray-100 text-xs text-secondary-400 space-y-1 text-center flex-none">
                            <p>Dibuat: {{ $sparepart->created_at->isoFormat('D MMMM Y HH:mm') }}</p>
                            <p>Diperbarui: {{ $sparepart->updated_at->isoFormat('D MMMM Y HH:mm') }}</p>
                        </div>
                    </div>

                    <!-- Meta Info -->

                </div>

                <!-- Active Borrowings Section (Only for Assets) -->
                @if($sparepart->type === 'asset' && $sparepart->borrowings->count() > 0)
                <div class="col-span-1 lg:col-span-3">
                    <div x-data="{ 
                        returnModalOpen: false, 
                        evidenceModalOpen: false, 
                        selectedBorrowing: null, 
                        activeEvidence: {},
                        errors: {},
                        successMessage: '',
                        activeEvidence: {},
                        maxReturnQty: 0,
                        errors: {},
                        successMessage: '',
                        isSubmitting: false,

                        async submitReturn(e) {
                            this.isSubmitting = true;
                            this.errors = {};
                            this.successMessage = '';

                            const form = e.target;
                            const action = form.getAttribute('action');
                            const formData = new FormData(form);

                            try {
                                const response = await fetch(action, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                });

                                const data = await response.json();

                                if (!response.ok) {
                                    if (response.status === 422) {
                                        this.errors = data.errors;
                                    } else {
                                        alert(data.message || 'Terjadi kesalahan sistem.');
                                    }
                                    return;
                                }

                                this.successMessage = data.message;
                                // alert(data.message); // Removed as per request
                                
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);

                            } catch (error) {
                                alert('Gagal menghubungi server.');
                                console.error(error);
                            } finally {
                                this.isSubmitting = false;
                            }
                        }
                    }" class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Riwayat Peminjaman</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-secondary-500 uppercase bg-secondary-50">
                                    <tr>
                                        <th class="px-4 py-3 whitespace-nowrap">Peminjam</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Jumlah</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Tgl Pinjam</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Estimasi Kembali</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Status</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-secondary-100">

                                    @foreach($sparepart->borrowings->sortByDesc('created_at') as $borrowing)
                                        @php
                                            $user = auth()->user();
                                            $isSuperAdmin = $user->role === 'superadmin';
                                            $isAdmin = $user->role === 'admin';
                                            $borrowerRole = $borrowing->user->role ?? 'user';
                                            $isOwn = $borrowing->user_id === $user->id;

                                            // Visibility Logic
                                            $canView = $isSuperAdmin 
                                                || ($isAdmin && ($isOwn || $borrowerRole === 'operator')) 
                                                || $isOwn;
                                        @endphp
                                        
                                        @if($canView)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap font-medium text-secondary-900">{{ $borrowing->borrower_name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">{{ $borrowing->quantity }} {{ $sparepart->unit ?? 'Pcs' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">{{ $borrowing->borrowed_at->translatedFormat('d F Y H:i') }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($borrowing->expected_return_at)
                                                        <span class="{{ $borrowing->status == 'borrowed' && $borrowing->expected_return_at->isPast() ? 'text-danger-600 font-bold' : '' }}">
                                                            {{ $borrowing->expected_return_at->translatedFormat('d F Y') }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($borrowing->status == 'borrowed')
                                                        @if($borrowing->expected_return_at && $borrowing->expected_return_at->isPast())
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-100 text-danger-800">
                                                                Terlambat
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-800">
                                                                Dipinjam
                                                            </span>
                                                        @endif
                                                    @elseif($borrowing->status == 'returned')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-800">
                                                            Dikembalikan
                                                        </span>
                                                    @elseif($borrowing->status == 'lost')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            Hilang
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                                    @if($borrowing->status == 'borrowed' && $isOwn)
                                                        <button @click="returnModalOpen = true; selectedBorrowing = {{ $borrowing->id }}; maxReturnQty = {{ $borrowing->quantity }}" 
                                                                type="button" 
                                                                class="text-xs px-3 py-1.5 bg-success-50 text-success-700 rounded-md hover:bg-success-100 font-medium transition-colors">
                                                            Kembalikan
                                                        </button>
                                                    @elseif($borrowing->status == 'returned' || $borrowing->status == 'lost')
                                                        <button @click="evidenceModalOpen = true; activeEvidence = {
                                                                    condition: '{{ $borrowing->return_condition }}',
                                                                    notes: {{ json_encode($borrowing->return_notes ?? '-') }},
                                                                    returned_at: '{{ $borrowing->returned_at ? $borrowing->returned_at->translatedFormat('d F Y H:i') : '-' }}',
                                                                    photos: {{ json_encode($borrowing->return_photos ?? []) }}
                                                                }" 
                                                                type="button" 
                                                                class="text-xs px-3 py-1.5 bg-secondary-50 text-secondary-700 rounded-md hover:bg-secondary-100 font-medium transition-colors">
                                                            Detail
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Evidence Modal -->
                        <div x-show="evidenceModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                             <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="evidenceModalOpen" @click="evidenceModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="evidenceModalOpen" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                    <div class="bg-white px-4 pt-5 pb-2 sm:p-6 sm:pb-2">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Detail Pengembalian</h3>
                                    </div>
                                    <div class="bg-white px-4 pb-4 sm:p-6 sm:pb-4 max-h-[60vh] overflow-y-auto">
                                        
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs text-secondary-500 uppercase tracking-wide">Kondisi</label>
                                                    <span class="font-medium text-secondary-900" x-text="activeEvidence.condition == 'good' ? 'Baik' : (activeEvidence.condition == 'broken' ? 'Rusak' : activeEvidence.condition)"></span>
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-secondary-500 uppercase tracking-wide">Dikembalikan Pada</label>
                                                    <span class="font-medium text-secondary-900" x-text="activeEvidence.returned_at"></span>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs text-secondary-500 uppercase tracking-wide">Catatan</label>
                                                <p class="text-sm text-secondary-700 bg-gray-50 p-2 rounded mt-1" x-text="activeEvidence.notes"></p>
                                            </div>

                                            <div>
                                                <label class="block text-xs text-secondary-500 uppercase tracking-wide mb-2">Bukti Foto</label>
                                                <div class="grid grid-cols-3 gap-3" x-show="activeEvidence.photos && activeEvidence.photos.length > 0">
                                                    <template x-for="photo in activeEvidence.photos">
                                                        <a :href="'/storage/' + photo" target="_blank" class="block aspect-square w-full bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-75 transition group relative">
                                                            <img :src="'/storage/' + photo" class="w-full h-full object-cover">
                                                        </a>
                                                    </template>
                                                </div>
                                                <p x-show="!activeEvidence.photos || activeEvidence.photos.length === 0" class="text-sm text-secondary-400 italic">Tidak ada foto dilampirkan.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="button" @click="evidenceModalOpen = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Return Modal -->
                        <div x-show="returnModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center sm:block sm:p-0">
                                <div x-show="returnModalOpen" @click="returnModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                <div x-show="returnModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                    <form :action="'{{ url('superadmin/inventory/borrow') }}/' + selectedBorrowing + '/return'" method="POST" enctype="multipart/form-data" @submit.prevent="submitReturn">
                                        @csrf
                                        <div class="bg-white px-4 pt-5 pb-2 sm:p-6 sm:pb-2">
                                            <!-- Success Message (Fixed) -->
                                             <div x-show="successMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                                                 <span class="block sm:inline" x-text="successMessage"></span>
                                             </div>

                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10 text-green-600">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Pengembalian Barang
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white px-4 pb-4 sm:p-6 sm:pb-4 max-h-[60vh] overflow-y-auto">
                                            <div class="space-y-4">
                                                <p class="text-sm text-gray-500">Pastikan barang yang dikembalikan sesuai dengan data peminjaman.</p>
                                                        
                                                        <!-- Quantity Input -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Jumlah Dikembalikan <span class="text-danger-500">*</span></label>
                                                            <input type="number" name="return_quantity" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                                   min="1" :max="maxReturnQty" :value="maxReturnQty" required>
                                                            <p class="text-xs text-gray-500 mt-1">Maksimal: <span x-text="maxReturnQty"></span> Pcs</p>
                                                        </div>

                                                        <!-- Condition -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Kondisi Barang <span class="text-danger-500">*</span></label>
                                                            <div class="mt-2 space-y-2">
                                                                <label class="flex items-center">
                                                                    <input type="radio" name="return_condition" value="good" class="text-primary-600 focus:ring-primary-500" checked>
                                                                    <span class="ml-2 text-sm text-gray-700">Baik (Kembali ke Stok)</span>
                                                                </label>
                                                                <label class="flex items-center">
                                                                    <input type="radio" name="return_condition" value="bad" class="text-danger-600 focus:ring-danger-500">
                                                                    <span class="ml-2 text-sm text-gray-700">Rusak (Tidak Masuk Stok)</span>
                                                                </label>
                                                                <label class="flex items-center">
                                                                    <input type="radio" name="return_condition" value="lost" class="text-gray-600 focus:ring-gray-500">
                                                                    <span class="ml-2 text-sm text-gray-700">Hilang (Tidak Masuk Stok)</span>
                                                                </label>
                                                            </div>
                                                            <template x-if="errors.return_condition">
                                                                <p class="text-danger-500 text-xs mt-1" x-text="errors.return_condition[0]"></p>
                                                            </template>
                                                        </div>

                                                        <!-- Photos Manager -->
                                                        <div x-data="{
                                                            files: [],
                                                            cameraOpen: false,
                                                            stream: null,
                                                            
                                                            init() {
                                                                this.$watch('files', () => {
                                                                    const dataTransfer = new DataTransfer();
                                                                    this.files.forEach(file => dataTransfer.items.add(file));
                                                                    this.$refs.returnPhotosInput.files = dataTransfer.files;
                                                                });
                                                            },

                                                            addFiles(e) {
                                                                const newFiles = Array.from(e.target.files);
                                                                // Filter validation (max 5 total)
                                                                if (this.files.length + newFiles.length > 5) {
                                                                    alert('Maksimal 5 foto.');
                                                                    return;
                                                                }
                                                                this.files = [...this.files, ...newFiles];
                                                            },

                                                            removeFile(index) {
                                                                this.files.splice(index, 1);
                                                            },

                                                            async openCamera() {
                                                                this.cameraOpen = true;
                                                                try {
                                                                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                                                                    this.$refs.video.srcObject = this.stream;
                                                                } catch (err) {
                                                                    alert('Gagal membuka kamera: ' + err.message);
                                                                    this.cameraOpen = false;
                                                                }
                                                            },

                                                            closeCamera() {
                                                                this.cameraOpen = false;
                                                                if (this.stream) {
                                                                    this.stream.getTracks().forEach(track => track.stop());
                                                                    this.stream = null;
                                                                }
                                                            },

                                                            takePhoto() {
                                                                if (this.files.length >= 5) {
                                                                    alert('Maksimal 5 foto.');
                                                                    return;
                                                                }
                                                                const canvas = document.createElement('canvas');
                                                                canvas.width = this.$refs.video.videoWidth;
                                                                canvas.height = this.$refs.video.videoHeight;
                                                                const ctx = canvas.getContext('2d');
                                                                ctx.drawImage(this.$refs.video, 0, 0, canvas.width, canvas.height);
                                                                
                                                                canvas.toBlob(blob => {
                                                                    const file = new File([blob], 'camera_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                                                                    this.files = [...this.files, file];
                                                                    this.closeCamera();
                                                                }, 'image/jpeg');
                                                            }
                                                        }">
                                                            <label class="block text-sm font-medium text-gray-700">Bukti Foto <span class="text-danger-500">*</span></label>
                                                            
                                                            <!-- Hidden Input for Form Submission -->
                                                            <input type="file" x-ref="returnPhotosInput" name="return_photos[]" class="hidden" multiple accept="image/*">

                                                            <!-- UI Controls -->
                                                            <div class="mt-2 flex gap-2">
                                                                <!-- Gallery Button -->
                                                                <label class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                                    <svg class="mr-2 -ml-1 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                    Pilih dari Galeri
                                                                    <input type="file" class="hidden" multiple accept="image/*" @change="addFiles($event)">
                                                                </label>

                                                                <!-- Camera Button -->
                                                                <button type="button" @click="openCamera()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                    Ambil Foto
                                                                </button>
                                                            </div>

                                                            <!-- File Previews -->
                                                            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                                                <template x-for="(file, index) in files" :key="index">
                                                                    <div class="relative group">
                                                                        <img :src="URL.createObjectURL(file)" class="h-24 w-full object-cover rounded-md border border-gray-200">
                                                                        <button type="button" @click="removeFile(index)" class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600">
                                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        </button>
                                                                        <p class="mt-1 text-xs text-gray-500 truncate" x-text="file.name"></p>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                            <p class="mt-1 text-xs text-secondary-500" x-show="files.length === 0">Belum ada foto yang dipilih. (Min 1, Max 5)</p>
                                                            
                                                            <template x-if="errors.return_photos">
                                                                <p class="text-danger-500 text-xs mt-1" x-text="errors.return_photos[0]"></p>
                                                            </template>
                                                             <template x-if="errors['return_photos.0']">
                                                                <p class="text-danger-500 text-xs mt-1" x-text="errors['return_photos.0'][0]"></p>
                                                            </template>

                                                            <!-- Camera Modal Overlay -->
                                                            <div x-show="cameraOpen" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-75">
                                                                <div class="bg-white p-4 rounded-lg shadow-xl w-full max-w-lg mx-4">
                                                                    <h3 class="text-lg font-bold mb-4">Ambil Foto</h3>
                                                                    <div class="relative bg-black rounded-lg overflow-hidden aspect-video">
                                                                        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                                                                    </div>
                                                                    <div class="mt-4 flex justify-between">
                                                                        <button type="button" @click="closeCamera()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button>
                                                                        <button type="button" @click="takePhoto()" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">Jepret Foto</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Notes -->
                                                        <div>
                                                            <label for="return_notes" class="block text-sm font-medium text-gray-700">Catatan Pengembalian</label>
                                                            <textarea name="return_notes" id="return_notes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Keterangan kondisi barang..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                            
                                            <!-- Fixed Footer Actions -->
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-success-600 text-base font-medium text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-wait">
                                                <span x-show="!isSubmitting">Konfirmasi Kembali</span>
                                                <span x-show="isSubmitting">Memproses...</span>
                                            </button>
                                            <button type="button" @click="returnModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
