<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetak Label - {{ $sparepart->name }}</title>
    <link rel="icon" href="{{ asset('logo.svg') }}?v=2" type="image/svg+xml">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/print.css', 'r!important; }

    </style>
    <!-- Dynamic @page handler.
         Grid mode: respects custom margin per page but defaults to A4 size.
         Thermal mode: forces physical size to 40mm x 20mm and strips browser margins. -->
    <style x-html="layoutMode === 'grid' ? `
        @media print {
            @page { 
                size: A4 portrait;
                margin-top: ${margin.top}mm;
                margin-bottom: ${margin.bottom}mm;
                margin-left: ${margin.left}mm;
                margin-right: ${margin.right}mm;
            }
        }
    ` : `
        @media print {
            @page {
                size: 40mm 20mm; /* Roughly the size of one label + gap */
                margin: 0 !important;
            }
        }
    `"></style>
</head>
<body x-data="{
    sidebarOpen: false,
    layoutMode: 'grid',
    quantity: 1,
    loading: false,
    isPrinting: false,
    margin: { top: 10, bottom: 10, left: 10, right: 10 },
    activeTab: 'quantity',
    presets: [],
    selectedPresetIndex: -1,
    newPresetName: '',
    presetToDeleteIndex: null,

    init() {
        const savedPresets = localStorage.getItem('az_print_presets');
        if (savedPresets) {
            this.presets = JSON.parse(savedPresets);
        } else {
            this.presets = [
                { name: 'Standar A4 (Aman)', margin: { top: 10, bottom: 10, left: 10, right: 10 } },
                { name: 'Margin Tipis (Banyak)', margin: { top: 5, bottom: 5, left: 5, right: 5 } },
                { name: 'Margin Lebar', margin: { top: 15, bottom: 15, left: 15, right: 15 } }
            ];
            this.saveToStorage();
        }

        const savedState = localStorage.getItem('az_print_state');
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                if (state.margin) this.margin = state.margin;
                if (state.selectedPresetIndex !== undefined) this.selectedPresetIndex = state.selectedPresetIndex;
            } catch (e) {}
        }

        this.$watch('layoutMode', () => this.updatePreview());
        this.$watch('margin', () => { 
            this.updatePreview(); 
            this.saveStateToStorage(); 
        }, { deep: true });
        this.$watch('selectedPresetIndex', () => this.saveStateToStorage());
        
        this.updatePreview();
    },

    saveStateToStorage() {
        localStorage.setItem('az_print_state', JSON.stringify({
            margin: this.margin,
            selectedPresetIndex: this.selectedPresetIndex
        }));
    },

    saveToStorage() {
        localStorage.setItem('az_print_presets', JSON.stringify(this.presets));
    },

    loadPreset(index) {
        this.selectedPresetIndex = index;
        this.margin = { ...this.presets[index].margin };
        this.updatePreview();
    },

    confirmSavePreset() {
        if (this.newPresetName.trim()) {
            this.presets.push({ name: this.newPresetName, margin: { ...this.margin } });
            this.saveToStorage();
            this.selectedPresetIndex = this.presets.length - 1;
            this.newPresetName = '';
            this.$dispatch('close-modal', 'save-preset-modal');
        }
    },

    confirmDeletePreset() {
        if (this.presetToDeleteIndex !== null) {
            this.presets.splice(this.presetToDeleteIndex, 1);
            this.saveToStorage();
            this.selectedPresetIndex = -1;
            this.presetToDeleteIndex = null;
            this.$dispatch('close-modal', 'delete-preset-modal');
        }
    },

    deletePreset(index) {
        this.presetToDeleteIndex = index;
        this.$dispatch('open-modal', 'delete-preset-modal');
    },

    saveCurrentAsPreset() {
        this.newPresetName = '';
        this.$dispatch('open-modal', 'save-preset-modal');
    },

    resetToFactoryPresets() {
        this.$dispatch('open-modal', 'reset-preset-modal');
    },

    confirmResetPresets() {
        this.presets = [
            { name: 'Standar A4 (Aman)', margin: { top: 10, bottom: 10, left: 10, right: 10 } },
            { name: 'Margin Tipis (Banyak)', margin: { top: 5, bottom: 5, left: 5, right: 5 } },
            { name: 'Margin Lebar', margin: { top: 15, bottom: 15, left: 15, right: 15 } }
        ];
        this.saveToStorage();
        this.selectedPresetIndex = -1;
        this.margin = { top: 10, bottom: 10, left: 10, right: 10 };
        this.$dispatch('close-modal', 'reset-preset-modal');
    },

    updatePreview() {
        this.loading = true;
        setTimeout(() => {
            this.loading = false;
        }, 150);
    },
    async logPrint() {
        if (this.isPrinting) return;
        this.isPrinting = true;
        // Update dynamic @page margin style before printing
        try { await new Promise(r => setTimeout(r, 50)); } catch(e) {}
        try {
            await fetch('{{ route('inventory.qr.log') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ids: [{{ $sparepart->id }}],
                    counts: { {{ $sparepart->id }}: this.quantity },
                    total: this.quantity
                })
            });
        } catch (e) { console.error('Logging failed', e); }
        window.print();
        setTimeout(() => this.isPrinting = false, 1000);
    },

    /* ---- Pagination Helpers ---- */
    get labelW() { return 34; /* label width + gap in mm */ },
    get labelH() { return 16; /* label height + gap in mm */ },
    get labelsPerRow() {
        return Math.max(1, Math.floor((210 - this.margin.left - this.margin.right) / this.labelW));
    },
    get rowsPerPage() {
        return Math.max(1, Math.floor((297 - this.margin.top - this.margin.bottom) / this.labelH));
    },
    get labelsPerPage() {
        return this.labelsPerRow * this.rowsPerPage;
    },
    get pages() {
        const total = Math.max(0, parseInt(this.quantity) || 0);
        if (total === 0) return [{ page: 1, count: 0 }];
        const perPage = this.labelsPerPage;
        const numPages = Math.max(1, Math.ceil(total / perPage));
        return Array.from({ length: numPages }, (_, i) => ({
            page: i + 1,
            count: i < numPages - 1 ? perPage : total - i * perPage
        }));
    }
}"
@keydown.window="if (!['input', 'textarea'].includes(document.activeElement.tagName.toLowerCase())) {
    if ($event.key.toLowerCase() === 's') sidebarOpen = !sidebarOpen;
    if ($event.key.toLowerCase() === 't' && sidebarOpen) activeTab = activeTab === 'quantity' ? 'margin' : 'quantity';
}">

    <nav class="premium-toolbar">
        <div class="toolbar-content">
            <div class="mobile-top-bar">
                <div class="brand-section">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-900/40 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-white text-sm font-black tracking-tight leading-none truncate">Label QR Satuan</h1>
                        <p class="text-slate-500 text-[9px] font-bold uppercase tracking-[0.1em] mt-1">
                            Item: <span class="text-blue-400">{{ $sparepart->part_number }}</span>
                        </p>
                    </div>
                </div>

                <div class="action-group md:order-3">
                    <button onclick="window.close()" class="btn-close text-slate-400 hover:text-white transition-colors">Tutup</button>
                    <button @click="logPrint()" class="btn-print group relative" :disabled="isPrinting" :class="{'opacity-75 cursor-wait': isPrinting}">
                        <svg x-show="!isPrinting" class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <svg x-show="isPrinting" x-cloak class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isPrinting ? 'Menyiapkan...' : 'Cetak'"></span>
                    </button>
                </div>
            </div>

            <div class="center-controls md:order-2">
                <div class="control-group">
                    <button @click="layoutMode = 'grid'" :class="{'active': layoutMode === 'grid'}" class="toolbar-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" stroke-width="2"></path></svg>
                        <span class="hidden sm:inline">Grid A4</span>
                    </button>
                    <button @click="layoutMode = 'thermal'" :class="{'active': layoutMode === 'thermal'}" class="toolbar-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"></path></svg>
                        <span class="hidden sm:inline">Thermal</span>
                    </button>
                </div>

                <button @click="sidebarOpen = !sidebarOpen" :class="{'active': sidebarOpen}" class="toolbar-btn">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    <span class="hidden sm:inline">Pengaturan <kbd class="ml-1 text-[9px] px-1 bg-slate-200/50 rounded font-mono text-slate-500">S</kbd></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar-ui" x-show="sidebarOpen" x-cloak class="fixed inset-0 z-[60] pointer-events-none">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm pointer-events-auto lg:bg-transparent lg:backdrop-blur-none lg:pointer-events-none" @click="sidebarOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" class="w-screen max-w-sm pointer-events-auto">
                <div class="flex h-full flex-col sidebar-glass shadow-2xl relative overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight leading-none group">
                                Pengaturan
                                <span class="block h-1 w-6 bg-blue-600 rounded-full mt-2 transition-all group-hover:w-12"></span>
                            </h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] mt-1" x-text="activeTab === 'quantity' ? 'Sesuaikan jumlah cetak' : 'Atur tata letak & margin'">
                                Sesuaikan jumlah cetak
                            </p>
                        </div>
                        <button @click="sidebarOpen = false" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-all active:scale-90 border border-slate-200/50 shadow-sm flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </button>
                    </div>

                    <!-- Tab Switcher -->
                    <div class="px-6 mb-6">
                        <div class="flex p-1.5 bg-slate-100 rounded-xl">
                            <button @click="activeTab = 'quantity'" :class="activeTab === 'quantity' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all">Salinan</button>
                            <button @click="activeTab = 'margin'" :class="activeTab === 'margin' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="flex-1 flex items-center justify-center gap-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Layout <kbd class="px-1 py-0.5 bg-slate-200 text-slate-500 rounded font-mono text-[8px]" title="Tekan T untuk ganti tab">T</kbd>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 space-y-8 pb-24 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
                        <!-- Tab: Quantity -->
                        <div x-show="activeTab === 'quantity'" class="space-y-8">
                            <section>
                                <div class="bg-blue-600 rounded-2xl p-5 shadow-lg shadow-blue-900/20 relative overflow-hidden group">
                                    <div class="relative z-10">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-1">
                                                <label for="quantity" class="text-[11px] font-black text-white/70 uppercase tracking-widest mb-1 pointer-events-none">{{ $sparepart->name }}</label>
                                                <p class="text-[13px] font-black text-white leading-tight break-words">{{ $sparepart->part_number }}</p>
                                            </div>
                                            <div class="flex items-center">
                                                <button @click="quantity > 1 ? quantity-- : null; updatePreview()" class="w-8 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-l-xl transition-colors font-bold select-none">-</button>
                                                <input type="number"
                                                       id="quantity"
                                                       name="quantity"
                                                       x-model.number="quantity"
                                                       @input.debounce.300ms="updatePreview()"
                                                       min="1"
                                                       x-on:keydown="if(['e', 'E', '+', '-', '.'].includes($event.key)) $event.preventDefault()"
                                                       class="w-16 bg-white/20 border-none px-2 py-3 text-center text-sm font-black text-white focus:bg-white focus:text-blue-600 outline-none transition-all focus:ring-0">
                                                <button @click="quantity++; updatePreview()" class="w-8 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-r-xl transition-colors font-bold select-none">+</button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 mt-4">
                                            <button @click="quantity = 0; updatePreview()" class="py-2 text-[10px] font-black uppercase text-white/70 hover:text-white hover:bg-white/10 rounded-lg border border-white/10 transition-colors">Reset 0</button>
                                            <button @click="quantity = 1; updatePreview()" class="py-2 text-[10px] font-black uppercase text-white/70 hover:text-white hover:bg-white/10 rounded-lg border border-white/10 transition-colors">Set 1</button>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Tab: Margin -->
                        <div x-show="activeTab === 'margin'" class="space-y-8">
                            <section>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Margin Kertas (mm)</span>
                                    <button @click="margin = { top: 10, bottom: 10, left: 10, right: 10 }; selectedPresetIndex = -1;" class="text-[9px] p-2 -mr-2 font-black text-slate-400 hover:text-blue-600 transition-colors uppercase flex items-center gap-1 group" title="Kembalikan ke margin bawaan">
                                        <svg class="w-3 h-3 transition-transform group-hover:-rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Bawaan
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 gap-5">
                                    <!-- Top -->
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center"><label for="margin_top" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Atas</label><span class="text-[10px] font-black text-blue-600" x-text="margin.top"></span></div>
                                        <div class="py-1"><input type="range" id="margin_top" name="margin_top" x-model="margin.top" min="0" max="50" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"></div>
                                    </div>
                                    <!-- Bottom -->
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center"><label for="margin_bottom" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Bawah</label><span class="text-[10px] font-black text-blue-600" x-text="margin.bottom"></span></div>
                                        <div class="py-1"><input type="range" id="margin_bottom" name="margin_bottom" x-model="margin.bottom" min="0" max="50" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"></div>
                                    </div>
                                    <!-- Left -->
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center"><label for="margin_left" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Kiri</label><span class="text-[10px] font-black text-blue-600" x-text="margin.left"></span></div>
                                        <div class="py-1"><input type="range" id="margin_left" name="margin_left" x-model="margin.left" min="0" max="50" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"></div>
                                    </div>
                                    <!-- Right -->
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center"><label for="margin_right" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Kanan</label><span class="text-[10px] font-black text-blue-600" x-text="margin.right"></span></div>
                                        <div class="py-1"><input type="range" id="margin_right" name="margin_right" x-model="margin.right" min="0" max="50" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"></div>
                                    </div>
                                </div>
                            </section>

                            </section>

                            <section class="pt-4 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Preset Printer</span>
                                    <div class="flex items-center gap-1">
                                        <button @click="resetToFactoryPresets()" class="p-2 text-[8px] font-black uppercase text-slate-400 hover:text-red-500 transition-colors" title="Kembalikan Preset Bawaan Pabrik">Reset</button>
                                        <button @click="saveCurrentAsPreset()" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase hover:bg-blue-100 transition-colors">+ Simpan Baru</button>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <template x-for="(preset, index) in presets" :key="index">
                                        <div class="flex items-center gap-2 group">
                                            <button @click="loadPreset(index)" 
                                                    :class="selectedPresetIndex === index ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-900/10' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-blue-400 hover:text-blue-600'"
                                                    class="flex-1 flex items-center justify-between px-4 py-3 rounded-xl border text-[10px] font-black uppercase transition-all">
                                                <span x-text="preset.name"></span>
                                                <span class="text-[8px] opacity-60" x-text="`${preset.margin.top}/${preset.margin.right}/${preset.margin.bottom}/${preset.margin.left}`"></span>
                                            </button>
                                            <button @click="deletePreset(index)" class="p-3 text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all focus:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </section>
                            <div class="pt-4 border-t border-slate-100">
                                <p class="text-[9px] leading-relaxed text-slate-400 font-medium italic">Gunakan margin bila hasil cetakan printer thermal anda terpotong atau tidak presisi.</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none z-10 rounded-b-3xl"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Container -->
    <main class="preview-canvas min-h-screen">
        <div class="preview-inner">

            <!-- Grid Mode: One page-card per A4 page -->
            <template x-if="layoutMode === 'grid'">
                <div>
                    <template x-for="(pg, pgIdx) in pages" :key="pgIdx">
                        <div class="page-card mb-8"
                     :style="`padding-top: ${margin.top}mm; padding-bottom: ${margin.bottom}mm; padding-left: ${margin.left}mm; padding-right: ${margin.right}mm;`">

                    <!-- Margin guides - visible on screen when margin tab is active -->
                    <div x-show="activeTab === 'margin'" class="margin-guide absolute inset-0 pointer-events-none" style="z-index:10;" x-cloak>
                        <div class="absolute w-full border-t-2 border-blue-500/40 border-dashed" :style="`top: ${margin.top}mm`"></div>
                        <div class="absolute w-full border-b-2 border-blue-500/40 border-dashed" :style="`bottom: ${margin.bottom}mm`"></div>
                        <div class="absolute h-full border-l-2 border-blue-500/40 border-dashed" :style="`left: ${margin.left}mm`"></div>
                        <div class="absolute h-full border-r-2 border-blue-500/40 border-dashed" :style="`right: ${margin.right}mm`"></div>
                    </div>

                    <!-- Page number badge (screen only) -->
                    <div class="print-page-num absolute top-2 right-2 bg-slate-100 text-slate-400 text-[8px] font-bold px-2 py-0.5 rounded-full" x-text="`Halaman ${pgIdx + 1} / ${pages.length}`"></div>

                            <!-- Labels for this page (grid mode) -->
                            <div class="layout-grid">
                                <template x-for="i in pg.count" :key="i">
                            <div class="label-item">
                                <div class="qr-section">
                                    <img src="{{ Storage::url($sparepart->qr_code_path) }}" class="qr-image" alt="QR Code">
                                </div>
                                <div class="info-section">
                                    <div class="label-title">PART NUMBER</div>
                                    <div class="label-content part-number">{{ $sparepart->part_number }}</div>
                                    <div class="label-text">{{ $sparepart->name }}</div>
                                </div>
                            </div>
                        </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Thermal Mode: Single continuous column (each label has page-break-after:always in CSS) -->
            <template x-if="layoutMode === 'thermal'">
                <div class="layout-thermal pb-10">
                    <template x-for="i in Math.max(0, parseInt(quantity) || 0)" :key="'thermal-'+i">
                        <div class="label-item layout-thermal-item">
                            <div class="qr-section">
                                <img src="{{ Storage::url($sparepart->qr_code_path) }}" class="qr-image" alt="QR Code">
                            </div>
                            <div class="info-section">
                                <div class="label-title">PART NUMBER</div>
                                <div class="label-content part-number">{{ $sparepart->part_number }}</div>
                                <div class="label-text">{{ $sparepart->name }}</div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

        </div>
    </main>


    <!-- SAVE PRESET MODAL -->
    <x-modal name="save-preset-modal" focusable zIndex="z-[70]">
        <div class="p-6">
            <h2 class="text-lg font-bold text-secondary-900">
                Simpan Preset Baru
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                Masukkan nama untuk kombinasi margin saat ini.
            </p>
            
            <div class="mt-6">
                <input type="text" 
                       id="new_preset_name"
                       name="new_preset_name"
                       x-model="newPresetName" 
                       @keydown.enter="confirmSavePreset()"
                       class="input-field w-full" 
                       placeholder="Contoh: Printer Thermal Gudang" 
                       autofocus>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'save-preset-modal')" class="btn btn-secondary">
                    Batal
                </button>
                <button type="button" @click="confirmSavePreset()" class="btn btn-primary">
                    Simpan Preset
                </button>
            </div>
        </div>
    </x-modal>

    <!-- DELETE PRESET MODAL -->
    <x-modal name="delete-preset-modal" zIndex="z-[70]">
        <div class="p-6">
            <h2 class="text-lg font-bold text-secondary-900">
                Hapus Preset?
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                Apakah Anda yakin ingin menghapus preset ini? Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'delete-preset-modal')" class="btn btn-secondary">
                    Batal
                </button>
                <button type="button" @click="confirmDeletePreset()" class="btn btn-danger">
                    Hapus
                </button>
            </div>
        </div>
    </x-modal>

    <!-- RESET PRESET MODAL -->
    <x-modal name="reset-preset-modal" zIndex="z-[70]">
        <div class="p-6">
            <h2 class="text-lg font-bold text-secondary-900">
                Kembalikan ke Preset Bawaan?
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                Apakah Anda yakin ingin menghapus semua preset tersimpan dan kembali ke template bawaan sistem?
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'reset-preset-modal')" class="btn btn-secondary">
                    Batal
                </button>
                <button type="button" @click="confirmResetPresets()" class="btn btn-primary">
                    Ya, Reset
                </button>
            </div>
        </div>
    </x-modal>
</body>
</html>
