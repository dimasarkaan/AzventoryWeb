<?php // Script to scale and add padding to logo.png

$dir = __DIR__;
$srcFile = $dir . '/public/logo.png';
$bkpFile = $dir . '/public/logo_backup.png';

if (!file_exists($srcFile)) {
    echo "Error: logo.png not found.\n";
    exit(1);
}

// Backup just in case
if (!file_exists($bkpFile)) {
    copy($srcFile, $bkpFile);
}

// Create image from PNG
$srcImage = imagecreatefrompng($srcFile);

if (!$srcImage) {
    echo "Failed to read image.\n";
    exit(1);
}

// Get dimensions
$srcWidth = imagesx($srcImage);
$srcHeight = imagesy($srcImage);

// Create a new empty image with same dimension
$destImage = imagecreatetruecolor($srcWidth, $srcHeight);

// Enable alpha channel for destination
imagealphablending($destImage, false);
imagesavealpha($destImage, true);

// Fill with transparent background
$transparent = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
imagefill($destImage, 0, 0, $transparent);

// Calculate new size (scale down to 60% relative to frame to add good margin)
$scale = 0.60;
$newWidth = $srcWidth * $scale;
$newHeight = $srcHeight * $scale;

// Center coordinates
$dstX = ($srcWidth - $newWidth) / 2;
$dstY = ($srcHeight - $newHeight) / 2;

// Resize onto transparent background (to make it smaller inside its own canvas)
imagecopyresampled(
    $destImage, $srcImage,
    $dstX, $dstY,
    0, 0,
    $newWidth, $newHeight,
    $srcWidth, $srcHeight
);

// Save back to PNG
imagepng($destImage, $srcFile);

imagedestroy($srcImage);
imagedestroy($destImage);

echo "Logo successfully padded and scaled to 60%.\n";
?>
