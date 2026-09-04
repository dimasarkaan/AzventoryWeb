<?php

// 1. Update config/scribe.php
$scribeFile = 'config/scribe.php';
$scribeContent = file_get_contents($scribeFile);

$scribeContent = preg_replace(
    "/'description' => 'Platform Manajemen Inventaris Kelas Enterprise'/",
    "'description' => 'Dokumentasi resmi API Azventory'",
    $scribeContent
);

$newIntro = <<<'INTRO'
Selamat datang di Dokumentasi API **Azventory**.

Halaman ini berisi panduan untuk menggunakan API Azventory. Anda dapat menggunakan API ini untuk mengintegrasikan sistem inventaris dengan aplikasi lain (misalnya aplikasi web, kasir, atau seluler).

Di dokumentasi ini, Anda akan menemukan daftar URL (*endpoint*) yang tersedia, data apa saja yang perlu dikirim (*parameter*), serta contoh balasan (*response*) dari sistem kami.

**Autentikasi (Token)**  
Sebagian besar fungsi API ini memerlukan proses login. Pastikan Anda menyertakan *header* `Authorization: Bearer {token}` di setiap permintaan (*request*) yang dikirimkan.
INTRO;

$scribeContent = preg_replace(
    "/Selamat datang di Pusat Dokumentasi API \*\*Azventory\*\*! 🚀.*?yang Anda kirimkan\.\s*/s",
    $newIntro."\n",
    $scribeContent
);

file_put_contents($scribeFile, $scribeContent);
echo "Updated config/scribe.php\n";

// 2. Update Controllers
$descriptions = [
    'Manajemen Pengguna' => "Bagian ini berisi API untuk mengelola data staf atau karyawan yang bisa masuk (login) ke aplikasi Azventory.\n\nMelalui API ini, Anda bisa menambahkan pengguna baru, mengubah profil mereka, mengatur ulang kata sandi, serta menentukan level akses atau peran masing-masing pengguna (seperti Superadmin, Admin, atau Operator).",

    'Sistem & Notifikasi' => "API ini digunakan untuk mengambil data pemberitahuan dari sistem.\n\nNotifikasi ini biasanya berisi pesan pengingat seperti stok barang yang mulai habis atau informasi batas waktu pengembalian barang yang sedang dipinjam.",

    'Laporan & Aktivitas' => "Modul ini mencatat riwayat penggunaan aplikasi.\n\nAnda bisa melihat log aktivitas pengguna (siapa yang mengubah data, kapan, dan apa yang diubah) serta mengambil ringkasan statistik aplikasi, seperti jumlah total barang dan persentase barang yang rusak.",

    'Profil Pengguna' => "API ini khusus disediakan untuk pengguna yang sedang login agar bisa melihat dan mengatur informasi akun mereka sendiri.\n\nPengguna dapat memperbarui alamat email, mengganti foto profil, atau mengubah kata sandi mereka secara mandiri.",

    'Manajemen Inventaris' => "Ini adalah fungsi utama aplikasi untuk mengelola barang inventaris.\n\nAnda bisa menggunakan kumpulan API ini untuk menambah daftar barang baru ke sistem, melihat sisa stok, memperbarui spesifikasi barang, serta menyesuaikan jumlah stok saat ada barang masuk atau keluar.",

    'Peminjaman Barang' => "Bagian ini digunakan untuk mencatat alur peminjaman aset perusahaan.\n\nAPI ini melacak siapa yang meminjam barang, berapa jumlahnya, kapan barang harus dikembalikan, dan bagaimana kondisi barang saat dikembalikan ke perusahaan.",

    'Master Data' => "API ini digunakan untuk mengatur data-data dasar aplikasi, seperti daftar Merk, Kategori, dan Lokasi Cabang.\n\nData-data pendukung ini wajib diisi terlebih dahulu sebelum Anda bisa menambahkan daftar barang baru ke dalam sistem.",

    'Manajemen Akses (Auth)' => "API ini bertugas menangani proses login dan logout pengguna.\n\nSaat login berhasil, sistem akan mengembalikan sebuah Token. Token inilah yang nantinya dipakai sebagai tanda pengenal untuk mengakses API lainnya di aplikasi ini.",
];

$files = [
    'app/Http/Controllers/Api/AuthController.php' => 'Manajemen Akses (Auth)',
    'app/Http/Controllers/Inventory/BrandController.php' => 'Master Data',
    'app/Http/Controllers/Inventory/CategoryController.php' => 'Master Data',
    'app/Http/Controllers/Inventory/LocationController.php' => 'Master Data',
    'app/Http/Controllers/Notifications/NotificationController.php' => 'Sistem & Notifikasi',
    'app/Http/Controllers/Inventory/Api/UserController.php' => 'Manajemen Pengguna',
    'app/Http/Controllers/Inventory/Api/ActivityLogController.php' => 'Laporan & Aktivitas',
    'app/Http/Controllers/Inventory/Api/StatsController.php' => 'Laporan & Aktivitas',
    'app/Http/Controllers/Inventory/Api/ProfileController.php' => 'Profil Pengguna',
    'app/Http/Controllers/Inventory/Api/InventoryController.php' => 'Manajemen Inventaris',
    'app/Http/Controllers/Inventory/Api/BorrowingController.php' => 'Peminjaman Barang',
];

foreach ($files as $file => $groupName) {
    if (! file_exists($file)) {
        continue;
    }

    $content = file_get_contents($file);
    $desc = $descriptions[$groupName];

    // Format description with " * "
    $formattedDesc = '';
    $descLines = explode("\n", $desc);
    foreach ($descLines as $line) {
        if (trim($line) === '') {
            $formattedDesc .= " * \n";
        } else {
            $formattedDesc .= ' * '.$line."\n";
        }
    }

    // Replace class docblock description
    // Pattern finds @group {GroupName} followed by any lines starting with * until */
    $pattern = '/(\*\s*@group\s+'.preg_quote($groupName, '/').'\s*\n)(?:\s*\*\s*.*?\n)*(?=\s*\*\/)/is';

    if (preg_match($pattern, $content)) {
        $replacement = "$1 * \n".rtrim($formattedDesc, "\n")."\n";
        $content = preg_replace($pattern, $replacement, $content, 1);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
