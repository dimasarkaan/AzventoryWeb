<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizationService
{
    protected $manager;

    // Inisialisasi ImageManager dengan driver GD.
    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimasi, resize, dan simpan gambar.
     * 
     * @return string Path relatif gambar yang disimpan
     */
    public function optimizeAndSave(UploadedFile $file, string $folder, int $maxWidth = 2000, int $quality = 95): string
    {
        // Generate a unique filename with .webp extension
        $filename = Str::random(40) . '.webp';
        $path = $folder . '/' . $filename;
        
        // Ensure directory exists
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        // Read and process the image
        $image = $this->manager->read($file);

        // Resize only if width is greater than maxWidth, maintaining aspect ratio
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        // Encode to WebP
        $encoded = $image->toWebp($quality);

        // Save directly to storage
        // Note: Intervention save() saves to a local path. 
        // We use Storage facade to be compatible with potential S3/Cloud storage in future
        // casting encoded to string gives the binary data
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
