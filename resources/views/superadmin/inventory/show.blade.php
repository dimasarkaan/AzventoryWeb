<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Sparepart: ') }} {{ $sparepart->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Informasi Umum</h3>
                        <div class="mt-4 space-y-4">
                            <p><strong>Nama Sparepart:</strong> {{ $sparepart->name }}</p>
                            <p><strong>Part Number (PN):</strong> {{ $sparepart->part_number }}</p>
                            <p><strong>Kategori:</strong> {{ $sparepart->category }}</p>
                            <p><strong>Lokasi Gudang:</strong> {{ $sparepart->location }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Detail Stok & Kondisi</h3>
                        <div class="mt-4 space-y-4">
                            <p><strong>Kondisi:</strong> {{ $sparepart->condition }}</p>
                            <p><strong>Harga:</strong> Rp {{ number_format($sparepart->price, 2, ',', '.') }}</p>
                            <p><strong>Stok:</strong> {{ $sparepart->stock }}</p>
                            <p><strong>Status:</strong> 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sparepart->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sparepart->status }}
                                </span>
                            </p>
                        </div>
                    </div>
                     <div>
                        <h3 class="text-lg font-medium text-gray-900">Informasi Tambahan</h3>
                        <div class="mt-4 space-y-4">
                            <p><strong>Tanggal Dibuat:</strong> {{ $sparepart->created_at->format('d F Y H:i') }}</p>
                            <p><strong>Tanggal Diperbarui:</strong> {{ $sparepart->updated_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 md:mt-0">
                        <h3 class="text-lg font-medium text-gray-900">QR Code</h3>
                        <div class="mt-4">
                            @if ($sparepart->qr_code_path)
                                <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code for {{ $sparepart->name }}" class="w-48 h-48 border p-1">
                                <div class="mt-2 flex">
                                    <a href="{{ route('superadmin.inventory.qr.download', $sparepart) }}">
                                        <x-button type="button" variant="secondary">
                                            {{ __('Unduh') }}
                                        </x-button>
                                    </a>
                                    <a href="{{ route('superadmin.inventory.qr.print', $sparepart) }}" target="_blank" class="ms-2">
                                        <x-button type="button" variant="secondary">
                                            {{ __('Cetak') }}
                                        </x-button>
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-500">QR Code belum dibuat.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <div>
                        <a href="{{ route('superadmin.inventory.stock.request.create', $sparepart) }}">
                            <x-button type="button" variant="primary">
                                {{ __('Ajukan Perubahan Stok') }}
                            </x-button>
                        </a>
                    </div>
                    <a href="{{ route('superadmin.inventory.index') }}">
                        <x-button variant="secondary">
                            {{ __('Kembali') }}
                        </x-button>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
