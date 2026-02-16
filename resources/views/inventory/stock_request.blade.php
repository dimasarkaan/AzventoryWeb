<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ui.request_stock_change_for') }} {{ $sparepart->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('inventory.stock.request.store', $sparepart) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Tipe Transaksi -->
                        <div>
                            <x-input-label for="type" :value="__('ui.transaction_type')" :required="true" />
                            @php
                                $typeOptions = [
                                    'masuk' => __('ui.stock_in_simple'),
                                    'keluar' => __('ui.stock_out_simple'),
                                ];
                            @endphp
                            <x-select name="type" :options="$typeOptions" :selected="old('type', 'masuk')" placeholder="{{ __('ui.select_transaction_type') }}" width="w-full" />
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <x-input-label for="quantity" :value="__('ui.quantity')" :required="true" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity')" min="1" />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- Alasan -->
                        <div>
                            <x-input-label for="reason" :value="__('ui.reason')" :required="true" />
                            <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" :value="old('reason')" />
                            <x-input-helper>{{ __('ui.reason_placeholder') }}</x-input-helper>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('inventory.show', $sparepart) }}">
                            <x-button type="button" variant="secondary">
                                {{ __('ui.cancel') }}
                            </x-button>
                        </a>

                        <x-button type="submit" variant="primary" class="ms-4">
                            {{ __('ui.submit_request') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
