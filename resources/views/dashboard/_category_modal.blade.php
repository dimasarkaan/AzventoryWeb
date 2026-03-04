<div x-show="showCategoryModal" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-secondary-900/60 backdrop-blur-sm" @click="showCategoryModal = false"></div>

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
                    <div class="p-2 bg-amber-50 rounded-xl text-amber-600">
                        <x-icon.category class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-secondary-900">Manajemen Kategori</h3>
                        <p class="text-xs text-secondary-500">Kelola master data kategori inventaris</p>
                    </div>
                </div>
                <button @click="showCategoryModal = false" class="p-2 text-secondary-400 hover:text-danger-600 hover:bg-danger-50 rounded-xl transition-colors">
                    <x-icon.close class="w-6 h-6" />
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto bg-secondary-50/30">
                {{-- Form Tambah Kategori Baru --}}
                <div class="mb-6 bg-white p-4 rounded-2xl border border-amber-100 shadow-sm">
                    <h4 class="text-xs font-bold text-secondary-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <x-icon.plus class="w-3 h-3" />
                        Tambah Kategori Baru
                    </h4>
                    <div class="flex gap-2">
                        <div class="relative flex-grow">
                            <input type="text" 
                                   x-model="newCategoryName"
                                   @keydown.enter="addCategory()"
                                   placeholder="Masukkan Nama Kategori Di Sini" 
                                   class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm font-bold text-secondary-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none">
                        </div>
                        <button @click="addCategory()" 
                                :disabled="isAddingCategory || !newCategoryName.trim()"
                                class="btn btn-primary px-5 rounded-xl flex items-center gap-2 shadow-lg shadow-primary-200/50 disabled:opacity-50 disabled:shadow-none transition-all">
                            <template x-if="isAddingCategory">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span class="font-bold text-sm" x-text="isAddingCategory ? 'Menyimpan...' : 'Tambah'"></span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-3 px-1">
                    <h4 class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Daftar Kategori</h4>
                    <span class="text-[10px] text-secondary-400 font-medium px-2 py-0.5 bg-secondary-100 rounded-full" x-text="categoriesList.length + ' Kategori'"></span>
                </div>

                <div x-show="isLoadingCategories" class="flex flex-col items-center justify-center py-12 gap-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                    <p class="text-sm text-secondary-500">Memuat data kategori...</p>
                </div>

                <div x-show="!isLoadingCategories" class="relative">
                    <div class="grid gap-3">
                        <template x-for="cat in categoriesList" :key="cat.id">
                            <div class="bg-white p-4 rounded-2xl border border-secondary-100 shadow-sm hover:shadow-md transition-all group flex items-center justify-between gap-4"
                                 :class="{'ring-2 ring-amber-500 border-transparent bg-amber-50/10': catEditingId === cat.id}">
                                
                                <div class="flex-grow min-w-0">
                                    {{-- Mode View --}}
                                    <div x-show="catEditingId !== cat.id">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-secondary-900 truncate" x-text="cat.name"></h4>
                                        </div>
                                        <p class="text-xs text-secondary-500 flex items-center gap-1">
                                            <span class="font-bold text-secondary-900" x-text="cat.items_count"></span> Barang dalam kategori ini
                                        </p>
                                    </div>

                                    {{-- Mode Edit --}}
                                    <div x-show="catEditingId === cat.id" x-cloak>
                                        <input type="text" 
                                               :id="'cat-edit-input-' + cat.id"
                                               x-model="catEditingName" 
                                               @keydown.enter="saveCatEdit(cat.id)"
                                               @keydown.escape="cancelCatEdit()"
                                               class="w-full bg-white border border-amber-300 rounded-xl px-3 py-2 text-sm font-bold text-secondary-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none"
                                               placeholder="Nama kategori...">
                                        <p class="text-[10px] text-secondary-400 mt-1 ml-1 font-medium italic">Tekan Enter untuk simpan, Esc untuk batal</p>
                                    </div>
                                </div>

                                {{-- Actions View --}}
                                <div x-show="catEditingId !== cat.id" class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button @click="startCatEdit(cat)"
                                            class="p-2 text-secondary-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                            title="Ubah Nama">
                                        <x-icon.edit class="w-5 h-5" />
                                    </button>

                                    <button @click="askCatDelete(cat)"
                                            class="p-2 text-secondary-400 hover:text-danger-600 hover:bg-danger-50 rounded-xl transition-all"
                                            title="Hapus Kategori">
                                        <x-icon.trash class="w-5 h-5" />
                                    </button>
                                </div>

                                {{-- Actions Edit --}}
                                <div x-show="catEditingId === cat.id" x-cloak class="flex items-center gap-2">
                                    <button @click="saveCatEdit(cat.id)"
                                            class="p-2 bg-success-500 text-white hover:bg-success-600 rounded-xl shadow-sm hover:shadow-md transition-all"
                                            title="Simpan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </button>

                                    <button @click="cancelCatEdit()"
                                            class="p-2 bg-secondary-100 text-secondary-600 hover:bg-secondary-200 rounded-xl transition-all"
                                            title="Batal">
                                        <x-icon.close class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Custom Delete Confirmation Overlay --}}
                    <div x-show="catConfirmDeleteId" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-x-0 -inset-y-4 bg-white/95 backdrop-blur-[2px] z-[20] flex items-center justify-center p-6 text-center"
                         x-cloak>
                        <div>
                            <div class="w-16 h-16 bg-danger-50 text-danger-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-danger-100">
                                <x-icon.trash class="w-8 h-8" />
                            </div>
                            <h4 class="text-secondary-900 font-bold text-lg mb-1">Hapus Kategori?</h4>
                            <p class="text-sm text-secondary-500 mb-6">
                                Anda yakin ingin menghapus kategori <span class="font-bold text-secondary-900" x-text="catConfirmDeleteName"></span>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                            <div class="flex items-center justify-center gap-3">
                                <button @click="cancelCatDelete()" 
                                        :disabled="isDeletingCat"
                                        class="btn btn-secondary px-6 py-2.5 rounded-2xl font-bold disabled:opacity-50 transition-all">Batal</button>
                                <button @click="deleteCategory(catConfirmDeleteId)" 
                                        :disabled="isDeletingCat"
                                        class="btn btn-danger px-6 py-2.5 rounded-2xl font-bold shadow-lg shadow-danger-200 disabled:opacity-50 transition-all flex items-center gap-2">
                                    <template x-if="isDeletingCat">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="isDeletingCat ? 'Menghapus...' : 'Ya, Hapus'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="categoriesList.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-4 text-secondary-400">
                            <x-icon.category class="w-8 h-8" />
                        </div>
                        <h4 class="text-secondary-900 font-bold">Belum Ada Kategori</h4>
                        <p class="text-sm text-secondary-500">Kategori akan otomatis bertambah saat Anda menyimpan barang baru.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
