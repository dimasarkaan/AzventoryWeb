<?php

$controllers = [
    'app/Http/Controllers/Api/AuthController.php' => [
        'group' => 'Manajemen Akses (Auth)',
        'desc' => "API ini menangani proses login dan logout.\n\nSaat berhasil login, sistem akan memberikan token yang digunakan untuk mengakses fitur-fitur lain di aplikasi.",
    ],
    'app/Http/Controllers/Inventory/BrandController.php' => [
        'group' => 'Master Data',
        'desc' => 'API ini digunakan untuk mengatur data dasar aplikasi seperti daftar Merk, Kategori, dan Lokasi Cabang. Data ini diperlukan sebelum Anda bisa menambahkan barang baru ke dalam sistem.',
    ],
    'app/Http/Controllers/Inventory/CategoryController.php' => [
        'group' => 'Master Data',
        'desc' => 'API ini digunakan untuk mengatur data dasar aplikasi seperti daftar Merk, Kategori, dan Lokasi Cabang. Data ini diperlukan sebelum Anda bisa menambahkan barang baru ke dalam sistem.',
    ],
    'app/Http/Controllers/Inventory/LocationController.php' => [
        'group' => 'Master Data',
        'desc' => 'API ini digunakan untuk mengatur data dasar aplikasi seperti daftar Merk, Kategori, dan Lokasi Cabang. Data ini diperlukan sebelum Anda bisa menambahkan barang baru ke dalam sistem.',
    ],
    'app/Http/Controllers/Notifications/NotificationController.php' => [
        'group' => 'Sistem & Notifikasi',
        'desc' => "API ini digunakan untuk mengambil pemberitahuan otomatis dari sistem.\n\nNotifikasi ini biasanya berisi info penting seperti pengingat saat stok barang mulai habis atau batas waktu pengembalian barang pinjaman.",
    ],
    'app/Http/Controllers/Inventory/Api/UserController.php' => [
        'group' => 'Manajemen Pengguna',
        'desc' => 'Bagian ini berisi API untuk mengelola akun staf atau karyawan yang bisa login ke aplikasi Azventory. Anda bisa menambahkan pengguna baru, mengubah data mereka, mengatur ulang kata sandi, serta menentukan peran masing-masing pengguna (Superadmin, Admin, Operator, dll).',
    ],
    'app/Http/Controllers/Inventory/Api/ActivityLogController.php' => [
        'group' => 'Laporan & Aktivitas',
        'desc' => "Modul ini mencatat riwayat penggunaan aplikasi.\n\nAnda bisa melihat log aktivitas pengguna (siapa yang mengubah data, kapan, dan apa yang diubah) serta mengambil ringkasan statistik seperti total barang dan stok yang menipis.",
    ],
    'app/Http/Controllers/Inventory/Api/StatsController.php' => [
        'group' => 'Laporan & Aktivitas',
        'desc' => "Modul ini mencatat riwayat penggunaan aplikasi.\n\nAnda bisa melihat log aktivitas pengguna (siapa yang mengubah data, kapan, dan apa yang diubah) serta mengambil ringkasan statistik seperti total barang dan stok yang menipis.",
    ],
    'app/Http/Controllers/Inventory/Api/ProfileController.php' => [
        'group' => 'Profil Pengguna',
        'desc' => 'API ini khusus untuk pengguna yang sedang login agar bisa melihat dan mengubah informasi profil mereka sendiri, termasuk mengubah foto dan kata sandi.',
    ],
    'app/Http/Controllers/Inventory/Api/InventoryController.php' => [
        'group' => 'Manajemen Inventaris',
        'desc' => "Ini adalah fungsi inti aplikasi untuk mengelola barang.\n\nAnda bisa menggunakan API ini untuk menambah daftar barang baru, melihat stok, memperbarui informasi barang, serta mencatat riwayat barang masuk atau keluar.",
    ],
    'app/Http/Controllers/Inventory/Api/BorrowingController.php' => [
        'group' => 'Peminjaman Barang',
        'desc' => "Bagian ini digunakan untuk mencatat peminjaman barang inventaris.\n\nAPI ini melacak siapa yang meminjam, kapan barang harus dikembalikan, dan bagaimana kondisi barang saat dikembalikan.",
    ],
];

foreach ($controllers as $file => $data) {
    if (! file_exists($file)) {
        continue;
    }
    $content = file_get_contents($file);

    // Create docblock
    $descLines = explode("\n", $data['desc']);
    $docblock = "/**\n * @group ".$data['group']."\n *\n";
    foreach ($descLines as $line) {
        $docblock .= ' * '.$line."\n";
    }
    $docblock .= " */\nclass ";

    // Remove existing class docblocks if any
    $content = preg_replace('/(\/\*\*.*?\*\/[\s\n]*)*class /is', 'class ', $content);
    // Inject the new docblock
    $content = str_replace('class ', $docblock, $content);

    file_put_contents($file, $content);
}
echo "Class group descriptions updated.\n";
