
<div class="pdf-footer">
    <div class="pdf-footer-left">
        <div style="display:inline-block; vertical-align: middle; margin-right: 6px; margin-bottom: -3px;">
            <?php
                $svgPath = public_path('images/logo/logo_azventory.svg');
                if (file_exists($svgPath)) {
                    $svgData = base64_encode(file_get_contents($svgPath));
                    echo '<img src="data:image/svg+xml;base64,'.$svgData.'" style="width: 14px; height: 14px;" alt="Logo">';
                }
            ?>
        </div>
        <span style="vertical-align: middle;">Azventory &bull; Dicetak oleh: <?php echo e(auth()->user()->name); ?> &bull; <?php echo e(now()->translatedFormat('d F Y, H:i')); ?> WIB</span>
    </div>
    <div class="pdf-footer-right"></div>
</div>


<div class="pdf-company-header">
    <div class="pdf-company-logo-cell">
        <img src="<?php echo e(public_path('images/logo/logo_azzahracomputer.png')); ?>" style="max-height: 50px; width: auto;" alt="Azzahra Computer">
    </div>
    <div class="pdf-company-info-cell">
        <div class="pdf-company-name">AZZAHRA COMPUTER</div>
        <div class="pdf-company-tagline">Solusi Teknologi Terpercaya &bull; Laporan Resmi Inventaris</div>
    </div>
</div>


<div class="pdf-report-title">
    <h1><?php echo e($title); ?></h1>
    <div class="pdf-report-meta">
        <?php if(!empty($startDate) && !empty($endDate)): ?>
            Periode: <?php echo e($startDate->translatedFormat('d F Y')); ?> &mdash; <?php echo e($endDate->translatedFormat('d F Y')); ?>

        <?php else: ?>
            Periode: Semua Data
        <?php endif; ?>
        &nbsp;&bull;&nbsp;
        Lokasi: <?php echo e(empty($location) || $location == 'all' ? 'Semua Lokasi' : $location); ?>

    </div>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/reports/partials/pdf_header.blade.php ENDPATH**/ ?>