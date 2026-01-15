<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sparepart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('superadmin.inventory.update', $sparepart) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Sparepart -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Sparepart')" :required="true" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $sparepart->name)" autofocus />
                            <x-input-helper>{{ __('Contoh: Keyboard Logitech G Pro X') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Part Number -->
                        <div>
                            <x-input-label for="part_number" :value="__('Part Number (PN)')" :required="true" />
                            <x-text-input id="part_number" class="block mt-1 w-full" type="text" name="part_number" :value="old('part_number', $sparepart->part_number)" />
                            <x-input-helper>{{ __('Contoh: KBD-LOGI-GPRO-X') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                        </div>

                        <!-- Kategori -->
                        <div>
                            <x-input-label for="category" :value="__('Kategori')" :required="true" />
                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category', $sparepart->category)" />
                            <x-input-helper>{{ __('Contoh: Keyboard, Mouse, Monitor') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Lokasi Gudang -->
                        <div>
                            <x-input-label for="location" :value="__('Lokasi Gudang')" :required="true" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $sparepart->location)" />
                            <x-input-helper>{{ __('Contoh: Gudang A, Rak B-01') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Kondisi Barang -->
                        <div>
                            <x-input-label for="condition" :value="__('Kondisi Barang')" :required="true" />
                            <x-text-input id="condition" class="block mt-1 w-full" type="text" name="condition" :value="old('condition', $sparepart->condition)" />
                            <x-input-helper>{{ __('Contoh: Baru, Bekas, Diperbaiki') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                        </div>

                        <!-- Harga -->
                        <div>
                            <x-input-label for="price" :value="__('Harga')" :required="true" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $sparepart->price)" />
                            <x-input-helper>{{ __('Isi hanya angka, contoh: 1500000') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Stok Awal -->
                        <div>
                            <x-input-label for="stock" :value="__('Stok Awal')" :required="true" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock', $sparepart->stock)" />
                            <x-input-helper>{{ __('Isi hanya angka, contoh: 10') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status')" :required="true" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="aktif" {{ old('status', $sparepart->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $sparepart->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('superadmin.inventory.index') }}">
                            <x-button type="button" variant="secondary">
                                {{ __('Batal') }}
                            </x-button>
                        </a>

                        <x-button type="submit" variant="primary" class="ms-4">
                            {{ __('Simpan Perubahan') }}
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
