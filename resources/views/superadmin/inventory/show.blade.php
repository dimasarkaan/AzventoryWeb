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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Left Column (Visual & Main Info) -->
                <div class="lg:col-span-2 space-y-4">
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
                    <div class="card p-6">
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
                <div class="space-y-4">
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

                        <div x-data="{ stockModalOpen: false, borrowModalOpen: false }">
                            <div class="mt-6 pt-4 border-t border-secondary-100">
                                @if($sparepart->type === 'asset')
                                    <div class="grid grid-cols-1 gap-3">
                                        <button @click="borrowModalOpen = true" class="btn btn-primary w-full justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            Pinjam Barang
                                        </button>
                                        <button @click="stockModalOpen = true" class="btn btn-secondary w-full justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                            Update Stok
                                        </button>
                                    </div>
                                @else
                                    <button @click="stockModalOpen = true" class="btn btn-primary w-full justify-center">
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

                                    <div x-show="stockModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <form action="{{ route('superadmin.inventory.stock.request.store', $sparepart) }}" method="POST">
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
                                                                <label class="block text-sm font-medium text-gray-700">Tipe Perubahan</label>
                                                                <div class="mt-2 grid grid-cols-2 gap-3">
                                                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none">
                                                                        <input type="radio" name="type" value="masuk" class="peer sr-only" required checked>
                                                                        <div class="w-full text-center peer-checked:text-primary-600">
                                                                            <span class="block text-sm font-medium">Stok Masuk (+)</span>
                                                                        </div>
                                                                        <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-primary-500 pointer-events-none"></div>
                                                                    </label>
                                                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none">
                                                                        <input type="radio" name="type" value="keluar" class="peer sr-only" required>
                                                                         <div class="w-full text-center peer-checked:text-danger-600">
                                                                            <span class="block text-sm font-medium">Stok Keluar (-)</span>
                                                                        </div>
                                                                         <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-danger-500 pointer-events-none"></div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                                                <div class="relative mt-1 rounded-md shadow-sm">
                                                                    <input type="number" name="quantity" id="quantity" min="1" class="form-input block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: 10" required>
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label for="reason" class="block text-sm font-medium text-gray-700">Alasan</label>
                                                                <div class="mt-1">
                                                                    <textarea name="reason" id="reason" rows="3" class="form-textarea block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: Barang rusak, Stok opname, Pembelian baru..." required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
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
                        </div>

                        <!-- Borrow Modal -->
                        <div x-show="borrowModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="borrowModalOpen" @click="borrowModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                <div x-show="borrowModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                    <form action="{{ route('superadmin.inventory.borrow.store', $sparepart) }}" method="POST">
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
                                                            <label for="borrower_name" class="block text-sm font-medium text-gray-700">Nama Peminjam</label>
                                                            <input type="text" name="borrower_name" id="borrower_name" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Nama Lengkap" required>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="borrow_quantity" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                                            <input type="number" name="quantity" id="borrow_quantity" min="1" max="{{ $sparepart->stock }}" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Contoh: 1" required>
                                                        </div>

                                                        <div>
                                                            <label for="expected_return_at" class="block text-sm font-medium text-gray-700">Estimasi Kembali (Opsional)</label>
                                                            <input type="date" name="expected_return_at" id="expected_return_at" class="form-input mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                                        </div>

                                                        <div>
                                                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                                                            <textarea name="notes" id="notes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Keperluan..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
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

                    <!-- QR Code Card -->
                    <div class="card p-6">
                        <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wider">Identifikasi QR Code</h3>
                        <div class="flex flex-col items-center">
                            @if ($sparepart->qr_code_path)
                                <div class="bg-white p-2 rounded-xl shadow-sm border border-secondary-100 mb-4">
                                     <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code" class="w-40 h-40">
                                </div>
                                <div class="grid grid-cols-2 gap-3 w-full">
                                    <a href="{{ route('superadmin.inventory.qr.download', $sparepart) }}" class="btn btn-secondary justify-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        Unduh
                                    </a>
                                     <a href="{{ route('superadmin.inventory.qr.print', $sparepart) }}" target="_blank" class="btn btn-secondary justify-center text-sm">
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
                    </div>

                    <!-- Meta Info -->
                    <div class="text-xs text-secondary-400 space-y-1 px-2">
                        <p>Dibuat: {{ $sparepart->created_at->isoFormat('D MMMM Y HH:mm') }}</p>
                        <p>Diperbarui: {{ $sparepart->updated_at->isoFormat('D MMMM Y HH:mm') }}</p>
                    </div>
                </div>

                <!-- Active Borrowings Section (Only for Assets) -->
                @if($sparepart->type === 'asset' && $sparepart->borrowings->count() > 0)
                <div class="col-span-1 lg:col-span-3">
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Status Peminjaman Aktif</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-secondary-500 uppercase bg-secondary-50">
                                    <tr>
                                        <th class="px-4 py-3">Peminjam</th>
                                        <th class="px-4 py-3">Jumlah</th>
                                        <th class="px-4 py-3">Tgl Pinjam</th>
                                        <th class="px-4 py-3">Estimasi Kembali</th>
                                        <th class="px-4 py-3">Catatan</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-secondary-100">
                                    @foreach($sparepart->borrowings as $borrowing)
                                    <tr class="hover:bg-secondary-50">
                                        <td class="px-4 py-3 font-medium text-secondary-900">{{ $borrowing->borrower_name }}</td>
                                        <td class="px-4 py-3">{{ $borrowing->quantity }} {{ $sparepart->unit ?? 'Pcs' }}</td>
                                        <td class="px-4 py-3">{{ $borrowing->borrowed_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            @if($borrowing->expected_return_at)
                                                <span class="{{ $borrowing->expected_return_at->isPast() ? 'text-danger-600 font-bold' : '' }}">
                                                    {{ $borrowing->expected_return_at->format('d M Y') }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-secondary-500 truncate max-w-xs">{{ $borrowing->notes ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <form action="{{ route('superadmin.inventory.borrow.return', $borrowing) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian barang?')">
                                                @csrf
                                                <button type="submit" class="text-xs px-3 py-1.5 bg-success-50 text-success-700 rounded-md hover:bg-success-100 font-medium transition-colors">
                                                    Kembalikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
