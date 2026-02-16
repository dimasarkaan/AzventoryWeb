<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.print_qr') }} - {{ $sparepart->name }}</title>
    <!-- Use Tailwind via CDN for consistent styling with main app -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* gray-100 */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .label-container {
            background: white;
            padding: 0.5rem; /* Minimal padding */
            border-radius: 0.25rem;
            border: 1px solid #e5e7eb;
            width: 200px; /* Fixed width matching QR exactly */
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-image {
            width: 100%;
            height: auto;
            display: block;
            margin-bottom: 0.1rem; /* Very tight gap */
            image-rendering: -webkit-optimize-contrast;
        }

        .item-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: #000;
            line-height: 1.1;
            margin-bottom: 0;
            max-width: 150px; /* Constrain strictly to visual QR width (approx 75-80%) */
            margin-left: auto;
            margin-right: auto;
            word-wrap: break-word;
        }

        .item-code {
            font-family: monospace;
            font-size: 0.65rem;
            color: #000;
            font-weight: 500;
            margin-top: 0.1rem;
            max-width: 150px;
            margin-left: auto;
            margin-right: auto;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #2563eb; /* primary-600 */
            color: white;
        }
        .btn-primary:hover { background-color: #1d4ed8; }

        .btn-secondary {
            background-color: white;
            border-color: #d1d5db; /* gray-300 */
            color: #374151; /* gray-700 */
        }
        .btn-secondary:hover { background-color: #f9fafb; border-color: #9ca3af; }

        @media print {
            @page {
                size: auto;
                margin: 0mm;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: white;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .label-container {
                box-shadow: none;
                border: none;
                width: auto;
                max-width: 100%;
                padding: 0;
            }

            .action-buttons {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="label-container">
        
        <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code" class="qr-image">
        
        <h1 class="item-name">{{ $sparepart->name }}</h1>
        <p class="item-code">{{ $sparepart->part_number }}</p>
    </div>

    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            {{ __('ui.print_label') }}
        </button>
        <button onclick="window.close()" class="btn btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ __('ui.close') }}
        </button>
    </div>

    <script>
        // Auto print on load if requested query param present? No, let user choose.
    </script>
</body>
</html>
