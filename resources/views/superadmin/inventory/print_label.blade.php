<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label - {{ \Illuminate\Support\Str::title($sparepart->category) }} - {{ strtoupper($sparepart->part_number) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        /* Base Label Style (33mm x 15mm) */
        .label-item {
            font-family: 'Inter', sans-serif;
            width: 33mm;
            height: 15mm;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: white;
            border: 1px solid #ddd; /* Light border for preview */
            box-sizing: border-box;
            padding: 1mm;
            page-break-inside: avoid;
        }

        .qr-section {
            width: 12mm;
            height: 12mm;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1mm;
        }

        .qr-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .info-section {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            height: 12mm;
        }

        .label-text {
            font-size: 6pt;
            color: black;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .label-title {
            font-weight: 700;
            font-size: 5pt;
            text-transform: uppercase;
            color: #444;
            margin-bottom: 1px;
        }

        .label-content {
            font-weight: 700;
            font-size: 6.5pt;
            margin-bottom: 2px;
        }
        
        .part-number {
             font-family: monospace;
             font-size: 6pt;
        }

        /* Toolbar Styling */
        #toolbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #1f2937;
            color: white;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        #preview-area {
            margin-top: 80px; /* Space for toolbar */
            padding: 2rem;
            min-height: calc(100vh - 80px);
            background: #f3f4f6;
            display: flex;
            justify-content: center;
        }

        #label-container {
             background: white;
             padding: 10mm;
             box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
             min-height: 50mm;
             width: 210mm; /* Default A4 width view */
        }

        /* Layout Modes */
        .layout-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 2mm;
            align-content: flex-start;
        }

        .layout-thermal {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2mm;
            width: auto !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        /* Print Specifics */
        @media print {
            @page {
                size: A4 portrait; /* Force Portrait A4 */
                margin: 0;
            }
            
            body { 
                background: white; 
                margin: 0; 
            }

            #toolbar { display: none !important; }

            #preview-area {
                margin: 0;
                padding: 10mm; /* Keep 10mm padding to ensure 5 items fit */
                background: white;
                display: block;
                width: 210mm;
                box-sizing: border-box;
            }

            #label-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%; 
                max-width: 100%;
            }

            .layout-thermal {
                width: 100% !important; /* Force full width for thermal to center if needed, or keep auto */
                padding: 0 !important;
                margin: 0 !important;
                align-items: flex-start !important; /* Align left for thermal roll usually */
            }
            
            .layout-thermal .label-item {
                 margin-bottom: 2mm; /* Gap for thermal cutting */
                 border: none;
                 page-break-inside: avoid;
            }

            .label-item {
                border: 1px dashed #9ca3af; /* Gray border for cutting guide */
            }
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Screen Toolbar -->
    <div id="toolbar" class="print:hidden">
        <div class="flex flex-col">
            <h1 class="font-bold text-lg">Cetak Label QR</h1>
            <p class="text-xs text-gray-400">{{ $sparepart->name }} ({{ $sparepart->part_number }})</p>
        </div>
        
        <div class="h-8 w-px bg-gray-600 mx-2"></div>

        <div class="flex items-center gap-2">
            <label class="text-sm">Jumlah:</label>
            <input type="number" id="copy-count" value="1" min="1" max="100" class="w-16 px-2 py-1 text-black rounded text-sm" onchange="updatePreview()">
        </div>

        <div class="flex items-center gap-2">
            <label class="text-sm">Layout:</label>
            <select id="layout-mode" class="text-black rounded px-2 py-1 text-sm w-32" onchange="updateMode()">
                <option value="grid">A4 (Grid)</option>
                <option value="thermal">Thermal (Roll)</option>
            </select>
        </div>
        
        <div class="flex-grow"></div>

        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print
        </button>
        
        <button onclick="window.close()" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm font-bold transition-colors">
            Tutup
        </button>
    </div>

    <!-- Preview/Print Area -->
    <div id="preview-area">
        <div id="label-container" class="layout-grid">
            <!-- Labels will be injected here -->
        </div>
    </div>

    <!-- Template for JS Cloning -->
    <template id="label-template">
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

    <script>
        function updatePreview() {
            const container = document.getElementById('label-container');
            const template = document.getElementById('label-template');
            const count = parseInt(document.getElementById('copy-count').value) || 1;
            
            // Clear current
            container.innerHTML = '';

            // Clone
            for(let i=0; i<count; i++) {
                const clone = template.content.cloneNode(true);
                container.appendChild(clone);
            }
        }

        function updateMode() {
            const mode = document.getElementById('layout-mode').value;
            const container = document.getElementById('label-container');
            
            if(mode === 'thermal') {
                container.className = 'layout-thermal';
                // Trigger styles for thermal
            } else {
                 container.className = 'layout-grid';
            }
        }

        // Initialize
        window.onload = function() {
            updatePreview();
        }
    </script>
</body>
</html>
