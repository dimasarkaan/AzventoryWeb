    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js/dist/flatpickr.min.css" crossorigin="anonymous">
        <style>
            /* --- Flatpickr Premium Theme (Figma Auto Layout) --- */
            .flatpickr-calendar { 
                background: #ffffff; 
                border: 1px solid #f1f5f9; 
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02); 
                border-radius: 28px; 
                z-index: 99999 !important;
                padding: 24px; 
                font-family: inherit;
                width: 380px !important; 
                max-width: 95vw !important; /* Mobile Fix */
                overflow: visible !important; 
                margin-top: 12px !important; /* Proper spacing from input */
                position: absolute; /* Auto fallback bila CDN lambat */
            }

            /* Mencegah kalender ngablak / bocor jika CDN JSDelivr diblokir atau telat me-load */
            .flatpickr-calendar:not(.open):not(.inline) {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }

            @media (max-width: 480px) {
                .flatpickr-calendar {
                    padding: 16px 12px !important;
                    width: 320px !important;
                    border-radius: 20px;
                }
                .custom-month-selector {
                    min-width: 65px !important;
                    padding: 0 8px !important;
                    font-size: 0.8rem !important;
                    justify-content: center !important;
                }
                .numInputWrapper {
                    width: 70px !important;
                    grid-column: span 1 / span 1 !important;
                }
                .stat-card {
                    padding: 16px !important;
                }
                .flatpickr-months {
                    gap: 8px !important;
                }
            }
            
            /* --- Export Mode Styles (PDF & PNG) --- */
            body.is-exporting, .is-exporting .min-h-screen {
                background-color: #ffffff !important;
            }
            .is-exporting .export-hide {
                display: none !important;
            }
            .is-exporting .export-show {
                display: block !important;
                margin-bottom: 24px;
            }
            .is-exporting .max-w-7xl {
                max-width: 100% !important;
                padding: 20px !important;
            }
            @media print {
                body, .min-h-screen, .bg-gray-100 { 
                    background-color: #ffffff !important; 
                    -webkit-print-color-adjust: exact !important; 
                    print-color-adjust: exact !important; 
                }
                
                /* Sembunyikan elemen skeleton, navigasi, dan elemen non-cetak */
                nav, header, form, button, .btn, .no-print, .animate-pulse { display: none !important; }
                .export-hide { display: none !important; }

                /* Aturan Print Table Header agar berulang di setiap halaman */
                thead.export-show { display: table-header-group !important; }
                tfoot.export-show { display: table-footer-group !important; }
                .export-show { display: block !important; }
                .print-container { display: table !important; width: 100% !important; }

                /* Mencegah grid collapse */
                .max-w-7xl { max-width: none !important; margin: 0 !important; padding: 0 !important; }

                /* Mencegah grafik mencetak terlalu besar */
                canvas { max-height: 280px !important; width: auto !important; margin: 0 auto !important; }
                .card-body.min-h-\[300px\] { min-height: 280px !important; }
                
                @page { 
                    margin: 12mm; 
                    size: auto; /* Mencegah browser print dialog menambahkan header/footer bawaan (URL, tgl) */
                }
            }
            
            /* Normalisasi tabel menjadi block di layar monitor agar layout CSS Grid Tailwind tidak rusak */
            @media screen {
                table.print-container, 
                table.print-container > tbody, 
                table.print-container > tbody > tr, 
                table.print-container > tbody > tr > td {
                    display: block; 
                    width: 100%;
                }
                /* Pastikan header dan footer cetak benar-benar tersembunyi di layar reguler */
                table.print-container > thead, 
                table.print-container > tfoot {
                    display: none;
                }
            }
            
            /* FORCE ALL INTERNAL CONTAINERS TO ALLOW DROPDOWN OVERLAP & CENTERING */
            .flatpickr-innerContainer {
                display: flex !important;
                justify-content: center !important;
            }
            .flatpickr-rContainer {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                max-width: 100% !important;
                overflow: visible !important;
            }
            .flatpickr-days, 
            .flatpickr-weeks,
            .flatpickr-month, 
            .flatpickr-current-month { 
                overflow: visible !important; 
            }
            .dayContainer, .flatpickr-weekdaycontainer {
                margin: 0 auto !important;
                justify-content: center !important;
                display: flex !important;
                flex-wrap: wrap !important;
            }
            .flatpickr-weekdaycontainer { width: 100% !important; }

            /* Header: True Auto Layout & Stacking Context */
            .flatpickr-months { 
                display: flex !important;
                align-items: center !important;
                justify-content: center !important; /* CENTER EVERYTHING */
                padding: 4px 0 !important;
                margin-bottom: 20px;
                position: relative !important;
                z-index: 100 !important; /* Higher than days */
                gap: 16px !important; /* Slightly more gap for balance */
                overflow: visible !important;
                width: 100% !important;
            }
            .flatpickr-month { 
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: 44px !important;
                width: 100% !important;
                margin: 0 !important;
            }
            .flatpickr-current-month { 
                position: static !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                padding: 0 !important;
                gap: 12px !important;
                font-size: 1rem;
                font-weight: 700;
            }
            
            /* Custom Dropdown Trigger (Replaces Static/Native) */
            .custom-month-selector {
                position: relative;
                display: flex;
                align-items: center;
                gap: 8px;
                background: #ffffff;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                padding: 0 16px;
                height: 44px;
                min-width: 155px; 
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                z-index: 101 !important;
            }
            .custom-month-selector:hover { border-color: #3b82f6; background: #f8fafc; }
            .custom-month-selector.active { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
            .custom-month-selector .month-name { font-weight: 800; color: #1e293b; font-size: 0.9rem; flex: 1; text-align: center; }
            .custom-month-selector svg { color: #3b82f6; width: 14px; height: 14px; }
            
            /* Custom Month List Panel */
            .custom-month-list {
                position: absolute;
                top: calc(100% + 8px);
                left: 0;
                width: 100%;
                background: #ffffff !important;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                z-index: 9999999 !important; /* Ensure it stays above days */
                display: none;
                padding: 6px;
                max-height: 280px;
                overflow-y: auto;
            }
            .custom-month-list div {
                padding: 10px 14px;
                border-radius: 10px;
                font-size: 0.85rem;
                color: #475569;
                cursor: pointer;
                transition: all 0.15s;
                font-weight: 600;
            }
            .custom-month-list div:hover { background: #eff6ff; color: #3b82f6; }
            .custom-month-list div.active { background: #3b82f6; color: #ffffff; font-weight: 700; }
            
            /* Year Input - Figma Precision */
            .numInputWrapper { 
                width: 85px !important;
                height: 44px !important;
                background: #ffffff;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                padding: 0 !important;
                transition: all 0.2s ease;
                display: flex !important;
                align-items: center;
                justify-content: center;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                overflow: visible !important;
            }
            .numInputWrapper:hover, .numInputWrapper:focus-within { border-color: #3b82f6; }
            .numInputWrapper:focus-within { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
            .numInputWrapper input { 
                font-weight: 800 !important; 
                color: #1e293b !important; 
                font-size: 0.95rem !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                text-align: center !important;
                background: transparent !important;
                border: none !important;
                outline: none !important;
            }
            .numInputWrapper span { display: none !important; }

            /* Navigation Buttons - Locked 44px */
            .flatpickr-prev-month, .flatpickr-next-month {
                position: static !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                height: 44px !important;
                width: 44px !important;
                border-radius: 12px !important;
                background: #ffffff !important;
                border: 2px solid #f1f5f9 !important;
                transition: all 0.2s ease;
                z-index: 10;
            }
            .flatpickr-prev-month:hover, .flatpickr-next-month:hover { 
                background: #eff6ff !important; 
                border-color: #3b82f6 !important;
                transform: translateY(-1px);
            }
            .flatpickr-prev-month svg, .flatpickr-next-month svg { width: 14px !important; height: 14px !important; fill: #3b82f6 !important; }
            
            /* Weekdays & Days Polishing */
            .flatpickr-weekday { color: #94a3b8; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 16px 0; }
            
            /* Default: Current Month Days (Deep & Circular) */
            .flatpickr-day { 
                border-radius: 9999px !important; /* PERFECT CIRCLE */
                color: #0f172a !important; /* Deepest Slate */
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
                border: 2px solid transparent !important; 
                height: 40px !important; /* Adjusted slightly for perfect circle in 44px cell */
                line-height: 36px !important; 
                font-weight: 800 !important; 
                width: 40px !important;
                margin: 2px auto !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .flatpickr-day.today { 
                background: #eff6ff !important; 
                color: #3b82f6 !important; 
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            }
            
            /* Non-current Month Days - Extreme Transition */
            .flatpickr-day.prevMonthDay, 
            .flatpickr-day.nextMonthDay,
            .flatpickr-day.prevMonthDay.inRange,
            .flatpickr-day.nextMonthDay.inRange {
                color: #94a3b8 !important; /* Neutral Gray */
                opacity: 0.45 !important; /* Slightly more visible */
                font-weight: 400 !important;
                background: transparent !important;
                border-color: transparent !important;
                pointer-events: all; /* Re-enable selection as requested */
            }
            .flatpickr-day.prevMonthDay:hover, .flatpickr-day.nextMonthDay:hover {
                background: #f1f5f9 !important;
                opacity: 0.8 !important;
                color: #94a3b8 !important;
            }

            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { 
                background: #3b82f6 !important; 
                color: #ffffff !important;
                box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
                opacity: 1 !important;
                border-color: #3b82f6 !important;
            }
            .flatpickr-day.inRange { 
                background: #f1f7ff !important; 
                color: #3b82f6 !important; 
                border-radius: 0 !important; /* Keep range segments square-ish for continuity */
                opacity: 1 !important;
            }
            .flatpickr-day.startRange { border-radius: 9999px 0 0 9999px !important; }
            .flatpickr-day.endRange { border-radius: 0 9999px 9999px 0 !important; }
            .flatpickr-day.selected.startRange.endRange { border-radius: 9999px !important; }

            .flatpickr-day:not(.selected):not(.prevMonthDay):not(.nextMonthDay):hover { 
                background: #f1f5f9; 
                border-color: #e2e8f0;
                transform: scale(1.1); 
            }
        </style>
    @endpush
