<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sparepart->part_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        @page {
            size: 33mm 15mm;
            margin: 0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            width: 33mm;
            height: 15mm;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: white;
        }

        .label-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 1mm; /* Safety margin */
            box-sizing: border-box;
        }

        .qr-section {
            width: 12mm; /* Approximately 12mm for QR */
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
            image-rendering: pixelated; /* Crisp QR edges */
        }

        .info-section {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            height: 12mm; /* Match QR height */
        }

        .label-text {
            font-size: 6pt; /* Very small text */
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

        /* Screen preview styling only */
        @media screen {
            body {
                background: #f0f0f0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .label-container {
                width: 33mm;
                height: 15mm;
                background: white;
                border: 1px solid #ccc;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <div class="label-container" onclick="window.print()">
        <div class="qr-section">
             <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR" class="qr-image">
        </div>
        <div class="info-section">
            <!-- PN is critical -->
            <div class="label-title">Part Number</div>
            <div class="label-content part-number">{{ $sparepart->part_number }}</div>
            
            <!-- Name is secondary -->
            <div class="label-text">{{ $sparepart->name }}</div>
        </div>
    </div>
    
    <script>
        // Optional: Auto-print when opened in a popup
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
