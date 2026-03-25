<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetak Banyak Label ({{ $spareparts->count() }} Item)</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        .preview-canvas {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* --- PREMIUIM UNIFIED TOOLBAR --- */
        /* --- SYSTEMATIC TOOLBAR (HYBRID) --- */
        .premium-toolbar {
            background: #0f172a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            min-height: 58px; /* Slightly more compact */
            display: flex;
            align-items: center;
        }

        .toolbar-content {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .mobile-top-bar {
            display: contents; /* Transparency on desktop */
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            order: 1;
        }

        .center-controls {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            order: 2;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
            order: 3;
        }

        @media (max-width: 768px) {
            .premium-toolbar { padding: 8px 0; }
            .toolbar-content {
                flex-direction: column;
                gap: 10px;
                padding: 0 16px;
            }
            .mobile-top-bar {
                display: flex;
                width: 100%;
                justify-content: space-between;
                align-items: center;
                order: 1;
            }
            .center-controls {
                width: 100%;
                justify-content: center;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                padding-top: 10px;
                order: 2;
                gap: 12px;
            }
            .brand-section h1 { font-size: 13px !important; }
            .action-group { gap: 10px; }
            .toolbar-btn { height: 34px !important; padding: 0 10px !important; font-size: 10.5px !important; }
            .btn-print { height: 34px !important; padding: 0 14px !important; font-size: 11px !important; }
        }

        /* Element Uniformity */
        .toolbar-btn, .btn-print, .btn-close {
            height: 38px;
            display: flex;
            align-items: center;
            border-radius: 10px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .control-group {
            display: flex;
            flex-direction: row;
            gap: 4px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 3px;
            border-radius: 12px;
        }

        .control-group .toolbar-btn {
            height: 30px !important;
            border: none;
            background: transparent;
            padding: 0 12px !important;
        }

        .control-group .toolbar-btn.active {
            background: rgba(59, 130, 246, 0.15);
        }

        .toolbar-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            padding: 0 14px;
        }

        .toolbar-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateY(-1px);
        }

        .toolbar-btn.active {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .control-group {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 3px;
        }

        .control-group .toolbar-btn {
            height: 30px !important; /* Nested uniformity */
            border: none;
            background: transparent;
            padding: 0 12px;
        }

        .control-group .toolbar-btn.active {
            background: rgba(59, 130, 246, 0.15);
        }

        .btn-print {
            background: #2563eb;
            color: white;
            padding: 0 20px;
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-print:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }

        /* --- PREVIEW CANVAS (STRICT CONTAINER) --- */
        .preview-canvas {
            padding-top: 100px;
            padding-bottom: 40px;
            background: #f1f5f9;
        }

        @media (max-width: 768px) {
            .preview-canvas { padding-top: 130px; }
        }

        .preview-inner {
            width: 100%;
            overflow-x: auto;
            display: flex;
            justify-content: center;
        }

        #label-container {
            flex-shrink: 0; /* NO SCALING */
        }

        .toolbar-btn {
            height: 38px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: #cbd5e1;
            white-space: nowrap;
        }

        .toolbar-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateY(-1px);
        }

        .toolbar-btn.active {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            padding: 0 24px;
            height: 40px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-print:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        /* --- LAYOUTS --- */
        .layout-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 2mm;
            width: 210mm; /* A4 Width */
            margin: 0 auto;
            background: white;
            padding: 20mm 10mm;
            min-height: 297mm; /* A4 Height */
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            align-content: flex-start;
        }

        .layout-thermal {
            display: flex;
            flex-direction: column;
            gap: 0;
            width: 40mm; /* Generic thermal width */
            margin: 0 auto;
            background: white;
            padding: 0;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            align-items: center;
        }

        .layout-thermal .label-item {
            margin: 0 !important;
            border: none !important;
            page-break-after: always !important;
        }

        /* --- COMPACT LABEL STYLE REPLICATION --- */
        .label-item {
            width: 33mm;
            height: 15mm;
            display: flex !important;
            align-items: center !important;
            background: white !important;
            border: 1.2px solid #ddd !important;
            box-sizing: border-box !important;
            padding: 1.25mm !important;
            page-break-inside: avoid;
            overflow: hidden !important;
            border-radius: 1.5mm !important;
        }

        .qr-section {
            width: 12mm !important;
            height: 12mm !important;
            flex-shrink: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-right: 1.25mm !important;
        }

        .qr-image {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
        }

        .info-section {
            flex-grow: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            overflow: hidden !important;
            height: 12mm !important;
        }

        .label-title {
            font-weight: 700 !important;
            font-size: 5pt !important;
            text-transform: uppercase !important;
            color: #64748b !important;
            margin-bottom: 0.5px !important;
            line-height: 1 !important;
        }

        .label-content {
            font-weight: 800 !important;
            font-size: 6.8pt !important;
            margin-bottom: 1px !important;
            line-height: 1 !important;
            color: #0f172a !important;
        }

        .label-text {
            font-size: 5.5pt !important;
            color: #475569 !important;
            line-height: 1 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .part-number {
            font-family: 'JetBrains Mono', monospace !important;
        }

        @media print {
            @page { size: auto; margin: 0; }
            .premium-toolbar, .sidebar-glass, #sidebar-ui, #loading-ui { display: none !important; }
            body { background: white !important; margin: 0 !important; padding: 0 !important; width: 100% !important; height: auto !important; }
            .preview-canvas { background: none !important; padding: 0 !important; height: auto !important; min-height: 0 !important; overflow: visible !important; }
            .preview-inner { display: block !important; width: 100% !important; height: auto !important; min-height: 0 !important; }
            .layout-grid, .layout-thermal { box-shadow: none !important; margin: 0 auto !important; padding: 0 !important; border: none !important; width: 100% !important; min-height: auto !important; height: auto !important; }
            .layout-grid { padding: 5mm !important; display: flex !important; flex-wrap: wrap !important; gap: 2mm !important; align-content: flex-start !important; } /* Restore flex grid for A4 */
            .label-item { border: 1px dashed #cbd5e1 !important; transform: scale(1); flex-shrink: 0 !important; }
        }

        /* Sidebar Styles */
        .sidebar-glass {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(0, 0, 0, 0.05);
        }

        [x-cloak] { display: none !important; }

        /* Hide Number Input Arrows */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body x-data="{ 
    sidebarOpen: false, 
    layoutMode: 'grid',
    quantities: {},
    globalCount: 1,
    loading: false,
    itemIds: @json($spareparts->pluck('id')),
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
            this.presets = [{ name: 'Standar A4', margin: { top: 10, bottom: 10, left: 10, right: 10 } }];
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

        this.itemIds.forEach(id => this.quantities[id] = 1);
        
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

    get totalLabels() {
        return Object.values(this.quantities).reduce((a, b) => a + Math.max(0, parseInt(b) || 0), 0);
    },
    resetAll() {
        this.globalCount = 0;
        this.itemIds.forEach(id => {
            this.quantities[id] = 0;
        });
        this.updatePreview();
    },
    setAllToOne() {
        this.globalCount = 1;
        this.itemIds.forEach(id => {
            this.quantities[id] = 1;
        });
        this.updatePreview();
    },
    setAll() {
        const val = Math.max(0, parseInt(this.globalCount) || 0);
        this.globalCount = val;
        this.itemIds.forEach(id => this.quantities[id] = val);
        this.updatePreview();
    },
    updatePreview() {
        this.loading = true;
        setTimeout(() => {
            this.loading = false;
        }, 150);
    },
    async logPrint() {
        try {
            await fetch('{{ route('inventory.qr.log') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ids: this.itemIds,
                    counts: this.quantities,
                    total: this.totalLabels
                })
            });
        } catch (e) { console.error('Logging failed', e); }
        window.print();
    }
}">
    
    <nav class="premium-toolbar">
        <div class="toolbar-content">
            <!-- Mobile Row 1 / Desktop Left/Right Content -->
            <div class="mobile-top-bar">
                <!-- Brand Section -->
                <div class="brand-section">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-900/40 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-white text-sm font-black tracking-tight leading-none truncate">Cetak Banyak Label</h1>
                        <p class="text-slate-500 text-[9px] font-bold uppercase tracking-[0.1em] mt-1">
                            <span class="text-blue-400" x-text="itemIds.length"></span> Item &bull; <span class="text-blue-400" x-text="totalLabels"></span> Salinan
                        </p>
                    </div>
                </div>

                <!-- Action Group (Desktop Right / Mobile Row 1 Right) -->
                <div class="action-group md:order-3">
                    <button onclick="window.close()" class="btn-close text-slate-400 hover:text-white transition-colors">Tutup</button>
                    <button @click="logPrint()" class="btn-print group">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>

            <!-- Center Content (Desktop Middle / Mobile Row 2) -->
            <div class="center-controls md:order-2">
                <!-- Layout Switcher -->
                <div class="control-group">
                    <button @click="layoutMode = 'grid'" :class="{'active': layoutMode === 'grid'}" class="toolbar-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span class="hidden sm:inline">Grid A4</span>
                    </button>
                    <button @click="layoutMode = 'thermal'" :class="{'active': layoutMode === 'thermal'}" class="toolbar-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="hidden sm:inline">Thermal</span>
                    </button>
                </div>

                <!-- Configuration Trigger -->
                <button @click="sidebarOpen = true" class="toolbar-btn">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    <span class="hidden sm:inline">Pengaturan</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar-ui" x-show="sidebarOpen" x-cloak class="fixed inset-0 z-[40] overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" class="w-screen max-w-sm pointer-events-auto">
                <div class="flex h-full flex-col sidebar-glass shadow-2xl relative overflow-hidden">
                    <!-- Sidebar Header (Padded Row) -->
                    <div class="flex items-center justify-between mt-20 mb-8 px-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight leading-none group">
                                Pengaturan
                                <span class="block h-1 w-6 bg-blue-600 rounded-full mt-2 transition-all group-hover:w-12"></span>
                            </h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] mt-3" x-text="activeTab === 'quantity' ? 'Sesuaikan jumlah cetak' : 'Atur tata letak & margin'">
                                Sesuaikan jumlah cetak
                            </p>
                        </div>
                        <button @click="sidebarOpen = false" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-all active:scale-90 border border-slate-200/50 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Tab Switcher -->
                    <div class="px-6 mb-6 text-center">
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl w-full">
                            <button @click="activeTab = 'quantity'" :class="activeTab === 'quantity' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all focus:outline-none">Salinan</button>
                            <button @click="activeTab = 'margin'" :class="activeTab === 'margin' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all focus:outline-none">Layout</button>
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent pb-24">
                        <!-- Tab: Quantity Control -->
                        <div x-show="activeTab === 'quantity'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                             <!-- Global Control Box (Padded Row) -->
                             <div class="px-6">
                                <section>
                                    <label for="globalCount" class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest cursor-pointer">Set Semua Item</label>
                                    <div class="bg-blue-600 rounded-2xl p-5 shadow-lg shadow-blue-900/20 relative overflow-hidden group">
                                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-8 -mt-8 blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                                        <div class="relative z-10">
                                            <div class="flex items-center gap-4">
                                                <input type="number" 
                                                       id="globalCount" 
                                                       name="globalCount" 
                                                       x-model.number="globalCount" 
                                                       min="0" 
                                                       x-on:keydown="if(['e', 'E', '+', '-', '.'].includes($event.key)) $event.preventDefault()" 
                                                       class="w-16 bg-white/20 border-white/20 rounded-xl px-2 py-2 text-center text-sm font-black text-white placeholder-white/60 focus:bg-white focus:text-blue-600 outline-none transition-all">
                                                <button @click="setAll()" class="flex-1 bg-white text-blue-600 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-slate-50 active:scale-95 transition-all shadow-md">Terapkan Ke Semua</button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 mt-4">
                                                <button @click="resetAll()" class="py-2 text-[10px] font-black uppercase text-white/70 hover:text-white hover:bg-white/10 rounded-lg border border-white/10 transition-colors">Reset 0</button>
                                                <button @click="setAllToOne()" class="py-2 text-[10px] font-black uppercase text-white/70 hover:text-white hover:bg-white/10 rounded-lg border border-white/10 transition-colors">Semua 1</button>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                             </div>

                             <!-- Item List Header (Padded Row) -->
                             <div class="flex items-center justify-between mb-2 px-6 border-t border-slate-50 pt-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Daftar Item</span>
                                <div class="flex items-center gap-2">
                                     <span class="text-[9px] font-bold text-slate-400 uppercase">Total:</span>
                                     <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100" x-text="totalLabels"></span>
                                </div>
                             </div>

                             <!-- Scrollable Item List (Internal Padded Gutter) -->
                             <div class="space-y-1.5 px-6 pb-4">
                                @foreach($spareparts as $sparepart)
                                <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-4 hover:border-blue-400/50 hover:shadow-lg hover:shadow-blue-900/5 transition-all group/card relative shadow-sm">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-black text-slate-800 leading-snug break-words line-clamp-2 transition-colors group-hover/card:text-blue-700" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $sparepart->name }}
                                        </p>
                                        <p class="text-[9px] font-bold text-slate-500 font-mono tracking-tighter mt-1 opacity-70 group-hover/card:opacity-100">{{ $sparepart->part_number }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        <label for="qty_{{ $sparepart->id }}" class="text-[8px] font-black text-slate-400 uppercase tracking-tighter group-hover/card:text-blue-500 transition-colors cursor-pointer">Salinan</label>
                                        <input type="number" 
                                               id="qty_{{ $sparepart->id }}" 
                                               name="qty[{{ $sparepart->id }}]" 
                                               x-model.number="quantities[{{ $sparepart->id }}]" 
                                               min="0"
                                               x-on:keydown="if(['e', 'E', '+', '-', '.'].includes($event.key)) $event.preventDefault()"
                                               @input.debounce.300ms="updatePreview()" 
                                               class="w-12 bg-slate-100/80 border-slate-200 rounded-xl px-1 py-1.5 text-center text-[13px] font-black text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                                    </div>
                                </div>
                                @endforeach
                             </div>
                        </div>

                        <!-- Tab: Margin Adjuster -->
                        <div x-show="activeTab === 'margin'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="px-6 space-y-8">
                             <section>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Margin Kertas (mm)</span>
                                    <button @click="margin = { top: 10, bottom: 10, left: 10, right: 10 }; selectedPresetIndex = -1;" class="text-[9px] font-black text-slate-400 hover:text-blue-600 transition-colors uppercase flex items-center gap-1 group" title="Kembalikan ke margin bawaan">
                                        <svg class="w-3 h-3 transition-transform group-hover:-rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Bawaan
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Top -->
                                    <div class="space-y-2">
                                        <div class="flex justify-between"><label for="margin_top" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Atas</label><span class="text-[9px] font-black text-blue-600" x-text="margin.top"></span></div>
                                        <input type="range" id="margin_top" name="margin_top" x-model="margin.top" min="0" max="50" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    </div>
                                    <!-- Bottom -->
                                    <div class="space-y-2">
                                        <div class="flex justify-between"><label for="margin_bottom" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Bawah</label><span class="text-[9px] font-black text-blue-600" x-text="margin.bottom"></span></div>
                                        <input type="range" id="margin_bottom" name="margin_bottom" x-model="margin.bottom" min="0" max="50" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    </div>
                                    <!-- Left -->
                                    <div class="space-y-2">
                                        <div class="flex justify-between"><label for="margin_left" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Kiri</label><span class="text-[9px] font-black text-blue-600" x-text="margin.left"></span></div>
                                        <input type="range" id="margin_left" name="margin_left" x-model="margin.left" min="0" max="50" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    </div>
                                    <!-- Right -->
                                    <div class="space-y-2">
                                        <div class="flex justify-between"><label for="margin_right" class="text-[9px] font-bold text-slate-500 uppercase cursor-pointer">Kanan</label><span class="text-[9px] font-black text-blue-600" x-text="margin.right"></span></div>
                                        <input type="range" id="margin_right" name="margin_right" x-model="margin.right" min="0" max="50" class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    </div>
                                </div>
                            </section>

                            <!-- Presets Section -->
                            <section class="pt-4 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Preset Printer</span>
                                    <button @click="saveCurrentAsPreset()" class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-[8px] font-black uppercase hover:bg-blue-100 transition-colors">+ Simpan Baru</button>
                                </div>
                                <div class="space-y-1.5">
                                    <template x-for="(preset, index) in presets" :key="index">
                                        <div class="flex items-center gap-2 group">
                                            <button @click="loadPreset(index)" 
                                                    :class="selectedPresetIndex === index ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-900/10' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-blue-400 hover:text-blue-600'"
                                                    class="flex-1 flex items-center justify-between px-3 py-2 rounded-xl border text-[10px] font-black uppercase transition-all">
                                                <span x-text="preset.name"></span>
                                                <span class="text-[8px] opacity-60" x-text="`${preset.margin.top}/${preset.margin.right}/${preset.margin.bottom}/${preset.margin.left}`"></span>
                                            </button>
                                            <button @click="deletePreset(index)" class="p-2 text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
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

                    <!-- Floating Action Gradient (bottom) -->
                    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none z-10 rounded-b-3xl"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Container -->
    <main class="preview-canvas min-h-screen">
        <div class="preview-inner">
            <div id="label-container" :class="layoutMode === 'grid' ? 'layout-grid' : 'layout-thermal'" 
                 :style="`padding-top: ${margin.top}mm; padding-bottom: ${margin.bottom}mm; padding-left: ${margin.left}mm; padding-right: ${margin.right}mm;`"
                 class="relative transition-all duration-300">

                <!-- Visual Margin Guides (Only show when Layout Tab is active) -->
                <div x-show="activeTab === 'margin'" class="absolute inset-0 pointer-events-none z-[10]" x-cloak>
                    <div class="absolute w-full border-t-2 border-blue-500/40 border-dashed" :style="`top: ${margin.top}mm`" style="z-index: 10;"></div>
                    <div class="absolute w-full border-b-2 border-blue-500/40 border-dashed" :style="`bottom: ${margin.bottom}mm`" style="z-index: 10;"></div>
                    <div class="absolute h-full border-l-2 border-blue-500/40 border-dashed" :style="`left: ${margin.left}mm`" style="z-index: 10;"></div>
                    <div class="absolute h-full border-r-2 border-blue-500/40 border-dashed" :style="`right: ${margin.right}mm`" style="z-index: 10;"></div>
                </div>

                <!-- Empty State (User Friendly) -->
                <div x-show="totalLabels === 0 && !loading" x-cloak class="absolute inset-0 flex flex-col items-center justify-center p-12 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </div>
                    <h3 class="text-slate-400 font-black text-sm uppercase tracking-widest">Pratinjau Kosong</h3>
                    <p class="text-slate-400 text-[10px] mt-2 font-bold">Atur jumlah salinan di sidebar untuk mulai melihat pratinjau</p>
                    <button @click="sidebarOpen = true" class="mt-6 px-5 py-2.5 bg-blue-50 text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-100 transition-all">Buka Sidebar</button>
                </div>

                <template x-for="id in Object.keys(quantities)" :key="id">
                    <template x-for="j in Math.max(0, parseInt(quantities[id]) || 0)" :key="`${id}-${j}`">
                        <div x-html="document.getElementById('template-' + id).innerHTML"></div>
                    </template>
                </template>

                <div id="loading-ui" x-show="loading" x-cloak class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-20 rounded-[inherit]">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 h-10 border-4 border-blue-600/20 border-t-blue-600 rounded-full animate-spin"></div>
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest animate-pulse">Menghitung Tata Letak...</span>
                    </div>
                </div>
            </div>
        </main>

    <!-- SAVE PRESET MODAL -->
    <x-modal name="save-preset-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-bold text-secondary-900">
                Simpan Preset Baru
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                Masukkan nama untuk kombinasi margin saat ini.
            </p>
            
            <div class="mt-6">
                <input type="text" 
                       id="bulk_new_preset_name"
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
    <x-modal name="delete-preset-modal">
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

    <!-- Label Templates -->
    <div style="display: none;">
        @foreach($spareparts as $sparepart)
        <template id="template-{{ $sparepart->id }}">
            <div class="label-item">
                <div class="qr-section">
                    <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR" class="qr-image">
                </div>
                <div class="info-section">
                    <div class="label-title">Part Number</div>
                    <div class="label-content part-number">{{ $sparepart->part_number }}</div>
                    <div class="label-text">{{ $sparepart->name }}</div>
                </div>
            </div>
        </template>
        @endforeach
    </div>
</body>
</html>
