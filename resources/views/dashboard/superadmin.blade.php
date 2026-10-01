<x-app-layout>
    @include('dashboard.partials.dashboard-styles')
@include('dashboard._superadmin_scripts')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
             x-data="dashboardData()">
             
            @include('dashboard._location_modal')
            @include('dashboard._category_modal')
            @include('dashboard._brand_modal')
            @include('dashboard._activity_modal')
             
            {{-- ================================================================
                 HEADER KHUSUS CETAK & EXPORT (MUNCUL DI SETIAP HALAMAN)
                 ================================================================ --}}
            <table class="w-full print-container">
                <thead class="hidden export-show pb-4 border-b-2 border-primary-900 mb-6">
                    <tr><td>
                        <div class="flex items-start justify-between w-full">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('images/logo/logo_azzahracomputer.png') }}" class="h-12 w-auto" alt="Logo Azzahra">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 uppercase">AZZAHRA COMPUTER</h1>
                                    <p class="text-sm text-gray-500">{!! __('ui.official_inventory_report') !!}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <h2 class="text-xl font-bold text-primary-900">{{ __('ui.dashboard_report') }}</h2>
                                <p class="text-sm text-gray-600 mt-1">{{ __('ui.printed_at') }} {{ now()->translatedFormat('d F Y, H:i') }}</p>
                                <p class="text-sm text-gray-600">{{ __('ui.printed_by_full') }} {{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    </td></tr>
                </thead>

                <tbody>
                    <tr><td class="print:pt-8">

            {{-- ================================================================
                 HEADER DASHBOARD
                 Baris 1: Judul + Tombol Pengaturan & Approvals
                 Baris 2: Tab Filter Periode Global (Opsi F)
                 ================================================================ --}}
            <div class="mb-6 export-hide print:hidden">
                {{-- Baris 1 --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">{{ __('ui.dashboard') }}</h1>
                        <p class="mt-1 text-sm text-secondary-500">{{ __('ui.dashboard_desc') }}</p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Tombol Pengaturan Widget --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="btn btn-secondary flex items-center gap-2 text-sm"
                                    aria-label="{{ __('ui.display_settings') }}"
                                    aria-expanded="false"
                                    :aria-expanded="open.toString()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="hidden sm:inline">{{ __('ui.display_settings') }}</span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-1 z-50 border border-secondary-100 max-h-[80vh] overflow-y-auto">
                                <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">{{ __('ui.active_widgets') }}</div>
                                <label for="setting_showStats" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showStats" name="showStats" x-model="showStats" @change="toggle('showStats')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_main_stats') }}</span>
                                </label>
                                <label for="setting_showCharts" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showCharts" name="showCharts" x-model="showCharts" @change="toggle('showCharts')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_distribution_location') }}</span>
                                </label>
                                <label for="setting_showLowStock" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showLowStock" name="showLowStock" x-model="showLowStock" @change="toggle('showLowStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_stock_alerts') }}</span>
                                </label>
                                <label for="setting_showOverdue" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showOverdue" name="showOverdue" x-model="showOverdue" @change="toggle('showOverdue')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_overdue') }}</span>
                                </label>
                                <label for="setting_showNoPriceItems" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showNoPriceItems" name="showNoPriceItems" x-model="showNoPriceItems" @change="toggle('showNoPriceItems')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_missing_price') }}</span>
                                </label>
                                <div class="border-t border-secondary-100 my-1"></div>
                                <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">{{ __('ui.widget_analytics') }}</div>
                                <label for="setting_showMovement" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showMovement" name="showMovement" x-model="showMovement" @change="toggle('showMovement')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_stock_movement') }}</span>
                                </label>
                                <label for="setting_showTopItems" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showTopItems" name="showTopItems" x-model="showTopItems" @change="toggle('showTopItems')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_popular_items') }}</span>
                                </label>
                                <label for="setting_showDeadStock" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showDeadStock" name="showDeadStock" x-model="showDeadStock" @change="toggle('showDeadStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_dead_stock') }}</span>
                                </label>
                                <label for="setting_showLeaderboard" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showLeaderboard" name="showLeaderboard" x-model="showLeaderboard" @change="toggle('showLeaderboard')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_top_contributors') }}</span>
                                </label>
                                <label for="setting_showRecent" class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" id="setting_showRecent" name="showRecent" x-model="showRecent" @change="toggle('showRecent')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_recent_activity') }}</span>
                                </label>
                                {{-- Tombol Reset ke Default --}}
                                <div class="border-t border-secondary-100 my-1"></div>
                                <div class="px-4 py-2">
                                    <button @click="resetWidgets()" class="w-full text-xs text-center text-secondary-500 hover:text-danger-600 transition-colors py-1 rounded hover:bg-danger-50">
                                        {{ __('ui.reset_default_view') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Ekspor --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="btn btn-secondary flex items-center gap-2 text-sm"
                                    aria-label="{{ __('ui.export_report') }}"
                                    aria-expanded="false"
                                    :aria-expanded="open.toString()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span class="hidden sm:inline">{{ __('ui.export_report') }}</span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl py-1 z-50 border border-secondary-100">
                                <button onclick="exportDashboardPDF()" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    {{ __('ui.print_pdf') }}
                                </button>
                                <button onclick="exportDashboardPNG()" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ __('ui.save_png') }}
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ========================================================
                     Baris 2: Tab Filter Periode Global (Opsi F)
                     Tab ini mengirim GET request ke URL yang sama + ?period=...
                     ======================================================== --}}
                @php
                    $activePeriod = $period ?? 'today';
                    $tabDefs = [
                        'today'      => __('ui.today'),
                        'this_week'  => __('ui.this_week'),
                        'this_month' => __('ui.this_month'),
                        'this_year'  => __('ui.this_year'),
                    ];
                @endphp
                {{-- Sticky wrapper: tab period menempel di atas saat scroll mobile --}}
                {{-- Sticky wrapper removed as requested --}}
                <div x-data="globalPeriodFilter()" class="flex flex-col gap-2">
                    {{-- Tab Row --}}
                    <div class="flex flex-wrap items-center gap-1 bg-secondary-100/60 rounded-xl p-1.5">
                        @foreach($tabDefs as $key => $label)
                            <a href="{{ route('dashboard.superadmin', ['period' => $key]) }}"
                               onclick="savePeriod('{{ $key }}')"
                               class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap
                                      {{ $activePeriod === $key
                                          ? 'bg-white text-primary-700 shadow-sm font-semibold ring-1 ring-secondary-200'
                                          : 'text-secondary-600 hover:text-secondary-900 hover:bg-white/60' }}">
                                {{ $label }}
                            </a>
                        @endforeach

                        {{-- Custom tab dengan SVG icon 1 warna --}}
                        <button @click="showCustom = !showCustom"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap
                                       {{ in_array($activePeriod, ['custom','custom_year'])
                                           ? 'bg-white text-primary-700 shadow-sm font-semibold ring-1 ring-secondary-200'
                                           : 'text-secondary-600 hover:text-secondary-900 hover:bg-white/60' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ __('ui.custom') }}</span>
                            @if(in_array($activePeriod, ['custom','custom_year']) && $year)
                                <span class="text-xs text-secondary-500">
                                    ({{ $year }}{{ isset($month) && $month !== 'all' ? '/' . str_pad($month,2,'0',STR_PAD_LEFT) : '' }})
                                </span>
                            @endif
                        </button>

                        {{-- Indikator rentang tanggal aktif --}}
                        <span class="ml-auto text-xs text-secondary-400 hidden sm:block">
                            {{ __('ui.data_range') }} {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                        </span>
                    </div>

                    {{-- Panel Custom dengan dropdown Alpine kustom --}}
                    <div x-show="showCustom"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="mt-4">
                        <form method="GET" action="{{ route('dashboard.superadmin') }}"
                              x-data="{ isSubmitting: false }" 
                              @submit.prevent="isSubmitting = true; isLoading = true; await fetchDashboardData($event.target); isSubmitting = false;"
                              class="bg-white border border-secondary-100 rounded-[24px] p-5 shadow-xl shadow-secondary-900/5 flex flex-col md:flex-row items-stretch md:items-end gap-6 transition-all" novalidate>
                            <input type="hidden" name="period" id="superadmin_period_input" value="custom">

                            {{-- Form Group: Date Range --}}
                            <div class="flex-grow space-y-3">
                                <div class="flex items-center justify-between px-1">
                                    <label for="date_range_picker" class="text-[11px] font-extrabold text-secondary-400 uppercase tracking-[0.1em]">{{ __('ui.date_range') }}</label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="setPickerRange(0)" class="text-[10px] font-bold text-secondary-500 hover:text-primary-600 transition-colors bg-secondary-50 px-2 py-0.5 rounded-md hover:bg-primary-50">{{ mb_strtoupper(__('ui.today')) }}</button>
                                        <button type="button" onclick="setPickerRange(7)" class="text-[10px] font-bold text-secondary-500 hover:text-primary-600 transition-colors bg-secondary-50 px-2 py-0.5 rounded-md hover:bg-primary-50">{{ __('ui.last_7_days') }}</button>
                                        <button type="button" onclick="setPickerRange(30)" class="text-[10px] font-bold text-secondary-500 hover:text-primary-600 transition-colors bg-secondary-50 px-2 py-0.5 rounded-md hover:bg-primary-50">{{ __('ui.last_30_days') }}</button>
                                    </div>
                                </div>
                                
                                <div class="relative group flatpickr-range-container">
                                    <input type="text" id="date_range_picker_hidden" name="date_range"
                                           class="range-picker-input w-full pl-12 pr-4 py-3 text-sm bg-secondary-50/50 border-secondary-200 rounded-2xl text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all cursor-pointer font-semibold placeholder:text-secondary-400"
                                           placeholder="{{ __('ui.select_date_range') }}">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-primary-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="hidden" name="start_date" id="start_date" class="range-start" value="{{ $start->format('Y-m-d') }}">
                                    <input type="hidden" name="end_date" id="end_date" class="range-end" value="{{ $end->format('Y-m-d') }}">
                                </div>
                            </div>

                            {{-- Form Group: Actions --}}
                            <div class="flex items-center gap-3 pt-2 md:pt-0">
                                <button type="submit" class="flex-grow md:flex-none btn btn-primary px-8 h-[44px] rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-primary-500/20 active:scale-95 transition-transform" :disabled="isSubmitting" :class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        <span class="font-bold tracking-wide">{{ __('ui.apply') }}</span>
                                    </span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2 font-bold tracking-wide" x-cloak>
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        {{ __('ui.processing') }}
                                    </span>
                                </button>
                                <button type="button" 
                                        onclick="resetCustomPicker()"
                                        class="btn btn-secondary h-[44px] px-6 rounded-xl border-secondary-200 hover:bg-secondary-50 font-bold active:scale-95 transition-transform flex items-center justify-center">{{ __('ui.reset') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ====================================================================
                 Mobile Quick Summary Bar (hanya tampil di layar < md)
                 Ringkasan 1 baris di atas stat cards â€” above the fold
                 ==================================================================== --}}
            {{-- Mobile Quick Summary removed as requested --}}

            <!-- Bagian Ikhtisar Statistik -->
            <!-- Loading Skeleton -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-6 animate-pulse print:hidden">
                @for($i = 0; $i < 5; $i++)
                    <div class="card p-6 flex flex-col justify-between h-40">
                        <div class="flex justify-between items-start">
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="h-10 w-10 bg-gray-200 rounded-bl-full -mr-6 -mt-6"></div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-200">000</div>
                        <div class="mt-4 flex items-center">
                            <div class="p-2 bg-gray-100 rounded-lg w-9 h-9"></div>
                            <div class="ml-2 h-4 bg-gray-100 rounded w-16"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Konten Asli -->
            <div x-show="showStats && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 print:grid-cols-6 gap-6 mb-6 print:break-inside-avoid">
                
                                <!-- 1. Total Sparepart -->
                <x-dashboard.stat-card 
                    title="Total<br>Barang" 
                    value="{{ $totalSpareparts }}" 
                    badgeText="{{ __('ui.sku_items') }}" 
                    color="primary"
                    onClick="window.location.href='{{ route('inventory.index') }}'">
                    <x-slot name="icon">
                        <x-icon.inventory class="w-5 h-5" />
                    </x-slot>
                </x-dashboard.stat-card>

                                <!-- 2. Total Stok -->
                <x-dashboard.stat-card 
                    title="Total Stok<br>Fisik" 
                    value="{{ $totalStock }}" 
                    badgeText="{{ __('ui.units') }}" 
                    color="success"
                    onClick="document.getElementById('stockByCategoryChart').scrollIntoView({behavior: 'smooth', block: 'center'})">
                    <x-slot name="icon">
                        <x-icon.package class="w-5 h-5" />
                    </x-slot>
                </x-dashboard.stat-card>

                <!-- 3. Widget Peminjaman Aktif (Interactive) -->
                <x-dashboard.stat-card 
                    title="Sedang<br>Dipinjam" 
                    value="{{ $activeBorrowingsCount }}" 
                    badgeText="{{ __('ui.units_out') }}" 
                    color="fuchsia"
                    onClick="window.location.href='{{ route('inventory.index', ['filter' => 'borrowed']) }}'">
                    <x-slot name="icon">
                        <x-icon.borrow-user class="w-5 h-5" />
                    </x-slot>
                </x-dashboard.stat-card>

                <!-- 4. Total Kategori (Interactive - Trigger Modal) -->
                <x-dashboard.stat-card 
                    title="Kategori<br>Barang" 
                    value="{{ $totalCategories }}" 
                    badgeText="{{ __('ui.item_types') }}" 
                    color="warning"
                    onClick="openCategoryModal()">
                    <x-slot name="icon">
                        <x-icon.category class="w-5 h-5" />
                    </x-slot>
                </x-dashboard.stat-card>

                <!-- 5. Total Merk (Interactive - Trigger Modal) -->
                <x-dashboard.stat-card 
                    title="Total<br>Merk" 
                    value="{{ $totalBrands }}" 
                    badgeText="Daftar Merk" 
                    color="pink"
                    onClick="openBrandModal()">
                    <x-slot name="icon">
                        <x-icon.tag class="w-5 h-5" />
                    </x-slot>
                </x-dashboard.stat-card>

                                <!-- 6. Total Lokasi -->
                <x-dashboard.stat-card 
                    title="Lokasi<br>Penyimpanan" 
                    value="{{ $totalLocations }}" 
                    badgeText="{{ __('ui.zones') }}" 
                    color="secondary"
                    onClick="openLocationModal()">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </x-slot>
                </x-dashboard.stat-card>
            </div>

            <div x-show="showOverdue && {{ $totalOverdueCount ?? 0 }} > 0 && isLoading" class="mb-6 animate-pulse print:hidden">
                <div class="card border-l-4 border-danger-200">
                    <div class="card-header p-4 border-b border-gray-100 flex justify-between">
                        <div class="h-6 bg-gray-200 rounded w-64"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<3; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showOverdue && {{ $totalOverdueCount ?? 0 }} > 0 && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="mb-6 print:hidden">
                <div class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden">
                    <div class="card-header p-4 bg-gradient-to-r from-red-500 to-orange-600 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h2 class="text-lg font-bold text-white">{{ __('ui.attention_overdue') }} ({{ $totalOverdueCount ?? 0 }})</h2>
                        </div>
                        @if(($totalOverdueCount ?? 0) > 0)
                            <a href="{{ route('inventory.index', ['filter' => 'overdue']) }}" class="text-xs text-white hover:text-red-100 font-bold underline decoration-white/50">{{ __('ui.view_all') }}</a>
                        @endif
                    </div>
                    <!-- Desktop table -->
                    <div class="overflow-x-auto md:block hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-secondary-500 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3">{{ __('ui.borrower') }}</th>
                                    <th class="px-6 py-3">{{ __('ui.item') }}</th>
                                    <th class="px-6 py-3 text-center">{{ __('ui.due_date_short') }}</th>
                                    <th class="px-6 py-3 text-center">{{ __('ui.late') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="borrow in overdueBorrowingsList" :key="borrow.id">
                                    <tr class="hover:bg-secondary-50 cursor-pointer" @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                        <td class="px-6 py-3 font-medium text-secondary-900" x-text="borrow.user_name || borrow.borrower_name"></td>
                                        <td class="px-6 py-3"><span x-text="borrow.sparepart_name"></span> (<span x-text="borrow.quantity"></span>)</td>
                                        <td class="px-6 py-3 text-center font-bold text-danger-600" x-text="borrow.due_date_formatted"></td>
                                        <td class="px-6 py-3 text-center text-danger-500" x-text="borrow.due_date_rel"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile stacked -->
                    <div class="md:hidden divide-y divide-secondary-100">
                        <template x-for="borrow in overdueBorrowingsList" :key="borrow.id">
                            <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-secondary-900" x-text="borrow.user_name || borrow.borrower_name"></div>
                                    <span class="text-xs font-bold text-danger-600 bg-danger-50 px-2 py-1 rounded-full" x-text="borrow.due_date_rel"></span>
                                </div>
                                <div class="text-sm text-secondary-600 mb-1"><span x-text="borrow.sparepart_name"></span> (<span x-text="borrow.quantity"></span> unit)</div>
                                <div class="text-xs text-secondary-500 flex items-center gap-1">
                                    <span>{{ __('ui.due_date_short') }}:</span>
                                    <span class="font-semibold text-danger-600" x-text="borrow.due_date_formatted"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div x-show="showNoPriceItems && (noPriceItems || []).length > 0 && isLoading" class="mb-6 animate-pulse print:hidden">
                <div class="card border-l-4 border-amber-300">
                    <div class="card-header p-4 border-b border-gray-100 flex justify-between">
                        <div class="h-6 bg-gray-200 rounded w-64"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<3; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showNoPriceItems && (noPriceItems || []).length > 0 && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="mb-6 print:hidden">
                <div class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden">
                    <div class="card-header p-4 bg-gradient-to-r from-amber-400 to-orange-400 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <h2 class="text-lg font-bold text-white">Harga Barang Belum Diisi (<span x-text="(noPriceItems || []).length"></span>)</h2>
                        </div>
                    </div>
                    <!-- Desktop table -->
                    <div class="overflow-x-auto md:block hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-secondary-500 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3">Nama Barang</th>
                                    <th class="px-6 py-3">Part Number</th>
                                    <th class="px-6 py-3 text-center">Status Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="item in noPriceItems" :key="item.id">
                                    <tr class="hover:bg-secondary-50 cursor-pointer" @click="window.location.href = '/inventory/' + item.uuid + '/edit'">
                                        <td class="px-6 py-3 font-medium text-secondary-900" x-text="item.name"></td>
                                        <td class="px-6 py-3 font-mono text-xs text-secondary-600" x-text="item.part_number || '-'"></td>
                                        <td class="px-6 py-3 text-center text-amber-600 font-bold">Belum Diisi</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile stacked -->
                    <div class="md:hidden divide-y divide-secondary-100">
                        <template x-for="item in noPriceItems" :key="item.id">
                            <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/' + item.uuid + '/edit'">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-secondary-900" x-text="item.name"></div>
                                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Belum Diisi</span>
                                </div>
                                <div class="text-xs text-secondary-500 flex items-center gap-1 font-mono">
                                    <span x-text="item.part_number || '-'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 CHART PERGERAKAN STOK
                 ================================================================ --}}
            <div x-show="showMovement && isLoading" class="card mb-4 animate-pulse">
                <div class="card-header border-b border-gray-100 p-5">
                    <div class="h-5 bg-gray-200 rounded w-48 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-64"></div>
                </div>
                <div class="card-body p-6">
                    <div class="h-[250px] w-full bg-gray-100 rounded flex items-end justify-between px-4 pb-4 gap-2">
                        @for($i=0; $i<12; $i++)
                            <div class="w-full bg-gray-200 rounded-t" style="height: {{ rand(20, 80) }}%"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showMovement && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="card mb-4">
                <div class="card-header border-b border-secondary-100 p-5">
                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-4">
                        <div class="flex-shrink-0">
                            <h2 class="text-lg font-bold text-secondary-900">Pergerakan Stok</h2>
                            <p class="text-xs text-secondary-500 mb-1">Aktivitas barang masuk vs keluar periode ini.</p>
                            <div class="text-[10px] text-secondary-400 font-medium" id="movement-period-label">Data: 30 hari terakhir</div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-3 flex-shrink-0">
                            {{-- KPI Summary Badges --}}
                            <div class="flex flex-wrap items-center gap-2" id="movement-kpi-badges">
                                <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-2.5 py-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                    <span class="text-xs text-emerald-700 font-medium whitespace-nowrap">Masuk: <span id="kpi-masuk" class="font-bold">0</span> <span id="kpi-masuk-pct" class="text-[10px] ml-0.5 font-bold text-emerald-600"></span></span>
                                </div>
                                <div class="flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-2.5 py-1">
                                    <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                    <span class="text-xs text-red-700 font-medium whitespace-nowrap">Keluar: <span id="kpi-keluar" class="font-bold">0</span> <span id="kpi-keluar-pct" class="text-[10px] ml-0.5 font-bold text-red-600"></span></span>
                                </div>
                                <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-2.5 py-1" id="kpi-net-badge">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" id="kpi-net-dot"></span>
                                    <span class="text-xs font-medium whitespace-nowrap" id="kpi-net-label">Net: <span id="kpi-net" class="font-bold">0</span> <span id="kpi-net-pct" class="text-[10px] ml-0.5 font-bold text-blue-600"></span></span>
                                </div>
                            </div>
                            
                            {{-- Quick-filter Periode Widget --}}
                            <div class="flex items-center gap-1 bg-secondary-100 rounded-lg p-0.5 flex-shrink-0" id="movement-range-btns">
                                <button onclick="fetchMovementData(7)" id="mov-btn-7"
                                        class="mov-range-btn px-3 py-1 rounded-md text-xs font-medium transition-all
                                               {{ in_array($activePeriod ?? '', ['today', 'this_week']) ? 'bg-white shadow-sm text-primary-700' : 'text-secondary-600 hover:bg-white/70' }}">7 Hari</button>
                                <button onclick="fetchMovementData(30)" id="mov-btn-30"
                                        class="mov-range-btn px-3 py-1 rounded-md text-xs font-medium transition-all
                                               {{ ($activePeriod ?? '') === 'this_month' ? 'bg-white shadow-sm text-primary-700' : 'text-secondary-600 hover:bg-white/70' }}">30 Hari</button>
                                <button onclick="fetchMovementData(90)" id="mov-btn-90"
                                        class="mov-range-btn px-3 py-1 rounded-md text-xs font-medium transition-all
                                               {{ in_array($activePeriod ?? '', ['this_year', 'custom', 'custom_year']) ? 'bg-white shadow-sm text-primary-700' : 'text-secondary-600 hover:bg-white/70' }}">3 Bulan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 md:p-6">
                    <div class="min-h-[200px] md:h-[280px] w-full relative">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>
            </div>

            <div x-show="showTopItems && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                 
                 {{-- Barang Sering Keluar --}}
                 <div class="card flex flex-col overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                     <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3 bg-gradient-to-r from-rose-50/50 to-transparent">
                         <div class="w-9 h-9 rounded-xl bg-white shadow-sm border border-rose-100 flex items-center justify-center flex-shrink-0">
                             <svg class="w-4.5 h-4.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                         </div>
                         <div>
                             <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.top_exiting_items') }}</h2>
                             <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                         </div>
                     </div>
                     <div class="flex-grow divide-y divide-secondary-50">
                         <template x-for="(item, index) in topExited" :key="item.sparepart_uuid || index">
                             <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-rose-50/30 transition-all duration-200 cursor-pointer"
                                  @click="window.location.href = '/inventory/' + (item.sparepart_uuid || '')">
                                 <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold transition-transform duration-300 group-hover:scale-110"
                                      :class="{
                                         'bg-amber-100 text-amber-700':  index === 0,
                                         'bg-slate-100 text-slate-600': index === 1,
                                         'bg-orange-100 text-orange-700': index === 2,
                                         'bg-secondary-50 text-secondary-400': index > 2
                                      }"
                                      x-text="index + 1"></div>
                                 <span class="flex-grow font-semibold text-secondary-700 text-sm group-hover:text-rose-700 transition-colors truncate" x-text="item.sparepart_name || 'Unknown'"></span>
                                 <div class="flex flex-col items-end justify-center">
                                     <span class="inline-flex items-center gap-1 font-bold text-sm text-rose-600 tabular-nums">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                         <span x-text="parseInt(item.total_qty || 0).toLocaleString('id-ID')"></span>
                                     </span>
                                     <span class="text-[10px] text-secondary-400 font-medium uppercase tracking-wider mt-0.5">Unit</span>
                                 </div>
                             </div>
                         </template>
                         <div x-show="!topExited || topExited.length === 0" class="px-6 py-10 text-center text-secondary-400">
                             <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                             <p class="text-sm italic">{{ __('ui.no_data_short') }}</p>
                         </div>
                     </div>
                 </div>

                 {{-- Barang Sering Masuk --}}
                 <div class="card flex flex-col overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                     <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3 bg-gradient-to-r from-emerald-50/50 to-transparent">
                         <div class="w-9 h-9 rounded-xl bg-white shadow-sm border border-emerald-100 flex items-center justify-center flex-shrink-0">
                             <svg class="w-4.5 h-4.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                         </div>
                         <div>
                             <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.top_entering_items') }}</h2>
                             <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                         </div>
                     </div>
                     <div class="flex-grow divide-y divide-secondary-50">
                         @forelse($topEntered as $item)
                             @php
                                  $rank = $loop->iteration;
                                  $badgeClass = match(true) {
                                      $rank === 1 => 'bg-amber-100 text-amber-700',
                                      $rank === 2 => 'bg-slate-100 text-slate-600',
                                      $rank === 3 => 'bg-orange-100 text-orange-700',
                                      default     => 'bg-secondary-50 text-secondary-400',
                                  };
                             @endphp
                             <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-emerald-50/30 transition-all duration-200 cursor-pointer"
                                  onclick="window.location.href='{{ route('inventory.show', $item->sparepart_uuid ?? $item->sparepart_id ?? '') }}'">
                                 <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold transition-transform duration-300 group-hover:scale-110 {{ $badgeClass }}">
                                     {{ $rank }}
                                 </div>
                                 <span class="flex-grow font-semibold text-secondary-700 text-sm group-hover:text-emerald-700 transition-colors truncate">{{ $item->sparepart_name ?? 'Unknown' }}</span>
                                 <div class="flex flex-col items-end justify-center">
                                     <span class="inline-flex items-center gap-1 font-bold text-sm text-emerald-600 tabular-nums">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                         {{ number_format($item->total_qty ?? 0, 0, ',', '.') }}
                                     </span>
                                     <span class="text-[10px] text-secondary-400 font-medium uppercase tracking-wider mt-0.5">Unit</span>
                                 </div>
                             </div>
                         @empty
                              <div class="px-6 py-10 text-center text-secondary-400">
                                  <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                  <p class="text-sm italic">{{ __('ui.no_data_short') }}</p>
                              </div>
                         @endforelse
                     </div>
                 </div>

             </div>

            <!-- Bagian Grafik -->
            <div x-show="showCharts && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse print:hidden">
                <!-- Skeleton Donut -->
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5">
                        <div class="h-5 bg-gray-200 rounded w-48"></div>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center">
                        <div class="w-48 h-48 rounded-full border-8 border-gray-200"></div>
                    </div>
                </div>
                <!-- Skeleton Bar -->
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-end justify-around gap-2 px-10">
                        @for($i=0; $i<6; $i++)
                            <div class="w-12 bg-gray-200 rounded-t" style="height: {{ rand(30, 90) }}%"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showCharts && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-1 print:gap-y-8 gap-4 mb-4 print:break-inside-avoid">
                <!-- Grafik Donut -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-secondary-900">Distribusi Stok per Kategori</h2>
 
                    </div>
                    <div class="card-body p-6 flex-grow flex flex-col items-center justify-center bg-white min-h-[300px]">
                        <div x-show="stockByCategory && Object.keys(stockByCategory).length > 0" class="w-full h-full max-h-[300px] relative">
                            <canvas id="stockByCategoryChart"></canvas>
                        </div>
                        <div x-show="!stockByCategory || Object.keys(stockByCategory).length === 0" class="text-center text-secondary-400 flex flex-col items-center justify-center w-full h-full">
                            <svg class="w-12 h-12 mb-3 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            <p class="text-sm italic">{{ __('ui.no_category_data') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Grafik Batang -->
                <div class="card flex flex-col">
                     <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-secondary-900">Stok per Lokasi</h2>

                    </div>
                    <div class="card-body p-6 flex-grow flex flex-col items-center justify-center bg-white min-h-[300px]">
                        <div x-show="stockByLocation && Object.keys(stockByLocation).length > 0" class="w-full h-full max-h-[300px] relative">
                            <canvas id="stockByLocationChart"></canvas>
                        </div>
                        <div x-show="!stockByLocation || Object.keys(stockByLocation).length === 0" class="text-center text-secondary-400 flex flex-col items-center justify-center w-full h-full">
                            <svg class="w-12 h-12 mb-3 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <p class="text-sm italic">{{ __('ui.no_location_data') }}</p>
                        </div>
                    </div>
                </div>
            </div>




            <!-- Bagian Bawah: Stok Rendah & Aktivitas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 print:grid-cols-1 gap-4 print:gap-y-8">
                <!-- Skeleton Stok Rendah -->
                <div x-show="showLowStock && isLoading" 
                     class="card animate-pulse h-[400px] print:hidden"
                     :class="{ 'lg:col-span-3 print:col-span-1': !showRecent, 'lg:col-span-2 print:col-span-1': showRecent }">
                    <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                        <div class="h-5 bg-gray-200 rounded w-48"></div>
                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="p-6 space-y-4">
                         <div class="flex gap-4 mb-4">
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                         </div>
                         @for($i=0; $i<5; $i++)
                             <div class="h-10 bg-gray-100 rounded w-full"></div>
                         @endfor
                    </div>
                </div>

                <!-- Item Stok Rendah (2 kolom) -->
                <div x-show="showLowStock && !isLoading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden print:break-inside-avoid" :class="{ 'lg:col-span-3 print:col-span-1': !showRecent, 'lg:col-span-2 print:col-span-1': showRecent }">
                    <div class="card-header p-5 bg-gradient-to-r from-amber-500 to-orange-500 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                             <div class="p-1.5 bg-white/20 text-white rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                             </div>
                            <h2 class="text-lg font-bold text-white">{{ __('ui.warning_low_stock') }}</h2>
                        </div>
                        <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="text-sm text-white hover:text-amber-100 font-medium underline decoration-white/50">{{ __('ui.view_all') }}</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-secondary-500">
                            <thead class="text-xs text-secondary-700 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold tracking-wider">{{ __('ui.item') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider hidden md:table-cell">{{ __('ui.categories') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">{{ __('ui.stock') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center hidden md:table-cell">{{ __('ui.min_stock') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">{{ __('ui.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="item in lowStockItems" :key="item.uuid">
                                    <tr class="bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/' + item.uuid">
                                        <td class="px-4 py-3 font-medium text-secondary-800" x-text="item.name || 'Unknown'"></td>
                                        <td class="px-6 py-4 hidden md:table-cell" x-text="item.category?.name || '-'"></td>
                                        <td class="px-6 py-4 text-center font-bold text-danger-600" x-text="item.stock"></td>
                                        <td class="px-6 py-4 text-center text-secondary-600 hidden md:table-cell" x-text="item.minimum_stock"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="badge badge-danger" x-show="item.stock == 0">{{ __('ui.status_out_of_stock') }}</span>
                                            <span class="badge badge-danger" x-show="item.stock > 0 && item.stock <= item.minimum_stock">{{ __('ui.status_critical') }}</span>
                                            <span class="badge badge-warning" x-show="item.stock > item.minimum_stock" style="background:#fff7ed;color:#92400e;border-color:#fbbf24;">{{ __('ui.approaching_stock') }}</span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="lowStockItems.length === 0">
                                    <td colspan="5" class="px-6 h-[250px] text-center align-middle text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>{{ __('ui.all_stock_safe') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Skeleton Terkini -->
                <div x-show="showRecent && isLoading" class="card lg:col-span-1 animate-pulse h-[400px]">
                    <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-5 space-y-4">
                        @for($i=0; $i<5; $i++)
                            <div class="flex gap-4">
                                <div class="h-8 w-8 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Aktivitas Terkini (1 kolom di web, penuh di cetak jika diperlukan) -->

                <div x-show="showRecent && !isLoading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card p-0 flex flex-col h-full print-safe" :class="{ 'lg:col-span-3 print:col-span-1': !showLowStock, 'lg:col-span-1 print:col-span-1': showLowStock }">
                     <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.recent_activities') }}</h2>
                        <a href="{{ route('reports.activity-logs.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">{{ __('ui.view_all') }}</a>
                     </div>
                    <div class="card-body p-0 overflow-y-auto max-h-[500px] custom-scrollbar">
                        <div class="flex flex-col">
                            <!-- Alpine Loop -->
                            <template x-for="log in recentActivities" :key="log.id">
                                <div class="px-5 py-2.5 hover:bg-secondary-50 transition-colors group">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all ring-2 ring-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0 cursor-pointer hover:bg-secondary-100 p-2 rounded-lg transition-colors"
                                             @click="viewActivityDetails(log)">
                                            <p class="text-sm font-medium text-secondary-900 line-clamp-2" x-text="log.description"></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-xs text-secondary-500 font-semibold" x-text="log.user_name || log.user?.name || 'Sistem'"></p>
                                                <span class="text-secondary-300">&bull;</span>
                                                <p class="text-xs text-secondary-400" x-text="log.created_at_diff"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Empty State -->
                            <div x-show="recentActivities.length === 0" class="px-5 py-12 text-center min-h-[300px] flex flex-col items-center justify-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-secondary-700">Belum ada aktivitas</p>
                                        <p class="text-xs text-secondary-400 mt-1">{{ __('ui.no_recent_activities') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Baru: Analitik & Perkiraan -->
            <!-- Skeleton Analitik -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4 animate-pulse">
                 <!-- Skeleton Stok Mati -->
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100">
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-10"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <!-- Skeleton Papan Peringkat -->
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100">
                        <div class="h-4 bg-gray-200 rounded w-40"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                </div>
                                <div class="h-4 bg-gray-200 rounded w-16"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showStats && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="flex flex-col lg:flex-row gap-4 mt-4 print:grid print:grid-cols-2">

                {{-- Widget Stok Mati --}}
                <div x-show="showDeadStock" x-transition class="card flex-1 flex flex-col overflow-hidden border-l-4 border-amber-400 min-w-0">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.dead_stock_title') }}</h2>
                            <p class="text-xs text-secondary-400">{{ __('ui.dead_stock_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <template x-for="(item, index) in deadStockItems" :key="item.id">
                            <div class="group flex items-center gap-4 px-6 py-3.5 transition-all duration-150 cursor-pointer"
                                 :class="index % 2 === 0 ? 'bg-white hover:bg-amber-50/50' : 'bg-secondary-50/50 hover:bg-amber-50/50'"
                                 @click="window.location.href = '/inventory/' + item.uuid">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full bg-amber-400"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="item.name"></span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-xs tabular-nums">
                                    <span x-text="item.stock"></span>&nbsp;{{ __('ui.units') }}
                                </span>
                            </div>
                        </template>
                        <div x-show="deadStockItems.length === 0" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm font-medium text-secondary-700">{{ __('ui.all_active_desc') }}</p>
                                <p class="text-xs text-secondary-400">{{ __('ui.no_dead_stock_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Papan Peringkat Pengguna --}}
                <div x-show="showLeaderboard" x-transition class="card flex-1 flex flex-col overflow-hidden border-l-4 border-success-400 min-w-0">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-success-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.top_contributors_title') }}</h2>
                            <p class="text-xs text-secondary-400">Pengguna paling aktif dalam periode ini</p>
                        </div>
                    </div>
                    <div class="flex-grow divide-y divide-secondary-100">
                        <template x-for="(userLog, index) in activeUsers" :key="userLog.user_id || Math.random()">
                            <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-success-50/40 transition-all duration-150">
                                {{-- User initial avatar with rank-tinted colors --}}
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-transform duration-200 group-hover:scale-110"
                                     :class="{
                                        'bg-amber-100 text-amber-700 ring-2 ring-amber-300':  index === 0,
                                        'bg-slate-100  text-slate-600  ring-2 ring-slate-300': index === 1,
                                        'bg-orange-100 text-orange-700 ring-2 ring-orange-300': index === 2,
                                        'bg-success-100 text-success-700': index > 2
                                     }"
                                     x-text="userLog.user ? userLog.user.name.charAt(0).toUpperCase() : '?'"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="userLog.user ? userLog.user.name : 'Unknown'"></span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-success-100 text-success-700 font-bold text-xs tabular-nums">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span x-text="userLog.total_actions"></span> {{ __('ui.actions_count') }}
                                </span>
                            </div>
                        </template>
                        {{-- Empty State --}}
                        <div x-show="activeUsers.length === 0" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium text-secondary-600">Belum ada aktivitas</p>
                                <p class="text-xs text-secondary-400">Data akan muncul setelah ada transaksi stok</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inline modal removed, using partial at the bottom --}}
            


                    </td></tr>
                </tbody>

                {{-- Footer statis di bawah setiap halaman PDF (Dikurangi kontennya agar tidak ganda dengan header) --}}
                <tfoot class="hidden export-show mt-8 pt-4 border-t border-secondary-200 w-full">
                    <tr><td>
                        <div class="text-center text-xs text-secondary-500">
                            Azventory Management System - Laporan Stok & Inventaris
                        </div>
                    </td></tr>
                </tfoot>
            </table> {{-- End print-container --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.js"></script>
    <script>
        // Default Chart untuk konsistensi
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = '#f1f5f9';

        // =====================================================================
        // Helper: Update KPI Summary Badges dari data movement
        // =====================================================================
        function updateMovementKPI(movementData) {
            const totalMasuk = (movementData.masuk || []).reduce((a, b) => a + b, 0);
            const totalKeluar = (movementData.keluar || []).reduce((a, b) => a + b, 0);
            const net = totalMasuk - totalKeluar;

            const elMasuk = document.getElementById('kpi-masuk');
            const elKeluar = document.getElementById('kpi-keluar');
            const elNet = document.getElementById('kpi-net');
            const elNetDot = document.getElementById('kpi-net-dot');
            const elNetLabel = document.getElementById('kpi-net-label');
            const elNetBadge = document.getElementById('kpi-net-badge');

            // Trend elements
            const elMasukPct = document.getElementById('kpi-masuk-pct');
            const elKeluarPct = document.getElementById('kpi-keluar-pct');
            const elNetPct = document.getElementById('kpi-net-pct');

            if (elMasuk) elMasuk.textContent = totalMasuk.toLocaleString('id-ID');
            if (elKeluar) elKeluar.textContent = totalKeluar.toLocaleString('id-ID');
            if (elNet) elNet.textContent = (net >= 0 ? '+' : '') + net.toLocaleString('id-ID');

            const comp = movementData.comparison || {};
            
            const updateTrend = (el, val) => {
                if (!el) return;
                if (val === undefined || val === null) { el.textContent = ''; return; }
                const prefix = val > 0 ? '↑' : (val < 0 ? '↓' : '');
                el.textContent = `${prefix} ${Math.abs(val)}%`;
                el.className = `text-[10px] ml-1 font-bold ${val >= 0 ? 'text-emerald-600' : 'text-red-600'}`;
            };

            updateTrend(elMasukPct, comp.masuk_pct);
            updateTrend(elKeluarPct, comp.keluar_pct);
            updateTrend(elNetPct, comp.net_pct);

            if (elNetBadge && elNetDot && elNetLabel) {
                if (net > 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-emerald-700 font-medium whitespace-nowrap';
                } else if (net < 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-red-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-red-700 font-medium whitespace-nowrap';
                } else {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-blue-400 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-blue-700 font-medium whitespace-nowrap';
                }
            }
        }


        // =====================================================================
        // Export Dashboard â€” PDF (Print) and PNG (html2canvas)
        // =====================================================================
        function exportDashboardPDF() {
            document.title = 'Dashboard Azventory - ' + new Date().toLocaleDateString('id-ID');
            window.print();
        }

        function exportDashboardPNG() {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-secondary-900 text-white text-sm px-4 py-3 rounded-xl shadow-xl flex items-center gap-2';
            toast.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyiapkan gambar...';
            document.body.appendChild(toast);

            // Load html2canvas jika belum ada
            if (typeof html2canvas === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                s.onload = () => doCapture(toast);
                document.head.appendChild(s);
            } else {
                doCapture(toast);
            }
        }

        function doCapture(toastEl) {
            document.body.classList.add('is-exporting'); // Add export class to trigger pure white mode

            // Tunggu sebentar agar CSS apply sebelum direkam
            setTimeout(() => {
                const target = document.querySelector('[x-data]') || document.body;
                html2canvas(target, {
                    scale: 1.5,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    windowWidth: 1280, // force desktop width so it doesn't squish
                    logging: false,
                }).then(canvas => {
                    document.body.classList.remove('is-exporting'); // Remove immediately
                    const link = document.createElement('a');
                    const d = new Date();
                    const dateStr = d.toISOString().slice(0, 10);
                    link.download = `dashboard-azventory-${dateStr}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    if (toastEl) toastEl.remove();
                }).catch(() => {
                    document.body.classList.remove('is-exporting');
                    if (toastEl) toastEl.remove();
                    window.showAlert('Error', 'Gagal mengambil screenshot. Gunakan opsi Cetak/PDF.', 'error');
                });
            }, 300);
        }

        // Muat data 7 hari via AJAX saat pertama load â€” ini adalah default tampilan chart
        const movementDataKey = { labels: [], masuk: [], keluar: [] };

        // Inisialisasi KPI Badge saat load
        updateMovementKPI(@json($movementData));

        // Fungsi pembuatan gradient (dipakai ulang)
        function makeGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        const movCtx = document.getElementById('stockMovementChart').getContext('2d');
        const gradMasuk = makeGradient(movCtx, 'rgba(16,185,129,0.85)', 'rgba(16,185,129,0.15)');
        const gradKeluar = makeGradient(movCtx, 'rgba(239,68,68,0.85)', 'rgba(239,68,68,0.15)');

        // Jika tidak ada label (periode kosong), tampilkan placeholder
        const movLabels = movementDataKey.labels.length > 0 ? movementDataKey.labels : ['{{ __('ui.no_data_short') }}'];
        const movMasuk  = movementDataKey.masuk.length > 0  ? movementDataKey.masuk  : [0];
        const movKeluar = movementDataKey.keluar.length > 0 ? movementDataKey.keluar : [0];

        // Plugin Custom untuk Garis Vertikal (Crosshair)
        const verticalLine = {
            id: 'verticalLine',
            beforeDraw(chart) {
                if (chart.tooltip?._active?.length) {
                    const ctx = chart.ctx;
                    const x = chart.tooltip._active[0].element.x;
                    const topY = chart.scales.y.top;
                    const bottomY = chart.scales.y.bottom;

                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x, topY);
                    ctx.lineTo(x, bottomY);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = 'rgba(100, 116, 139, 0.4)'; // Gray-400
                    ctx.setLineDash([4, 4]); // Dashed
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        let movementChart = new Chart(movCtx, {
            type: 'line',
            plugins: [verticalLine], // Register plugin
            data: {
                labels: movLabels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: movMasuk,
                        backgroundColor: gradMasuk,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    },
                    {
                        label: 'Barang Keluar',
                        data: movKeluar,
                        backgroundColor: gradKeluar,
                        borderColor: '#ef4444',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            padding: 16,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        usePointStyle: true,
                        boxPadding: 6,
                        callbacks: {
                            title(ctx) {
                                return ctx[0].label;
                            },
                            label(ctx) {
                                const val = ctx.parsed.y.toLocaleString('id-ID');
                                return `${ctx.dataset.label}: ${val} unit`;
                            },
                            afterBody(ctx) {
                                if (ctx.length < 2) return '';
                                const masuk  = ctx.find(c => c.datasetIndex === 0)?.parsed.y ?? 0;
                                const keluar = ctx.find(c => c.datasetIndex === 1)?.parsed.y ?? 0;
                                const net = masuk - keluar;
                                const prefix = net >= 0 ? '+' : '';
                                return [`─────────────────`, `Net Stok: ${prefix}${net.toLocaleString('id-ID')} unit`];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10, // Batasi jumlah label agar tidak sesak
                            padding: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148,163,184,0.15)',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: val => val.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 45,
                            autoSkipPadding: 8
                        }
                    }
                }
            }
        });


        // =====================================================================
        // Grafik Donut: Stok berdasarkan Kategori
        // =====================================================================
        const stockByCategoryData = @json($stockByCategory);
        const catCtx = document.getElementById('stockByCategoryChart').getContext('2d');
        // Cool-tone Gradient Colors (Blue -> Violet -> Pink -> Cyan)
        const baseColors = [
            '#3b82f6', // Blue
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#06b6d4', // Cyan
            '#6366f1', // Indigo
            '#14b8a6', // Teal
        ];
        const chartColors = baseColors.map(c => {
            const grd = catCtx.createLinearGradient(0, 0, 0, 300);
            grd.addColorStop(0, c);
            grd.addColorStop(1, c + '90'); // Less transparency for richer color
            return grd;
        });
        const chartColorsBorder = baseColors;

        // Total untuk persentase tooltip
        const catTotal = Object.values(stockByCategoryData).reduce((a, b) => a + b, 0);

        // Responsive legend position
        const isSmallScreen = window.innerWidth < 640;

        // Custom Plugin untuk menggambar dashed ring yang presisi di tengah chart
        const outerDashedRing = {
            id: 'outerDashedRing',
            beforeDraw(chart) {
                const {ctx, chartArea: {top, bottom, left, right, width, height}} = chart;
                const centerX = (left + right) / 2;
                const centerY = (top + bottom) / 2;
                
                // Pastikan radius ring dihitung dari radius chart sebenarnya
                const meta = chart.getDatasetMeta(0);
                if (meta.data.length > 0) {
                    const outerRadius = meta.data[0].outerRadius;
                    const ringRadius = outerRadius + 15; // Jarak ring dari chart

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, ringRadius, 0, 2 * Math.PI);
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#e0e7ff'; // Indigo-100
                    ctx.setLineDash([6, 6]); // Garis putus-putus
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        let stockCategoryChart = new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stockByCategoryData),
                datasets: [{
                    label: '{{ __('ui.total_stock') }}',
                    data: Object.values(stockByCategoryData),
                    backgroundColor: chartColors,
                    borderColor: '#ffffff',
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            plugins: [outerDashedRing, {
                id: 'noData',
                afterDraw: (chart) => {
                    const dataCount = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    if (dataCount === 0) {
                        const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                        ctx.save();
                        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                        ctx.font = '14px sans-serif'; ctx.fillStyle = '#94a3b8';
                        ctx.fillText('{{ __('ui.no_distribution_data') }}', left + width / 2, top + height / 2);
                        ctx.restore();
                    }
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Balanced & Modern
                layout: {
                    padding: 40 // Extra padding for ring
                },
                elements: {
                    arc: {
                        borderWidth: 0,
                        borderColor: '#ffffff',
                        borderRadius: 5,
                        hoverOffset: 10
                    }
                },
                plugins: {
                    legend: {
                        position: isSmallScreen ? 'bottom' : 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: { size: 12 },
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                const val = ctx.parsed;
                                const pct = catTotal > 0 ? ((val / catTotal) * 100).toFixed(1) : 0;
                                return `  ${ctx.label}: ${val.toLocaleString('id-ID')} unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Grafik Batang: Stok berdasarkan Lokasi
        // =====================================================================
        const stockByLocationData = @json($stockByLocation);
        const locCtx = document.getElementById('stockByLocationChart').getContext('2d');
        const gradLoc = locCtx.createLinearGradient(0, 0, 0, 280);
        gradLoc.addColorStop(0, 'rgba(59,130,246,0.9)');
        gradLoc.addColorStop(1, 'rgba(59,130,246,0.2)');

        let stockLocationChart = new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(stockByLocationData),
                datasets: [{
                    label: '{{ __('ui.total_stock') }}',
                    data: Object.values(stockByLocationData),
                    backgroundColor: gradLoc,
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    maxBarThickness: 60
                }]
            },
            plugins: [{
                id: 'noData',
                afterDraw: (chart) => {
                    const dataCount = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    if (dataCount === 0) {
                        const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                        ctx.save();
                        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                        ctx.font = '14px sans-serif'; ctx.fillStyle = '#94a3b8';
                        ctx.fillText('{{ __('ui.no_location_data') }}', left + width / 2, top + height / 2);
                        ctx.restore();
                    }
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                return `  Stok: ${ctx.parsed.y.toLocaleString('id-ID')} unit`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148,163,184,0.15)',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: val => val.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 35,
                            autoSkipPadding: 6
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Global Function untuk Update Chart (Real-time Safe)
        // Dipanggil oleh real-time listener Alpine.js saat ada event baru
        // =====================================================================
        window.updateDashboardCharts = function(movementData, stockByCategory, stockByLocation) {
            // Update Movement Chart + KPI Badges
            if (movementData && movementChart) {
                const newLabels  = (movementData.labels || []).length > 0 ? movementData.labels : ['{{ __('ui.no_data_short') }}'];
                const newMasuk   = (movementData.masuk || []).length  > 0 ? movementData.masuk  : [0];
                const newKeluar  = (movementData.keluar || []).length > 0 ? movementData.keluar : [0];
                movementChart.data.labels = newLabels;
                movementChart.data.datasets[0].data = newMasuk;
                movementChart.data.datasets[1].data = newKeluar;
                movementChart.resize();
                movementChart.update();
                // Sinkronkan KPI badges dengan data terbaru
                updateMovementKPI(movementData);
            }

            // Update Category Chart
            if (stockByCategory && stockCategoryChart) {
                stockCategoryChart.data.labels = Object.keys(stockByCategory);
                stockCategoryChart.data.datasets[0].data = Object.values(stockByCategory);
                stockCategoryChart.resize();
                stockCategoryChart.update();
            }

            // Update Location Chart
            if (stockByLocation && stockLocationChart) {
                stockLocationChart.data.labels = Object.keys(stockByLocation);
                stockLocationChart.data.datasets[0].data = Object.values(stockByLocation);
                stockLocationChart.resize();
                stockLocationChart.update();
            }
        };

        // =====================================================================
        // Alpine Component: Tab Period Global
        // Mengelola state panel "Custom" (Opsi F)
        // =====================================================================
        function globalPeriodFilter() {
            return {
                // Buka panel custom secara otomatis jika periode aktif = custom
                showCustom: {{ in_array($period ?? 'today', ['custom','custom_year']) ? 'true' : 'false' }},
            };
        }

        // =====================================================================
        // Opsi C: Quick-filter per-widget Pergerakan Stok
        // Fetch data movement dari endpoint ringan tanpa reload halaman
        // =====================================================================
        let movementActiveRange = 30; // default aktif

        async function fetchMovementData(range) {
            movementActiveRange = range;

            // Update tampilan state tombol aktif
            document.querySelectorAll('.mov-range-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-primary-700');
                btn.classList.add('text-secondary-600');
            });
            const activeBtn = document.getElementById('mov-btn-' + range);
            if (activeBtn) {
                activeBtn.classList.add('bg-white', 'shadow-sm', 'text-primary-700');
                activeBtn.classList.remove('text-secondary-600');
            }

            // Tampilkan loading state pada canvas
            const canvas = document.getElementById('stockMovementChart');
            if (canvas) canvas.style.opacity = '0.5';

            // Update label periode dan reset error state
            const periodLabel = document.getElementById('movement-period-label');
            const rangeLabel = range === 7 ? '7 hari terakhir' : range === 30 ? '30 hari terakhir' : '3 bulan terakhir';
            if (periodLabel) periodLabel.textContent = 'Data: ' + rangeLabel;
            const errEl = document.getElementById('movement-error');
            const wrapEl = document.getElementById('movement-chart-wrap');
            if (errEl)  errEl.classList.replace('flex', 'hidden');
            if (wrapEl) wrapEl.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("dashboard.movement-data") }}?range=' + range, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data movement');

                const data = await response.json();

                // Update chart dengan data baru
                if (movementChart) {
                    const newLabels  = (data.labels  || []).length > 0 ? data.labels  : ['{{ __('ui.no_data_short') }}'];
                    const newMasuk   = (data.masuk   || []).length > 0 ? data.masuk   : [0];
                    const newKeluar  = (data.keluar  || []).length > 0 ? data.keluar  : [0];

                    movementChart.data.labels = newLabels;
                    movementChart.data.datasets[0].data = newMasuk;
                    movementChart.data.datasets[1].data = newKeluar;
                    movementChart.update('active');
                }

                // Update KPI badges
                updateMovementKPI(data);

            } catch (err) {
                console.error('fetchMovementData error:', err);
                // Tampilkan error state di chart
                const errEl2 = document.getElementById('movement-error');
                const wrapEl2 = document.getElementById('movement-chart-wrap');
                if (errEl2)  errEl2.classList.replace('hidden', 'flex');
                if (wrapEl2) wrapEl2.classList.add('hidden');
            } finally {
                // Hapus loading state
                if (canvas) canvas.style.opacity = '1';
            }
        }
    </script>
    @endpush
    <x-flatpickr />
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.setPickerRange = function(days) {
                    const pickerInput = document.getElementById('date_range_picker_hidden');
                    if (pickerInput && pickerInput._flatpickr) {
                        const end = new Date();
                        const start = new Date();
                        start.setDate(end.getDate() - (days > 0 ? days - 1 : 0));
                        pickerInput._flatpickr.setDate([start, end], true);
                    }
                };

                window.resetCustomPicker = function() {
                    window.location.href = '{{ route("dashboard.superadmin") }}';
                };
            });
        </script>
    @endpush
    </div>
</div>
</x-app-layout>

