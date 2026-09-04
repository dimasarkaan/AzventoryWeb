<?php

// Test script to debug regex protection

$desc = "Nama lengkap barang inventaris. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\\s\\.\\,\\&\\-\\(\\)\\/''\"]*$/. harus memiliki minimal 3 karakter. tidak boleh lebih dari 255 karakter.";

echo "=== Original ===\n$desc\n\n";

// Proteksi regex patterns
$regexPlaceholders = [];
$protected = preg_replace_callback('/\/\^[^$]+\$\//', function ($m) use (&$regexPlaceholders) {
    $key = '###REGEX_'.count($regexPlaceholders).'###';
    $regexPlaceholders[$key] = $m[0];
    echo "Matched regex: $m[0]\n";

    return $key;
}, $desc);

echo "\n=== After protection ===\n$protected\n\n";
echo 'Placeholders: '.count($regexPlaceholders)."\n\n";

// Split
$parts = explode('. ', $protected);
echo '=== Parts ('.count($parts).") ===\n";
foreach ($parts as $i => $part) {
    echo "$i: $part\n";
}
