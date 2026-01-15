<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Perubahan Stok untuk: ') }} {{ $sparepart->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('superadmin.inventory.stock.request.store', $sparepart) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Tipe Transaksi -->
                        <div>
                            <x-input-label for="type" :value="__('Tipe Transaksi')" :required="true" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="masuk">Stok Masuk</option>
                                <option value="keluar">Stok Keluar</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <x-input-label for="quantity" :value="__('Jumlah')" :required="true" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity')" min="1" />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- Alasan -->
                        <div>
                            <x-input-label for="reason" :value="__('Alasan')" :required="true" />
                            <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" :value="old('reason')" />
                            <x-input-helper>{{ __('Contoh: Restock bulanan, Dipinjam untuk perbaikan, Dijual') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('superadmin.inventory.show', $sparepart) }}">
                            <x-button type="button" variant="secondary">
                                {{ __('Batal') }}
                            </x-button>
                        </a>

                        <x-button type="submit" variant="primary" class="ms-4">
                            {{ __('Kirim Pengajuan') }}
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
