<?php

namespace App\Services;

use App\Models\Sparepart;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeService
{
    // Generate dan simpan QR Code standar.
    public function generate(Sparepart $sparepart)
    {
        $options = new QROptions(['outputBase64' => false]);
        $qrCodeUrl = route('inventory.show', $sparepart);
        $qrCodeOutput = (new QRCode($options))->render($qrCodeUrl);
        
        $qrCodePath = 'qrcodes/' . $sparepart->part_number . '_' . $sparepart->id . '.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeOutput);

        $sparepart->update(['qr_code_path' => $qrCodePath]);
        
        return $qrCodePath;
    }

    // Generate SVG label siap cetak dengan QR code.
    public function generateLabelSvg(Sparepart $inventory)
    {
        if (!$inventory->qr_code_path || !Storage::disk('public')->exists($inventory->qr_code_path)) {
            // Regenerate if missing
            $this->generate($inventory);
        }

        // 1. Define Dimensions (33mm x 15mm @ 96DPI)
        $width = 125;  // ~33mm
        $height = 57;  // ~15mm
        $qrSize = 45;
        $qrMargin = ($height - $qrSize) / 2;

        // 2. Generate clean QR for embedding (ensure known dimensions/viewBox)
        $options = new QROptions([
            'outputBase64' => false,
            'imageTransparent' => false,
        ]);
        $freshQr = (new QRCode($options))->render(route('inventory.show', $inventory));
        
        // Extract inner content and viewBox
        preg_match('/viewBox="([^"]+)"/', $freshQr, $vbMatches);
        $qrViewBox = $vbMatches[1] ?? '0 0 53 53';
        
        preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $freshQr, $contentMatches);
        $cleanInner = $contentMatches[1] ?? '';

        // 3. Construct Final SVG
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="33mm" height="15mm" viewBox="0 0 ' . $width . ' ' . $height . '" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <!-- Background with Border Stroke -->
    <rect x="0.5" y="0.5" width="' . ($width - 1) . '" height="' . ($height - 1) . '" fill="white" stroke="black" stroke-width="0.5" rx="3" ry="3"/>
    
    <!-- QR Code (Left) -->
    <svg x="' . $qrMargin . '" y="' . $qrMargin . '" width="' . $qrSize . '" height="' . $qrSize . '" viewBox="' . $qrViewBox . '">
        ' . $cleanInner . '
    </svg>

    <!-- Text (Right) -->
    <g font-family="sans-serif" fill="black">
        <text x="55" y="18" font-size="5" font-weight="bold" fill="#555">PART NUMBER</text>
        <text x="55" y="29" font-size="8" font-family="monospace" font-weight="bold">' . htmlspecialchars($inventory->part_number) . '</text>
        <text x="55" y="40" font-size="6">' . htmlspecialchars(Str::limit($inventory->name, 20)) . '</text>
    </g>
</svg>';

        return $svg;
    }
    public function getLabelFilename(Sparepart $inventory)
    {
        $cat = Str::title($inventory->category);
        $brand = Str::title($inventory->brand);
        $pn = strtoupper($inventory->part_number);
        
        $catSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $cat);
        $brandSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $brand);
        $pnSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $pn);

        return "Label-{$catSlug}-{$brandSlug}-{$pnSlug}.svg";
    }
}
