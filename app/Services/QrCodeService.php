<?php

namespace App\Services;

use App\Models\Sparepart;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Service (Pekerja Keras) khusus untuk melukis kode QR (QR Code).
// Berfungsi ganda: Membuat QR murni, atau membuat desain Stiker Label siap cetak yang ada teksnya.
class QrCodeService
{
    // Membuat file gambar QR Code standar yang jika di-scan oleh HP akan langsung membuka halaman profil barang tersebut.
    public function generate(Sparepart $sparepart)
    {
        $options = new QROptions([
            'outputBase64' => false,
            'eccLevel' => QRCode::ECC_L,
            'quietzoneSize' => 1,
        ]);
        $qrCodeUrl = route('inventory.show', $sparepart);
        $qrCodeOutput = (new QRCode($options))->render($qrCodeUrl);

        $qrCodePath = 'qrcodes/'.$sparepart->part_number.'_'.$sparepart->id.'.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeOutput);

        $sparepart->update(['qr_code_path' => $qrCodePath]);

        return $qrCodePath;
    }

    // Desain Grafis: Menggambar stiker label siap cetak (ukuran 33x15mm).
    // Menggabungkan gambar QR Code di sebelah kiri, dan teks (Part Number, Nama) di sebelah kanan.
    public function generateLabelSvg(Sparepart $inventory)
    {
        if (! $inventory->qr_code_path || ! Storage::disk('public')->exists($inventory->qr_code_path)) {
            $this->generate($inventory);
        }

        // Ukuran label dikonversi ke satuan pixel @96DPI
        $width = 125;  // ~33mm
        $height = 57;  // ~15mm
        $qrSize = 49;  // Tingkatkan sedikit (~13mm) agar lebih mudah dipindai
        $qrMargin = ($height - $qrSize) / 2;

        $options = new QROptions([
            'outputBase64' => false,
            'imageTransparent' => false,
            'eccLevel' => QRCode::ECC_L, // Mengurangi kepadatan titik (modul jadi lebih besar)
            'quietzoneSize' => 1, // Memberi jarak aman putih di sekeliling QR
        ]);
        $freshQr = (new QRCode($options))->render(route('inventory.show', $inventory));

        preg_match('/viewBox="([^"]+)"/', $freshQr, $vbMatches);
        $qrViewBox = $vbMatches[1] ?? '0 0 53 53';

        preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $freshQr, $contentMatches);
        $cleanInner = $contentMatches[1] ?? '';

        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="33mm" height="15mm" viewBox="0 0 '.$width.' '.$height.'" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <!-- Background with Border Stroke -->
    <rect x="0.5" y="0.5" width="'.($width - 1).'" height="'.($height - 1).'" fill="white" stroke="black" stroke-width="0.5" rx="3" ry="3"/>
    
    <!-- QR Code (Left) -->
    <svg x="'.$qrMargin.'" y="'.$qrMargin.'" width="'.$qrSize.'" height="'.$qrSize.'" viewBox="'.$qrViewBox.'">
        '.$cleanInner.'
    </svg>

    <!-- Info Barang (Kanan) -->
    <g font-family="sans-serif" fill="black">
        <text x="58" y="18" font-size="5" font-weight="bold" fill="#555">PART NUMBER</text>
        <text x="58" y="29" font-size="8" font-family="monospace" font-weight="bold">'.htmlspecialchars($inventory->part_number).'</text>
        <text x="58" y="40" font-size="6">'.htmlspecialchars(Str::limit($inventory->name, 20)).'</text>
    </g>
</svg>';

        return $svg;
    }

    // Membersihkan nama file stiker dari spasi atau simbol aneh (Slugify) agar tidak error saat disimpan di Windows/Linux
    public function getLabelFilename(Sparepart $inventory)
    {
        $inventory->loadMissing(['category', 'brand']);
        $cat = Str::title($inventory->category->name ?? '');
        $brand = Str::title($inventory->brand->name ?? '');
        $pn = strtoupper($inventory->part_number);

        $catSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $cat);
        $brandSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $brand);
        $pnSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $pn);

        return "Label-{$catSlug}-{$brandSlug}-{$pnSlug}.svg";
    }
}
