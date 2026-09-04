<x-app-layout>
    <div class="py-6" x-data="inventoryDetail()" @open-return-modal.window="initReturn($event.detail)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                            Detail Riwayat Peminjaman
                        </h2>
                        @if($borrowing->status === 'borrowed')
                            <span class="badge badge-warning">Dipinjam</span>
                        @elseif($borrowing->status === 'returned')
                            <span class="badge badge-success">Selesai</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($borrowing->status) }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-1.5 text-secondary-500 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Dipinjam: {{ $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-' }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('inventory.show', $borrowing->sparepart) }}" class="btn btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Detail Barang
                    </a>
                </div>
            </div>

            <!-- Overdue Banner Alert -->
            @php
                $isOverdue = false;
                $daysOverdue = 0;
                if ($borrowing->status === 'borrowed' && $borrowing->expected_return_at) {
                    $expectedDate = \Carbon\Carbon::parse($borrowing->expected_return_at);
                    if ($expectedDate->isPast() && !$expectedDate->isToday()) {
                        $isOverdue = true;
                        $daysOverdue = $expectedDate->diffInDays(now());
                    }
                }
            @endphp
            @if($isOverdue)
            <div class="mb-6 p-4 rounded-2xl bg-danger-50 border border-danger-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-danger-100 text-danger-600 rounded-full flex-shrink-0">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-danger-800 font-bold text-lg">Peringatan Keterlambatan!</h3>
                        <p class="text-danger-600 text-sm mt-0.5">Barang ini sudah melewati batas waktu pengembalian selama <strong class="text-danger-700">{{ $daysOverdue }} hari</strong>.</p>
                    </div>
                </div>
                @if($borrowing->remaining_quantity > 0)
                <button @click="initReturn({ maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })" class="btn btn-danger whitespace-nowrap shadow-md hover:shadow-lg w-full sm:w-auto flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    Kembalikan Segera
                </button>
                @endif
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Borrowing Summary -->
                <div class="lg:col-span-1 flex flex-col gap-6 lg:sticky lg:top-6 self-start max-h-[calc(100vh-2rem)] overflow-y-auto pr-2 pb-4 scrollbar-thin scrollbar-thumb-secondary-200 hover:scrollbar-thumb-secondary-300">
                    <!-- Borrower Info Card -->
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Informasi Peminjam</h3>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 rounded-full bg-secondary-200 overflow-hidden flex-shrink-0">
                                @if($borrowing->user && $borrowing->user->avatar)
                                    <img src="{{ asset('storage/' . $borrowing->user->avatar) }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $borrowing->user->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-secondary-500 text-xl font-bold">
                                        {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg font-semibold text-secondary-900">{{ $borrowing->user->name ?? 'User Terhapus' }}</span>
                                <span class="text-sm text-secondary-500">{{ $borrowing->user->role ?? '-' }}</span>
                                @if($borrowing->user && $borrowing->user->email)
                                    <span class="text-xs text-secondary-400 mt-1">{{ $borrowing->user->email }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Borrowing Details Card -->
                    <div class="card p-6" id="borrowing-details">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Detail Peminjaman</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Barang</span>
                                <p class="text-base text-secondary-900 font-medium mt-1">{{ $borrowing->sparepart->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-secondary-500 font-mono" x-data="{ copied: false }">
                                    <span>{{ $borrowing->sparepart->part_number }}</span>
                                    <button @click="navigator.clipboard.writeText('{{ $borrowing->sparepart->part_number }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                            class="p-0.5 rounded hover:bg-secondary-100 transition-colors text-secondary-400 hover:text-secondary-600 focus:outline-none"
                                            :title="copied ? 'Tersalin!' : 'Salin Part Number'">
                                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <svg x-show="copied" class="w-3.5 h-3.5 text-success-500" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Total Dipinjam</span>
                                <p class="text-base text-secondary-900 font-bold mt-1">{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</p>
                            </div>

                            @if($borrowing->remaining_quantity !== null)
                                <div>
                                    <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Sisa Belum Dikembalikan</span>
                                    <p class="text-base {{ $borrowing->remaining_quantity > 0 ? 'text-warning-600' : 'text-success-600' }} font-bold mt-1">
                                        {{ $borrowing->remaining_quantity }} {{ $borrowing->sparepart->unit }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Tanggal Pinjam</span>
                                <p class="text-base text-secondary-900 font-medium mt-1">
                                    {{ $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-' }}
                                </p>
                            </div>

                            @if($borrowing->expected_return_at)
                                <div>
                                    <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Estimasi Kembali</span>
                                    <p class="text-base text-secondary-900 font-medium mt-1">
                                        {{ \Carbon\Carbon::parse($borrowing->expected_return_at)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            @endif

                            @if($borrowing->notes)
                                <div>
                                    <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Catatan</span>
                                    <p class="text-sm text-secondary-600 mt-1 break-words whitespace-normal">{{ $borrowing->notes }}</p>
                                </div>
                            @endif
                        </div>

                        @if($borrowing->remaining_quantity > 0)
                            <div class="mt-6 pt-4 border-t border-secondary-100">
                                <button
                                    @click="initReturn({ maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                                    class="btn btn-primary w-full justify-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    Kembalikan Barang
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Returns Timeline -->
                <div class="lg:col-span-2">
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Riwayat Pengembalian</h3>
                        
                        @if($borrowing->returns->isEmpty())
                            <!-- Empty State -->
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-20 h-20 rounded-full bg-secondary-100 flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-secondary-700 mb-2">Belum Ada Pengembalian</p>
                                <p class="text-sm text-secondary-500 max-w-md">
                                    Barang masih dalam status dipinjam. Riwayat pengembalian akan muncul setelah ada pengembalian pertama.
                                </p>
                            </div>
                        @else
                            <!-- Timeline -->
                            <div class="space-y-6">
                                @foreach($borrowing->returns as $return)
                                    <div class="relative pl-8 pb-6 border-l-2 border-secondary-200 last:pb-0 last:border-l-0">
                                        <!-- Timeline Dot -->
                                        <div class="absolute -left-2 top-0 w-4 h-4 rounded-full"
                                             :class="getItemColor('{{ $return->condition }}')">
                                        </div>

                                        <!-- Return Card -->
                                        <div class="bg-secondary-50 rounded-lg p-4 ml-2">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-secondary-900 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $return->return_date ? $return->return_date->translatedFormat('d F Y H:i') : '-' }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                                      :class="getBadgeColor('{{ $return->condition }}')">
                                                    @if($return->condition === 'good') Baik
                                                    @elseif($return->condition === 'bad') Rusak
                                                    @elseif($return->condition === 'lost') Hilang
                                                    @else {{ ucfirst($return->condition) }}
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                <div>
                                                    <span class="text-xs text-secondary-500 font-semibold">Jumlah Dikembalikan</span>
                                                    <p class="text-lg font-bold text-secondary-900">{{ $return->quantity }} {{ $borrowing->sparepart->unit }}</p>
                                                </div>
                                            </div>

                                            @if($return->notes)
                                                <div class="mb-3">
                                                    <span class="text-xs text-secondary-500 font-semibold">Catatan</span>
                                                    <p class="text-sm text-secondary-700 mt-1 break-words whitespace-normal">{{ $return->notes }}</p>
                                                </div>
                                            @endif

                                            <!-- Photos Gallery -->
                                            @if($return->photos && count($return->photos) > 0)
                                                <div x-data="{ showLightbox: false, lightboxSrc: '' }">
                                                    <span class="text-xs text-secondary-500 font-semibold mb-2 block">Foto Bukti</span>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                        @foreach($return->photos as $photo)
                                                            <button type="button" @click.stop="lightboxSrc = '{{ asset('storage/' . $photo) }}'; showLightbox = true" class="group relative aspect-square rounded-lg overflow-hidden bg-secondary-200 hover:ring-2 hover:ring-primary-500 transition-all focus:outline-none text-left">
                                                                <img src="{{ asset('storage/' . $photo) }}" alt="Return Evidence" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                                    </svg>
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <!-- Lightbox Modal -->
                                                    <template x-teleport="body">
                                                        <div x-show="showLightbox" 
                                                             x-transition.opacity.duration.300ms
                                                             class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90" 
                                                             style="display: none;">
                                                            <button @click.stop="showLightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 focus:outline-none z-[101]">
                                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                            <img @click.away="showLightbox = false" :src="lightboxSrc" alt="Return Evidence" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl relative z-[100]">
                                                        </div>
                                                    </template>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Sticky Return Action -->
        @if($borrowing->remaining_quantity > 0)
        <div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 bg-white/90 backdrop-blur-md border-t border-secondary-200 z-40 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] pb-[calc(1rem+env(safe-area-inset-bottom))]">
            <button
                @click="initReturn({ maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                class="btn btn-primary w-full justify-center shadow-lg py-3.5 text-base"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                Kembalikan Barang
            </button>
        </div>
        @endif

    @include('inventory.borrow.partials.return-modal')

    @include('inventory.partials.alpine_script')
    </div>
    @push('styles')
    <style>
        @keyframes highlight-fade {
            0% { background-color: rgba(239, 68, 68, 0.2); ring: 2px solid #ef4444; }
            100% { background-color: transparent; ring: none; }
        }
        .highlight-overdue {
            animation: highlight-fade 3s ease-in-out forwards;
            border-color: #ef4444 !important;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('highlight') === 'overdue') {
                const target = document.getElementById('borrowing-details');
                if (target) {
                    target.classList.add('highlight-overdue');
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

