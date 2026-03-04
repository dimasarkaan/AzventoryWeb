{{-- Footer (fixed, muncul di setiap halaman) --}}
<div class="pdf-footer">
    <div class="pdf-footer-left">
        <div style="display:inline-block; vertical-align: middle; margin-right: 6px; margin-bottom: -3px;">
            @php
                $svgPath = public_path('images/logo/logo_azventory.svg');
                if (file_exists($svgPath)) {
                    $svgData = base64_encode(file_get_contents($svgPath));
                    echo '<img src="data:image/svg+xml;base64,'.$svgData.'" style="width: 14px; height: 14px;" alt="Logo">';
                }
            @endphp
        </div>
        <span style="vertical-align: middle;">Azventory &bull; Dicetak oleh: {{ auth()->user()->name }} &bull; {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
    </div>
    <div class="pdf-footer-right"></div>
</div>

{{-- Company Header --}}
<div class="pdf-company-header">
    <div class="pdf-company-logo-cell">
        <img src="{{ public_path('images/logo/logo_azzahracomputer.png') }}" style="max-height: 50px; width: auto;" alt="Azzahra Computer">
    </div>
    <div class="pdf-company-info-cell">
        <div class="pdf-company-name">AZZAHRA COMPUTER</div>
        <div class="pdf-company-tagline">Solusi Teknologi Terpercaya &bull; Laporan Resmi Inventaris</div>
    </div>
</div>

{{-- Report Title --}}
<div class="pdf-report-title">
    <h1>{{ $title }}</h1>
    <div class="pdf-report-meta">
        @if(!empty($startDate) && !empty($endDate))
            Periode: {{ $startDate->translatedFormat('d F Y') }} &mdash; {{ $endDate->translatedFormat('d F Y') }}
        @else
            Periode: Semua Data
        @endif
        &nbsp;&bull;&nbsp;
        Lokasi: {{ empty($location) || $location == 'all' ? 'Semua Lokasi' : $location }}
    </div>
</div>
