<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
             x-data="inventoryDetail()"
             x-init="console.log('Alpine Scope Initialized')"
             x-effect="document.body.style.overflow = (stockModalOpen || borrowModalOpen) ? 'hidden' : ''"
             @open-return-modal.window="initReturn($event.detail)"
        >
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                            {{ $sparepart->name }}
                        </h2>
                        <x-status-badge :status="$sparepart->status" type="pill" />
                    </div>
                    <div class="flex items-center gap-2 mt-1.5 text-secondary-500 font-mono text-sm">
                        <!-- Part number -->
                        <div x-data="{ copied: false }" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                            <span>{{ $sparepart->part_number }}</span>
                            <button @click="navigator.clipboard.writeText('{{ $sparepart->part_number }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                    class="p-1 rounded-md hover:bg-secondary-100 transition-colors text-secondary-400 hover:text-secondary-600 focus:outline-none"
                                    :title="copied ? 'Tersalin!' : 'Salin Part Number'">
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <svg x-show="copied" class="w-4 h-4 text-success-500" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                        
                        <span class="text-secondary-300">|</span>

                        <!-- Share URL -->
                        <div x-data="{ shared: false }" class="flex items-center">
                            <button @click="navigator.clipboard.writeText(window.location.href); shared = true; setTimeout(() => shared = false, 2000)" 
                                    class="flex items-center gap-1.5 px-2 py-1 rounded-md hover:bg-secondary-100 transition-colors text-secondary-400 hover:text-secondary-600 focus:outline-none"
                                    :title="shared ? 'Tautan disalin!' : 'Salin Tautan'">
                                <svg x-show="!shared" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <svg x-show="shared" class="w-4 h-4 text-success-500" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-xs hidden sm:inline" x-text="shared ? 'Tersalin' : 'Bagikan'"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary px-2 md:px-4">
                        <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span class="hidden md:inline">{{ __('ui.back') }}</span>
                    </a>
                    
                    @can('update', $sparepart)
                    <!-- Desktop Edit Button -->
                    <a href="{{ route('inventory.edit', $sparepart) }}" data-testid="btn-edit" class="btn btn-warning hidden md:flex">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        {{ __('ui.edit') }}
                    </a>

                    <!-- Mobile Dropdown -->
                    <div class="relative md:hidden" x-data="{ openMenu: false }">
                        <button @click="openMenu = !openMenu" class="btn btn-secondary px-2" aria-label="Opsi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                        <div x-show="openMenu" @click.away="openMenu = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-secondary-200 z-50">
                            <a href="{{ route('inventory.edit', $sparepart) }}" class="flex items-center gap-3 px-4 py-3 text-secondary-700 hover:bg-secondary-50 hover:text-warning-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                {{ __('ui.edit') }}
                            </a>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (Visual & Main Info) -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <!-- Image Card -->
                    <div class="card overflow-hidden" x-data="{ showLightbox: false }" x-effect="if(showLightbox) document.body.style.overflow = 'hidden'; else document.body.style.overflow = '';">
                        <div class="aspect-video w-full bg-secondary-100 flex items-center justify-center relative group cursor-pointer" @click="showLightbox = true">
                            @if($sparepart->image)
                                <img src="{{ asset('storage/' . $sparepart->image) }}" alt="{{ $sparepart->name }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                </div>
                                
                                <!-- Lightbox Modal -->
                                <template x-teleport="body">
                                    <div x-show="showLightbox" 
                                         @keydown.window.escape="showLightbox = false"
                                         x-transition.opacity.duration.300ms
                                         class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90" 
                                         x-cloak>
                                        <button @click="showLightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 focus:outline-none">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                        <img @click.away="showLightbox = false" src="{{ asset('storage/' . $sparepart->image) }}" alt="{{ $sparepart->name }}" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl">
                                    </div>
                                </template>
                            @else
                                <div class="text-secondary-400 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-sm">{{ __('ui.no_image') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Details Card -->
                    <div class="card p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">{{ __('ui.detail_info') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.category') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium flex items-center gap-2">
                                    <span class="p-1.5 bg-primary-50 text-primary-600 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                    </span>
                                    {{ $sparepart->category->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.brand') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->brand->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.color') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->color ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.storage_location') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium flex items-center gap-2">
                                     <span class="p-1.5 bg-warning-50 text-warning-600 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </span>
                                    {{ $sparepart->location->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.age') }}</span>
                                <div class="mt-1 text-secondary-900 font-medium">
                                    {{ $sparepart->age ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.condition') }}</span>
                                <div class="mt-1">
                                    @php
                                        $condition = $sparepart->condition ?? '-';
                                        $conditionColor = match(strtolower($condition)) {
                                            'baik' => 'text-success-700 bg-success-50 border-success-200',
                                            'rusak' => 'text-danger-700 bg-danger-50 border-danger-200',
                                            'hilang' => 'text-secondary-700 bg-secondary-100 border-secondary-200',
                                            default => 'text-secondary-700 bg-secondary-50 border-secondary-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border {{ $conditionColor }}">
                                        {{ ucfirst($condition) }}
                                    </span>
                                </div>
                            </div>
                            @if($sparepart->type === 'sale')
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.unit_price') }}</span>
                                <div class="mt-1 text-secondary-900 font-bold text-lg">
                                    Rp {{ number_format($sparepart->price, 0, ',', '.') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column (Stock & QR) -->
                <div class="flex flex-col gap-6">
                    <!-- Stock Card -->
                    <div class="card p-6 border-t-4 border-primary-500">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">{{ __('ui.available_stock') }}</span>
                                <div id="main-stock-display" class="mt-1 text-4xl font-extrabold text-secondary-900 transition-all duration-300" :class="{'text-primary-600 scale-105': liveUpdateShow}">
                                    <span x-text="liveStock">{{ $sparepart->stock }}</span>
                                    <span class="text-base font-medium text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                </div>
                            </div>
                            <div class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        
                        @if(strtolower($sparepart->condition) === 'baik')
                            <template x-if="liveStock <= {{ $sparepart->minimum_stock ?? 0 }}">
                                <div class="mt-4 bg-danger-50 text-danger-700 p-3 rounded-lg text-sm flex items-start gap-2 border border-danger-100">
                                     <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                     <div>
                                         <span class="font-bold block">{{ __('ui.status_critical') }}</span>
                                         Stok mencapai atau di bawah batas minimum (Min: {{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }}).
                                     </div>
                                </div>
                            </template>
                            <template x-if="liveStock > {{ $sparepart->minimum_stock ?? 0 }} && liveStock <= {{ ($sparepart->minimum_stock ?? 0) + 5 }}">
                                <div class="mt-4 bg-warning-50 text-warning-700 p-3 rounded-lg text-sm flex items-start gap-2 border border-warning-200">
                                     <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                     <div>
                                         <span class="font-bold block">{{ __('ui.approaching_stock') }}</span>
                                         Stok hampir mencapai batas minimum (Min: {{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }}).
                                     </div>
                                </div>
                            </template>
                            <template x-if="liveStock > {{ ($sparepart->minimum_stock ?? 0) + 5 }}">
                                 <div class="mt-4 bg-success-50 text-success-700 p-3 rounded-lg text-sm flex items-center gap-2 border border-success-100">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                     <span>{{ __('ui.stock_safe') }} (Min: {{ $sparepart->minimum_stock }} {{ $sparepart->unit ?? 'Pcs' }})</span>
                                </div>
                            </template>
                        @endif

                        <!-- Actions Wrapper -->
                        <div>
                            <div class="mt-4 pt-4 border-t border-secondary-100 grid grid-cols-1 gap-3">
                                @php
                                    $isOperator = auth()->user()->role === \App\Enums\UserRole::OPERATOR;
                                @endphp
                                @if($sparepart->type === 'asset')
                                    @if($sparepart->condition === 'Baik')
                                        <button @click="borrowModalOpen = true" type="button" data-testid="btn-borrow" class="btn btn-primary w-full justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            {{ __('ui.borrow_item') }}
                                        </button>
                                    @else
                                        <div class="bg-warning-50 text-warning-700 p-3 rounded-lg text-sm flex items-start gap-2 border border-warning-200">
                                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <div>
                                                <span class="font-bold block">{{ __('ui.cannot_borrow') }}</span>
                                                {{ __('ui.cannot_borrow_desc', ['condition' => $sparepart->condition]) }}
                                            </div>
                                        </div>
                                    @endif
                                    <button @click="stockModalOpen = true" type="button" data-testid="btn-update-stock" class="btn btn-secondary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        {{ $isOperator ? __('ui.request_stock_change') : __('ui.update_stock') }}
                                    </button>
                                @else
                                    <button @click="stockModalOpen = true" type="button" data-testid="btn-update-stock" class="btn btn-primary w-full justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        {{ $isOperator ? __('ui.request_stock_change') : __('ui.update_stock') }}
                                    </button>
                                @endif
                            </div>

                            @include('inventory.partials.stock-modal')

                            @include('inventory.partials.borrow-modal')
                        </div>

                    </div>

                    <!-- QR Code Card -->
                    <div class="card p-6 flex-1 flex flex-col h-full"> 
                        <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wider flex-none">{{ __('ui.qr_identification') }}</h3>
                        
                        <div class="flex-1 flex flex-col items-center justify-center min-h-[200px]" x-data="{ showQrLightbox: false }"> <!-- Centered Content area -->
                            @if ($sparepart->qr_code_path)
                                <div class="bg-white p-2 rounded-xl shadow-sm border border-secondary-100 mb-6 cursor-pointer group relative transition-transform duration-300 hover:scale-105 hover:shadow-md" @click="showQrLightbox = true" title="Perbesar QR Code">
                                     <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code" class="w-56 h-56">
                                     <div class="absolute inset-0 bg-secondary-900 bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center rounded-xl">
                                         <svg class="w-8 h-8 text-secondary-700 opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-sm bg-white/80 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                     </div>
                                </div>
                                
                                <!-- QR Lightbox Modal -->
                                <template x-teleport="body">
                                    <div x-show="showQrLightbox" 
                                         @keydown.window.escape="showQrLightbox = false"
                                         x-transition.opacity.duration.300ms
                                         class="fixed inset-0 z-[100] flex items-center justify-center bg-secondary-900/90 backdrop-blur-sm" 
                                         x-cloak>
                                        <button @click="showQrLightbox = false" class="absolute top-4 right-4 text-white/70 hover:text-white p-2 focus:outline-none transition-colors">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                        <div class="bg-white p-4 rounded-2xl shadow-2xl flex flex-col items-center" @click.away="showQrLightbox = false">
                                            <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code" class="max-w-[80vw] max-h-[80vh] object-contain">
                                            <p class="mt-4 text-secondary-900 font-bold font-mono text-xl">{{ $sparepart->part_number }}</p>
                                        </div>
                                    </div>
                                </template>

                                <div class="grid grid-cols-2 gap-3 w-full max-w-xs">
                                    <a href="{{ route('inventory.qr.download', $sparepart) }}" data-testid="btn-download-qr" class="btn btn-secondary justify-center text-sm py-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        {{ __('ui.download') }}
                                    </a>
                                     <a href="{{ route('inventory.qr.print', $sparepart) }}" target="_blank" class="btn btn-secondary justify-center text-sm py-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        {{ __('ui.print') }}
                                    </a>
                                </div>
                            @else
                                <div class="bg-secondary-50 w-full h-40 rounded-xl flex items-center justify-center text-secondary-400 mb-4">
                                    <span class="text-sm italic">{{ __('ui.no_qr') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-100 text-xs text-secondary-400 space-y-1 text-center flex-none">
                            <p>{{ __('ui.created_at') }}: {{ $sparepart->created_at->isoFormat('D MMMM Y HH:mm') }}</p>
                            <p>{{ __('ui.updated_at') }}: {{ $sparepart->updated_at->isoFormat('D MMMM Y HH:mm') }}</p>
                        </div>
                    </div>

                    <!-- Meta Info -->

                </div>

                <!-- Active Borrowings Section (Only for Assets) -->
                @if($sparepart->type === 'asset')
                <div class="col-span-1 lg:col-span-3">

                        <!-- History Card -->
                        <div id="activity-history-container" class="card p-6" x-data="{ searchQuery: '', filterStatus: 'all' }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 border-b border-secondary-100 pb-2 gap-4">
                                <h3 class="text-lg font-bold text-secondary-900">{{ __('ui.borrowing_history') }}</h3>
                                
                                <div class="flex items-center gap-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <input type="text" x-model="searchQuery" placeholder="Cari peminjam..." class="input-field pl-9 py-1.5 text-sm w-full sm:w-48 transition-all">
                                    </div>
                                    <select x-model="filterStatus" class="input-field py-1.5 pl-3 pr-8 text-sm cursor-pointer border-secondary-200">
                                        <option value="all">Semua Status</option>
                                        <option value="borrowed">Dipinjam</option>
                                        <option value="returned">Selesai</option>
                                    </select>
                                </div>
                            </div>

                            @if($borrowings->count() > 0)
                            <!-- Desktop Table -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-secondary-500 uppercase bg-secondary-50">
                                        <tr>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.borrower') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.quantity') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.borrow_date') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.expected_return_date') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap">{{ __('ui.status') }}</th>
                                            <th class="px-4 py-3 whitespace-nowrap text-right">{{ __('ui.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-secondary-100">

                                    @foreach($borrowings as $borrowing)
                                        @php
                                            $user = auth()->user();
                                            $isSuperAdmin = $user->role === \App\Enums\UserRole::SUPERADMIN;
                                            $isAdmin = $user->role === \App\Enums\UserRole::ADMIN;
                                            $borrowerRole = $borrowing->user->role ?? null;
                                            $isOwn = $borrowing->user_id === $user->id;

                                            // Visibility Logic
                                            // Admin can see operators (if borrower is operator)
                                            // SuperAdmin sees all
                                            $canView = $isSuperAdmin
                                                || ($isAdmin && ($isOwn || ($borrowerRole === \App\Enums\UserRole::OPERATOR)))
                                                || $isOwn;
                                        @endphp

                                        @if($canView)
                                        <tr class="hover:bg-primary-50 transition-colors group cursor-pointer" 
                                            onclick="window.location.href='{{ route('inventory.borrow.show', $borrowing) }}'"
                                            x-show="(filterStatus === 'all' || '{{ $borrowing->status }}' === filterStatus) && ('{{ strtolower(addslashes($borrowing->user->name ?? '')) }}'.includes(searchQuery.toLowerCase()))"
                                            x-transition>
                                            <td class="px-4 py-3 text-secondary-900 font-medium">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-secondary-200 overflow-hidden flex-shrink-0">
                                                        @if($borrowing->user && $borrowing->user->avatar)
                                                            <img src="{{ asset('storage/' . $borrowing->user->avatar) }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center text-secondary-500 text-xs font-bold">
                                                                {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span>{{ $borrowing->user->name ?? 'User Terhapus' }}</span>
                                                        <span class="text-xs text-secondary-500">{{ $borrowing->user->role ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-secondary-600">
                                                <div class="flex flex-col items-start gap-1">
                                                    <span class="font-bold text-secondary-900 bg-secondary-100 px-2 py-1 rounded-lg text-xs">
                                                        {{ $borrowing->quantity }} {{ $sparepart->unit }}
                                                    </span>
                                                    @if($borrowing->remaining_quantity < $borrowing->quantity)
                                                        <span class="text-xs text-secondary-500 font-normal">Sisa: {{ $borrowing->remaining_quantity }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-secondary-600">
                                                {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d F Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 text-secondary-600">
                                                {{ $borrowing->expected_return_at ? \Carbon\Carbon::parse($borrowing->expected_return_at)->translatedFormat('d F Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($borrowing->status === 'borrowed')
                                                    <span class="bg-warning-100 text-warning-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-warning-500"></span>
                                                        {{ __('ui.status_borrowed') }}
                                                    </span>
                                                @elseif($borrowing->status === 'returned')
                                                    <span class="bg-success-100 text-success-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-success-500"></span>
                                                        {{ __('ui.status_returned') }}
                                                    </span>
                                                @else
                                                    <span class="bg-danger-100 text-danger-800 text-xs font-bold px-2 py-1 rounded-lg">
                                                        {{ ucfirst($borrowing->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                @if(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->user()->can('update', $borrowing))
                                                    <!-- Return Button -->
                                                    <button
                                                        type="button"
                                                        @click.stop="$dispatch('open-return-modal', { maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                                                        class="bg-success-50 text-success-600 hover:bg-success-100 hover:text-success-700 font-bold py-1.5 px-3 rounded-lg text-xs transition-colors inline-flex items-center gap-1 group z-10 relative"
                                                    >
                                                        <span>{{ __('ui.return_item') }}</span>
                                                    </button>
                                                @else
                                                    <!-- View Evidence Button -->
                                                    @if($borrowing->return_evidence)

                                                    <button 
                                                        type="button"
                                                        @click.stop="
                                                            activeEvidence = {
                                                                image: '{{ asset('storage/' . $borrowing->return_evidence) }}',
                                                                notes: '{{ addslashes($borrowing->return_notes ?? '-') }}',
                                                                date: '{{ $borrowing->actual_return_date ? \Carbon\Carbon::parse($borrowing->actual_return_date)->translatedFormat('d F Y H:i') : '-' }}',
                                                                condition: '{{ $borrowing->return_condition ?? 'Baik' }}'
                                                            };
                                                            evidenceModalOpen = true;
                                                        "
                                                        class="text-secondary-400 hover:text-primary-600 transition-colors tooltip-trigger z-10 relative"
                                                        title="{{ __('ui.view_return_evidence') }}"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </button>
                                                    @endif
                                                @endif

                                                @if($borrowing->returns->count() > 0 || !(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->user()->can('update', $borrowing)))
                                                    <a 
                                                        href="{{ route('inventory.borrow.show', $borrowing) }}"
                                                        @click.stop
                                                        class="mt-2 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 font-bold py-1.5 px-3 rounded-lg text-xs transition-colors inline-flex items-center gap-1"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ __('ui.history') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="md:hidden space-y-4">
                                @foreach($borrowings as $borrowing)
                                    @php
                                        $user = auth()->user();
                                        $isSuperAdmin = $user->role === \App\Enums\UserRole::SUPERADMIN;
                                        $isAdmin = $user->role === \App\Enums\UserRole::ADMIN;
                                        $borrowerRole = $borrowing->user->role ?? null;
                                        $isOwn = $borrowing->user_id === $user->id;

                                        $canView = $isSuperAdmin
                                            || ($isAdmin && ($isOwn || ($borrowerRole === \App\Enums\UserRole::OPERATOR)))
                                            || $isOwn;
                                    @endphp

                                    @if($canView)
                                    <div class="bg-white border border-secondary-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all cursor-pointer" 
                                         onclick="window.location.href='{{ route('inventory.borrow.show', $borrowing) }}'"
                                         x-show="(filterStatus === 'all' || '{{ $borrowing->status }}' === filterStatus) && ('{{ strtolower(addslashes($borrowing->user->name ?? '')) }}'.includes(searchQuery.toLowerCase()))"
                                         x-transition>
                                        <!-- Header: User & Status -->
                                        <div class="flex items-start justify-between mb-3 border-b border-gray-100 pb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-secondary-200 overflow-hidden flex-shrink-0">
                                                    @if($borrowing->user && $borrowing->user->avatar)
                                                        <img src="{{ asset('storage/' . $borrowing->user->avatar) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-secondary-500 text-xs font-bold">
                                                            {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-secondary-900">{{ $borrowing->user->name ?? 'User Terhapus' }}</h4>
                                                    <span class="text-xs text-secondary-500 block">{{ $borrowing->user->role ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                 @if($borrowing->status === 'borrowed')
                                                    <span class="bg-warning-100 text-warning-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>
                                                        {{ __('ui.status_borrowed') }}
                                                    </span>
                                                @elseif($borrowing->status === 'returned')
                                                    <span class="bg-success-100 text-success-800 text-xs font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>
                                                        {{ __('ui.status_returned') }}
                                                    </span>
                                                @else
                                                    <span class="bg-danger-100 text-danger-800 text-xs font-bold px-2 py-1 rounded-lg">
                                                        {{ ucfirst($borrowing->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Body: Details Grid -->
                                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4">
                                            <div>
                                                <span class="text-[10px] text-secondary-400 block uppercase tracking-wider font-semibold">{{ __('ui.quantity') }}</span>
                                                <div class="font-medium text-secondary-900 flex items-center gap-1">
                                                    {{ $borrowing->quantity }} {{ $sparepart->unit }}
                                                    @if($borrowing->remaining_quantity < $borrowing->quantity)
                                                        <span class="text-xs text-secondary-500 font-normal bg-secondary-100 px-1.5 rounded">(Sisa: {{ $borrowing->remaining_quantity }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-secondary-400 block uppercase tracking-wider font-semibold">{{ __('ui.borrow_date') }}</span>
                                                <span class="font-medium text-secondary-900">{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] text-secondary-400 block uppercase tracking-wider font-semibold">{{ __('ui.expected_return_date') }}</span>
                                                <span class="font-medium text-secondary-900">{{ $borrowing->expected_return_at ? \Carbon\Carbon::parse($borrowing->expected_return_at)->translatedFormat('d F Y') : '-' }}</span>
                                            </div>
                                        </div>

                                        <!-- Footer: Actions -->
                                        <div class="flex items-center justify-end border-t border-gray-100 pt-3 gap-3">
                                            @if(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->user()->can('update', $borrowing))
                                                <button
                                                    type="button"
                                                    @click.stop="$dispatch('open-return-modal', { maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                                                    class="bg-success-50 text-success-700 hover:bg-success-100 font-bold py-2 px-4 rounded-lg text-sm w-full text-center transition-colors"
                                                >
                                                    {{ __('ui.return_item') }}
                                                </button>
                                            @else
                                                 <!-- View Evidence Button (Mobile) -->
                                                @if($borrowing->return_evidence)
                                                <button 
                                                    type="button"
                                                    @click.stop="
                                                        activeEvidence = {
                                                            image: '{{ asset('storage/' . $borrowing->return_evidence) }}',
                                                            notes: '{{ addslashes($borrowing->return_notes ?? '-') }}',
                                                            date: '{{ $borrowing->actual_return_date ? \Carbon\Carbon::parse($borrowing->actual_return_date)->translatedFormat('d F Y H:i') : '-' }}',
                                                            condition: '{{ $borrowing->return_condition ?? 'Baik' }}'
                                                        };
                                                        evidenceModalOpen = true;
                                                    "
                                                    class="text-secondary-500 hover:text-primary-600 transition-colors flex items-center gap-1 text-sm bg-gray-50 px-3 py-2 rounded-lg"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <span>{{ __('ui.evidence') }}</span>
                                                </button>
                                                @endif

                                                @if($borrowing->returns->count() > 0 || !(($borrowing->status === 'borrowed' || $borrowing->remaining_quantity > 0) && auth()->id() === $borrowing->user_id))
                                                <a 
                                                    href="{{ route('inventory.borrow.show', $borrowing) }}"
                                                    @click.stop
                                                    class="bg-secondary-100 text-secondary-700 hover:bg-secondary-200 font-bold py-2 px-4 rounded-lg text-sm w-full text-center transition-colors flex items-center justify-center gap-2"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ __('ui.history') }}
                                                </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-4 px-4">
                                {{ $borrowings->links() }}
                            </div>
                            @else
                                <div class="text-center py-8 text-secondary-400 bg-secondary-50 rounded-lg border border-dashed border-secondary-200">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm">{{ __('ui.no_borrowing_history') }}</p>
                                </div>
                            @endif
                        </div>
                </div>
                @endif
            
                <!-- Similar Items Section -->
                <div class="col-span-1 lg:col-span-3">
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2 flex items-center justify-between">
                            <span>{{ __('ui.similar_items') }}</span>
                            @if(isset($similarItems) && $similarItems->count() > 0)
                                <span class="text-xs font-normal text-secondary-500 bg-secondary-100 px-2 py-1 rounded-full">{{ $similarItems->total() }} {{ __('ui.items_found') }}</span>
                            @endif
                        </h3>
                        
                        @if(isset($similarItems) && $similarItems->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($similarItems as $item)
                            <a href="{{ route('inventory.show', $item) }}" class="group block border border-secondary-200 rounded-xl hover:border-primary-500 hover:shadow-md transition-all duration-200 bg-white overflow-hidden">
                                <div class="flex items-start p-4 gap-4">
                                    <!-- Thumbnail -->
                                    <div class="flex flex-col gap-2 w-20 flex-shrink-0">
                                        <div class="w-20 h-20 bg-secondary-100 rounded-lg overflow-hidden relative">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="flex items-center justify-center h-full text-secondary-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="h-1 w-full rounded-full {{ $item->type === 'sale' ? 'bg-success-600' : 'bg-primary-600' }}"></div>
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-secondary-900 truncate group-hover:text-primary-600 transition-colors">{{ $item->name }}</h4>
                                        <p class="text-xs text-secondary-500 mb-2 truncate">{{ $item->brand->name ?? __('ui.no_brand') }} â€¢ {{ $item->category->name ?? '-' }}</p>
                                        
                                        <div class="grid grid-cols-2 gap-y-1 gap-x-2 text-xs">
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.color') }}</span>
                                                <span class="font-medium text-secondary-700">{{ $item->color ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.condition') }}</span>
                                                <span class="font-medium text-secondary-700">{{ $item->condition }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.location') }}</span>
                                                <span class="font-medium text-secondary-700 truncate">{{ $item->location->name ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary-400 block text-[10px] uppercase">{{ __('ui.stock') }}</span>
                                                <span class="font-bold {{ $item->stock <= ($item->minimum_stock ?? 0) ? 'text-danger-600' : 'text-success-600' }}">
                                                    {{ $item->stock }} {{ $item->unit ?? 'Pcs' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $similarItems->links() }}
                        </div>
                        @else
                            <div class="text-center py-8 text-secondary-400 bg-secondary-50 rounded-lg border border-dashed border-secondary-200">
                                <svg class="w-10 h-10 mx-auto mb-2 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                    <p class="text-sm">{{ __('ui.no_similar_items') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @include('inventory.partials.return-modal')
            @include('inventory.partials.evidence-modal')

    </div>
    </div>
    @include('inventory.partials.alpine_script')
</x-app-layout>


