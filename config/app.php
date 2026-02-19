<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini adalah nama aplikasi Anda, yang akan digunakan ketika
    | framework perlu menempatkan nama aplikasi dalam notifikasi atau
    | elemen UI lainnya di mana nama aplikasi perlu ditampilkan.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Lingkungan Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini menentukan "lingkungan" aplikasi Anda saat ini berjalan.
    | Ini mungkin menentukan bagaimana Anda lebih suka mengonfigurasi berbagai
    | layanan yang digunakan aplikasi. Atur ini di file ".env" Anda.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mode Debug Aplikasi
    |--------------------------------------------------------------------------
    |
    | Ketika aplikasi Anda dalam mode debug, pesan kesalahan terperinci dengan
    | stack trace akan ditampilkan pada setiap kesalahan yang terjadi dalam
    | aplikasi Anda. Jika dinonaktifkan, halaman kesalahan umum yang sederhana akan ditampilkan.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL Aplikasi
    |--------------------------------------------------------------------------
    |
    | URL ini digunakan oleh console untuk menghasilkan URL dengan benar saat menggunakan
    | alat baris perintah Artisan. Anda harus mengatur ini ke root dari
    | aplikasi sehingga tersedia dalam perintah Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Zona Waktu Aplikasi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan zona waktu default untuk aplikasi Anda, yang
    | akan digunakan oleh fungsi tanggal dan waktu PHP. Zona waktu
    | disetel ke "UTC" secara default karena cocok untuk sebagian besar kasus penggunaan.
    |
    */

    'timezone' => 'Asia/Jakarta',

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Lokal Aplikasi
    |--------------------------------------------------------------------------
    |
    | Lokal aplikasi menentukan lokal default yang akan digunakan
    | oleh metode terjemahan / lokalisasi Laravel. Opsi ini dapat
    | diatur ke lokal apa pun yang Anda rencanakan untuk memiliki string terjemahan.
    |
    */

    'locale' => env('APP_LOCALE', 'id'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Kunci Enkripsi
    |--------------------------------------------------------------------------
    |
    | Kunci ini digunakan oleh layanan enkripsi Laravel dan harus disetel
    | ke string acak 32 karakter untuk memastikan bahwa semua nilai yang dienkripsi
    | aman. Anda harus melakukan ini sebelum men-deploy aplikasi.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Mode Pemeliharaan
    |--------------------------------------------------------------------------
    |
    | Opsi konfigurasi ini menentukan driver yang digunakan untuk menentukan dan
    | mengelola status "mode pemeliharaan" Laravel. Driver "cache" bunda
    | memungkinkan mode pemeliharaan dikontrol di beberapa mesin.
    |
    | Driver yang didukung: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
