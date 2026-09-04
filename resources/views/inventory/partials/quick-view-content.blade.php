<div class="space-y-6 pb-20">
    <!-- Image Header -->
    <div class="relative h-48 w-full rounded-xl overflow-hidden bg-secondary-100 flex items-center justify-center">
        @if($inventory->image)
            <img src="{{ asset('storage/' . $inventory->image) }}" alt="{{ $inventory->name }}" class="w-full h-full object-cover">
        @else
            <svg class="w-16 h-16 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        @endif
        
        <!-- Type Badge overlay -->
        <div class="absolute top-3 right-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold shadow-sm backdrop-blur-md {{ $inventory->type === 'sale' ? 'bg-green-500/90 text-white' : 'bg-blue-500/90 text-white' }}">
                {{ $inventory->type === 'sale' ? 'Barang Dijual' : 'Aset Kantor' }}
            </span>
        </div>
    </div>

    <!-- Title & Basic Info -->
    <div>
        <h3 class="text-xl font-bold text-secondary-900">{{ $inventory->name }}</h3>
        <p class="text-sm font-mono text-secondary-500 mt-1">{{ $inventory->part_number }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-secondary-50 p-3 rounded-lg border border-secondary-100">
            <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-1">Stok Saat Ini</p>
            <p class="text-xl font-bold {{ $inventory->stock <= $inventory->minimum_stock ? 'text-danger-600' : 'text-secondary-900' }}">
                {{ $inventory->stock }} <span class="text-sm font-normal text-secondary-500">{{ $inventory->unit }}</span>
            </p>
        </div>
        <div class="bg-secondary-50 p-3 rounded-lg border border-secondary-100">
            <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-1">Kondisi</p>
            <x-status-badge :status="$inventory->condition" type="pill" />
        </div>
        <div class="bg-secondary-50 p-3 rounded-lg border border-secondary-100">
            <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-1">Kategori</p>
            <p class="text-sm font-medium text-secondary-900 truncate">{{ $inventory->category->name ?? '-' }}</p>
        </div>
        <div class="bg-secondary-50 p-3 rounded-lg border border-secondary-100">
            <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-1">Lokasi</p>
            <p class="text-sm font-medium text-secondary-900 truncate">{{ $inventory->location->name ?? '-' }}</p>
        </div>
    </div>
    
    @if($inventory->type === 'sale')
    <!-- Price Info -->
    <div class="border-t border-secondary-100 pt-4">
        <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-1">Harga Jual</p>
        <p class="text-lg font-bold text-success-600">Rp {{ number_format($inventory->price, 0, ',', '.') }}</p>
    </div>
    @endif

    <!-- Notes / Chronology -->
    <div class="border-t border-secondary-100 pt-4">
        <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-2">Catatan / Kronologi</p>
        <p class="text-sm text-secondary-600 leading-relaxed">{{ $inventory->problem_chronology ?: 'Tidak ada catatan spesifik.' }}</p>
    </div>

    <!-- Action Button (Fixed Bottom) -->
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-white border-t border-secondary-100">
        <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-primary w-full flex items-center justify-center gap-2 py-3 shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            Buka Halaman Detail Penuh
        </a>
    </div>
</div>