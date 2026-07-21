<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Service (Pekerja Keras) khusus untuk mengurus foto/gambar yang diunggah pengguna.
// Bertugas mengecilkan ukuran gambar (kompresi) dan mengubah formatnya ke WebP agar memori server tidak cepat penuh.
class ImageOptimizationService
{
    protected $manager;

    // Tahap Persiapan: Menyiapkan alat pemroses gambar (GD Library).
    // Menggunakan teknik panggil-saat-butuh (Lazy Load) agar aplikasi tidak berat saat pertama kali dijalankan.
    protected function getManager()
    {
        if (! $this->manager) {
            if (! extension_loaded('gd')) {
                return null;
            }
            $this->manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver);
        }

        return $this->manager;
    }

    // Proses Inti: Memotong ukuran jika terlalu besar, memadatkan file, lalu menyimpan sebagai file WebP.
    // Mengembalikan alamat letak file tersebut disimpan.
    public function optimizeAndSave(UploadedFile $file, string $folder, int $maxWidth = 1000, int $quality = 80): string
    {
        if (! Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $manager = $this->getManager();

        // Fallback: jika GD tidak tersedia, simpan file apa adanya tanpa optimasi
        if (! $manager) {
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::random(40).'.'.$extension;
            $path = $folder.'/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file));

            return $path;
        }

        $filename = Str::random(40).'.webp';
        $path = $folder.'/'.$filename;

        $image = $manager->read($file);

        // Resize otomatis jika lebar gambar melebihi batas maksimal, tetap menjaga aspek rasio.
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        $encoded = $image->toWebp($quality);

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
