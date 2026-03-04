<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * ImageOptimizationService menangani kompresi, resize, dan konversi gambar ke format WebP.
 */
class ImageOptimizationService
{
    protected $manager;

    /**
     * Inisialisasi ImageManager dengan driver GD.
     */
    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Mengoptimasi, mengubah ukuran, dan menyimpan gambar hasil upload sebagai file WebP.
     *
     * @return string Path relatif file yang disimpan di storage.
     */
    public function optimizeAndSave(UploadedFile $file, string $folder, int $maxWidth = 1000, int $quality = 80): string
    {
        $filename = Str::random(40).'.webp';
        $path = $folder.'/'.$filename;

        if (! Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $image = $this->manager->read($file);

        // Resize otomatis jika lebar gambar melebihi batas maksimal, tetap menjaga aspek rasio.
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        $encoded = $image->toWebp($quality);

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
