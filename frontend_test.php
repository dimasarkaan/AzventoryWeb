<?php

$filesToCheck = [
    'resources/views/superadmin/inventory/index.blade.php',
];

echo "--- STARTING FRONTEND VERIFICATION ---\n";

foreach ($filesToCheck as $file) {
    echo "Checking $file... ";
    $content = file_get_contents(__DIR__ . '/' . $file);
    
    // Check for loading="lazy"
    $lazyCount = substr_count($content, 'loading="lazy"');
    $decodingCount = substr_count($content, 'decoding="async"');
    
    if ($lazyCount >= 2 && $decodingCount >= 2) {
        echo "OK (Found $lazyCount lazy tags, $decodingCount async tags)\n";
    } else {
        echo "FAILED (Missing attributes. Found lazy: $lazyCount)\n";
    }
}

echo "\nChecking vite.config.js... ";
$viteContent = file_get_contents(__DIR__ . '/vite.config.js');
if (strpos($viteContent, 'manualChunks') !== false) {
    echo "OK (manualChunks found)\n";
} else {
    echo "FAILED (manualChunks missing)\n";
}

echo "\n--- VERIFICATION COMPLETE ---\n";
