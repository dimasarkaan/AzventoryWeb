{{--
    Partial: PDF Document Header (company brand + report metadata + page numbering)
    Digunakan oleh semua template PDF laporan.
    
    Variabel yang diperlukan:
    - $title       : Judul laporan
    - $startDate   : Carbon atau null
    - $endDate     : Carbon atau null
    - $location    : string ('all' atau nama lokasi)
--}}

@php
    if (!function_exists('getBase64Image')) {
        function getBase64Image($path) {
            if (file_exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $mime = $ext === 'svg' ? 'svg+xml' : $ext;
                $data = file_get_contents($path);
                return 'data:image/' . $mime . ';base64,' . base64_encode($data);
            }
            return '';
        }
    }
    $headerLogo = getBase64Image(public_path('images/logo/logo_azzahracomputer.png'));
    $footerLogo = getBase64Image(public_path('logo.svg'));
@endphp

<style>
    /* ===== Global Reset ===== */
    div, span, h1, h2, h3, p, table, th, td { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #1f2937; }

    /* ===== Company Header ===== */
    .pdf-company-header {
        display: table;
        width: 100%;
        border-bottom: 3px solid #2563eb;
        padding-bottom: 8px;
        margin-bottom: 6px;
    }
    .pdf-company-logo-cell { display: table-cell; width: 140px; vertical-align: middle; }
    .pdf-company-info-cell { display: table-cell; vertical-align: middle; padding-left: 15px; }
    .pdf-company-name { font-size: 16pt; font-weight: bold; color: #2563eb; letter-spacing: 1px; }
    .pdf-company-tagline { font-size: 9pt; color: #6b7280; margin-top: 2px; }

    /* ===== Report Title Block ===== */
    .pdf-report-title {
        text-align: center;
        margin: 10px 0 6px 0;
        padding: 8px 0;
        border-bottom: 1px solid #d1d5db;
    }
    .pdf-report-title h1 { font-size: 14pt; font-weight: bold; text-transform: uppercase; color: #111827; }
    .pdf-report-meta { font-size: 8.5pt; color: #6b7280; margin-top: 3px; }

    /* ===== Table ===== */
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-top: 12px; }
    th, td { border: 1px solid #9ca3af; padding: 8px 10px; text-align: left; vertical-align: middle; }
    th { background-color: #2563eb; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 8.5pt; text-align: center; }
    tr:nth-child(even) { background-color: #f9fafb; }

    /* ===== Badges ===== */
    .badge { font-size: 7.5pt; padding: 4px 8px; border-radius: 4px; border: 1px solid transparent; display: inline-block; white-space: nowrap; font-weight: bold; text-align: center; }
    .badge-success { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
    .badge-warning { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
    .badge-danger  { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
    .text-success  { color: #059669; font-weight: bold; }
    .text-danger   { color: #dc2626; font-weight: bold; }

    /* ===== Page Footer (CSS counter-based page number) ===== */
    @page { 
        margin-top: 10mm;
        margin-right: 20mm;
        margin-bottom: 25mm;
        margin-left: 20mm;
    }
    .pdf-footer {
        position: fixed;
        bottom: -10mm; /* Naik sedikit agar teks tidak menyentuh ujung kertas */
        left: 0; right: 0;
        border-top: 1px solid #d1d5db;
        padding-top: 6px;
        font-size: 7.5pt;
        color: #9ca3af;
        display: table;
        width: 100%;
    }
    .pdf-footer-left  { display: table-cell; text-align: left; vertical-align: middle; }
    .pdf-footer-right { display: table-cell; text-align: right; vertical-align: middle; }
    .pdf-footer-right:after { 
        content: counter(page, decimal-leading-zero); 
        font-weight: bold; 
        color: #9ca3af; 
    }
</style>
