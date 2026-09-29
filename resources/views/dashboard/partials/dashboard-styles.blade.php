    @push('styles')
        <style>
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
        </style>
    @endpush
