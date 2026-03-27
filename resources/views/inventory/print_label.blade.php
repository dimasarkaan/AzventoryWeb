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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; background-color: #f1f5f9; margin: 0; padding: 0; }
        
        /* Paginated Preview for Screen */
        @media screen {
            .preview-inner {
                background-image: linear-gradient(to bottom, transparent 296.5mm, rgba(59, 130, 246, 0.15) 296.5mm, rgba(59, 130, 246, 0.15) 297mm, transparent 297mm);
                background-size: 100% 297mm;
                background-attachment: local;
            }
        }

        /* --- SYSTEMATIC TOOLBAR (HYBRID) --- */
        .premium-toolbar {
            background: #0f172a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            min-height: 58px;
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

        .mobile-top-bar { display: contents; }

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
            gap: 8px;
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

        .btn-print {
            background: #2563eb;
            color: white !important;
            padding: 0 24px;
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

        /* --- PREVIEW CANVAS --- */
        .preview-canvas {
            padding-top: 100px;
            padding-bottom: 60px;
            background: #e2e8f0;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
        }

        @media (max-width: 768px) {
            .preview-canvas { padding-top: 130px; }
        }

        .preview-inner {
            width: 100%;
            overflow-x: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px; /* Gap between A4 page cards on screen */
        }

        /* A4 Page Card on Screen */
        .page-card {
            width: 210mm;
            min-height: 297mm;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 1px 4px rgba(0,0,0,0.08);
            position: relative;
            flex-shrink: 0;
        }

        /* --- LAYOUTS (Standardized) --- */
        .layout-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
            width: 100%;
            align-content: flex-start;
        }

        .layout-thermal {
            display: flex;
            flex-direction: column;
            gap: 2mm; /* Give gap between labels */
            width: 40mm;
            min-height: 100mm; /* Just for visual on screen */
            margin: 0 auto;
            background: white;
            padding: 2mm 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 1px 4px rgba(0,0,0,0.08); /* Similar shadow to A4 */
            align-items: center;
        }

        .layout-thermal .layout-thermal-item {
            margin: 0 !important; /* Managed by gap */
            page-break-after: always !important;
            break-after: page !important;
            /* Do not remove border */
        }

        /* --- LABEL COMPONENT --- */
        .label-item {
            width: 33mm;
            height: 15mm;
            display: flex !important;
            align-items: center !important;
            background: white !important;
            box-sizing: border-box !important;
            padding: 1.25mm !important;
            overflow: hidden !important;
            border-radius: 2mm !important;
            border: 1px solid #000000 !important;
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

        .qr-image { width: 100% !important; height: 100% !important; object-fit: contain !important; }

        .info-section {
            flex-grow: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            overflow: hidden !important;
            height: 12mm !important;
        }

        .label-title { font-weight: 700 !important; font-size: 5pt !important; text-transform: uppercase !important; color: #64748b !important; margin-bottom: 0.5px !important; line-height: 1 !important; }
        .label-content { font-weight: 800 !important; font-size: 6.8pt !important; margin-bottom: 1px !important; line-height: 1 !important; color: #0f172a !important; }
        .label-text { font-size: 5.5pt !important; color: #475569 !important; line-height: 1 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; }
        .part-number { font-family: 'JetBrains Mono', monospace !important; }

        @media print {
            .premium-toolbar, .sidebar-glass, #sidebar-ui, #loading-ui, .margin-guide, .print-page-num { 
                display: none !important; 
            }
            html, body { background: white !important; margin: 0 !important; padding: 0 !important; width: 100% !important; height: auto !important; overflow: visible !important; }
            .preview-canvas { background: none !important; padding: 0 !important; height: auto !important; min-height: 0 !important; overflow: visible !important; display: block !important; }
            .preview-inner { display: block !important; width: 100% !important; height: auto !important; min-height: 0 !important; overflow: visible !important; gap: 0 !important; }
            /* Each page-card becomes a page — reset for print */
            .page-card { 
                width: 100% !important; 
                min-height: 0 !important; 
                box-shadow: none !important; 
                display: block !important;
                page-break-after: always !important;
                break-after: page !important;
                overflow: visible !important;
            }
            .page-card:last-child { page-break-after: auto !important; break-after: auto !important; }
            .layout-grid { 
                display: block !important; 
                text-align: left !important; 
                line-height: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }
            .label-item { 
                display: inline-flex !important; 
                margin: 1mm !important; 
                border: 1px solid #000000 !important; 
                transform: scale(1) !important; 
                flex-shrink: 0 !important; 
                page-break-inside: avoid !important; 
                break-inside: avoid !important; 
                vertical-align: top !important;
            }
            /* Specific fix for thermal print layout */
            .layout-thermal {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                text-align: left !important;
            }
            .layout-thermal .layout-thermal-item {
                display: flex !important; /* Force block-level flex to allow page-break */
                margin: 0 !important;
                border: 1px solid #000000 !important;
                page-break-after: always !important;
                break-after: page !important;
            }
        }

        /* Sidebar Styles */
        .sidebar-glass {
            background: #ffffff;
            border-left: 1px solid rgba(0, 0, 0, 0.08);
        }

        [x-cloak] { display: none !important; }

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

    updatePreview() {
        this.loading = true;
        setTimeout(() => {
            this.loading = false;
        }, 150);
    },
    async logPrint() {
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
                    <button @click="logPrint()" class="btn-print group">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak</span>
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
                    <span class="hidden sm:inline">Pengaturan</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar-ui" x-show="sidebarOpen" x-cloak class="fixed inset-0 z-[60] overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="sidebarOpen = false"></div>
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
                            <button @click="activeTab = 'margin'" :class="activeTab === 'margin' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all">Layout</button>
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
                                            <div class="flex flex-col items-center">
                                                <input type="number"
                                                       id="quantity"
                                                       name="quantity"
                                                       x-model.number="quantity"
                                                       @input.debounce.300ms="updatePreview()"
                                                       min="1"
                                                       x-on:keydown="if(['e', 'E', '+', '-', '.'].includes($event.key)) $event.preventDefault()"
                                                       class="w-16 bg-white/20 border-white/20 rounded-xl px-2 py-2 text-center text-sm font-black text-white focus:bg-white focus:text-blue-600 outline-none transition-all">
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
</body>
</html>
