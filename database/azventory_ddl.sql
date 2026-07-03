-- ============================================================
-- AZVENTORY - Sistem Informasi Manajemen Inventaris Sparepart
-- DDL (Data Definition Language) - Struktur Database
-- ============================================================
-- Database Engine : MySQL / MariaDB
-- Generated From  : Laravel Migrations (28 file migrasi)
-- Catatan          : Hanya struktur (CREATE TABLE), tanpa data dummy
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- BAGIAN 1: TABEL INTI - PENGGUNA & AUTENTIKASI
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: users
-- Deskripsi: Data pengguna sistem dengan role-based access control
-- Role: superadmin, admin, operator
-- Fitur: Soft Delete (deleted_at)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama lengkap pengguna',
    `username` VARCHAR(255) NOT NULL COMMENT 'Username untuk login',
    `email` VARCHAR(255) NOT NULL COMMENT 'Alamat email',
    `role` ENUM('superadmin', 'admin', 'operator') NOT NULL DEFAULT 'operator' COMMENT 'Role pengguna dalam sistem RBAC',
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu verifikasi email',
    `password` VARCHAR(255) NOT NULL COMMENT 'Password terenkripsi (bcrypt)',
    `password_changed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu terakhir mengubah password',
    `is_username_changed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag apakah username pernah diubah',
    `settings` JSON NULL DEFAULT NULL COMMENT 'Preferensi pengguna (tema, notifikasi, dll.)',
    `jabatan` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Jabatan atau posisi',
    `status` VARCHAR(255) NOT NULL DEFAULT 'aktif' COMMENT 'Status akun: aktif / nonaktif',
    `avatar` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Path file foto profil',
    `phone` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nomor telepon',
    `address` TEXT NULL DEFAULT NULL COMMENT 'Alamat lengkap',
    `remember_token` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Token fitur Remember Me',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft Delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: password_reset_tokens
-- Deskripsi: Token untuk fitur reset password
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL COMMENT 'Email pengguna yang melakukan reset',
    `token` VARCHAR(255) NOT NULL COMMENT 'Token reset password',
    `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu pembuatan token',
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: sessions
-- Deskripsi: Data sesi pengguna yang sedang aktif
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) NOT NULL COMMENT 'Session ID',
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'ID pengguna pemilik sesi',
    `ip_address` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Alamat IP pengguna',
    `user_agent` TEXT NULL DEFAULT NULL COMMENT 'Informasi browser/perangkat',
    `payload` LONGTEXT NOT NULL COMMENT 'Data sesi terenkripsi',
    `last_activity` INT NOT NULL COMMENT 'Timestamp aktivitas terakhir',
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: personal_access_tokens
-- Deskripsi: Token API (Laravel Sanctum) - Polymorphic Relationship
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL COMMENT 'Polymorphic: nama model (App\\Models\\User)',
    `tokenable_id` BIGINT UNSIGNED NOT NULL COMMENT 'Polymorphic: ID dari model',
    `name` TEXT NOT NULL COMMENT 'Nama/label token',
    `token` VARCHAR(64) NOT NULL COMMENT 'Hash token unik',
    `abilities` TEXT NULL DEFAULT NULL COMMENT 'Daftar kemampuan/izin token',
    `last_used_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu terakhir token digunakan',
    `expires_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu kedaluwarsa token',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`),
    KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BAGIAN 2: TABEL MASTER DATA
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: brands
-- Deskripsi: Master data merek/brand barang
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `brands` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama merek',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif merek',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `brands_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: categories
-- Deskripsi: Master data kategori barang
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama kategori',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif kategori',
    `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag kategori default',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: locations
-- Deskripsi: Master data lokasi penyimpanan barang
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `locations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama lokasi penyimpanan',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif lokasi',
    `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag lokasi default',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `locations_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BAGIAN 3: TABEL INTI - INVENTARIS & TRANSAKSI
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: spareparts
-- Deskripsi: Data inventaris barang (sparepart, aset, barang jual)
-- Fitur: Soft Delete (deleted_at)
-- FK: category_id → categories, brand_id → brands, location_id → locations
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `spareparts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama barang',
    `part_number` VARCHAR(255) NOT NULL COMMENT 'Nomor part unik untuk identifikasi',
    `category_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'FK ke tabel categories',
    `brand_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'FK ke tabel brands',
    `location_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'FK ke tabel locations',
    `minimum_stock` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Batas minimum stok (trigger notifikasi)',
    `stock` INT NOT NULL COMMENT 'Jumlah stok saat ini',
    `unit` VARCHAR(255) NOT NULL DEFAULT 'Pcs' COMMENT 'Satuan unit barang',
    `type` ENUM('sale', 'asset') NOT NULL DEFAULT 'sale' COMMENT 'Jenis: sale (barang jual) / asset (aset perusahaan)',
    `condition` VARCHAR(255) NOT NULL COMMENT 'Kondisi barang: Baru, Baik, Rusak, Hilang',
    `age` VARCHAR(50) NOT NULL DEFAULT 'Bekas' COMMENT 'Usia kebaruan: Baru / Bekas',
    `price` DECIMAL(15, 2) NULL DEFAULT NULL COMMENT 'Harga barang',
    `image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Path file gambar barang',
    `qr_code_path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Path file QR Code',
    `color` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Warna barang',
    `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif' COMMENT 'Status keaktifan barang',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft Delete timestamp',
    PRIMARY KEY (`id`),
    KEY `spareparts_part_number_index` (`part_number`),
    KEY `idx_spareparts_category_status` (`category_id`, `status`),
    KEY `idx_spareparts_location_status` (`location_id`, `status`),
    CONSTRAINT `spareparts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `spareparts_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
    CONSTRAINT `spareparts_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: borrowings
-- Deskripsi: Transaksi peminjaman barang oleh personil
-- Fitur: Soft Delete, mendukung pengembalian bertahap (parsial)
-- FK: sparepart_id → spareparts, user_id → users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `borrowings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sparepart_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke barang yang dipinjam',
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'FK ke user yang memvalidasi peminjaman',
    `borrower_name` VARCHAR(255) NOT NULL COMMENT 'Nama peminjam (input manual)',
    `quantity` INT NOT NULL COMMENT 'Jumlah barang yang dipinjam',
    `remaining_quantity` INT NOT NULL DEFAULT 0 COMMENT 'Sisa barang yang belum dikembalikan',
    `borrowed_at` TIMESTAMP NOT NULL COMMENT 'Tanggal peminjaman',
    `expected_return_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Estimasi tanggal pengembalian',
    `returned_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Tanggal pengembalian aktual',
    `notes` TEXT NULL DEFAULT NULL COMMENT 'Catatan peminjaman',
    `status` ENUM('borrowed', 'returned', 'lost') NOT NULL DEFAULT 'borrowed' COMMENT 'Status: borrowed/returned/lost',
    `return_condition` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Kondisi barang saat dikembalikan',
    `return_notes` TEXT NULL DEFAULT NULL COMMENT 'Catatan pengembalian',
    `return_photos` JSON NULL DEFAULT NULL COMMENT 'Foto bukti pengembalian (array JSON)',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft Delete timestamp',
    PRIMARY KEY (`id`),
    KEY `borrowings_sparepart_id_foreign` (`sparepart_id`),
    KEY `borrowings_user_id_foreign` (`user_id`),
    CONSTRAINT `borrowings_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `borrowings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: borrowing_returns
-- Deskripsi: Detail pengembalian barang (mendukung pengembalian parsial)
-- FK: borrowing_id → borrowings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `borrowing_returns` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `borrowing_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke transaksi peminjaman induk',
    `return_date` TIMESTAMP NOT NULL COMMENT 'Tanggal pengembalian',
    `quantity` INT NOT NULL COMMENT 'Jumlah barang yang dikembalikan',
    `condition` VARCHAR(255) NOT NULL COMMENT 'Kondisi barang: good / bad / lost',
    `notes` TEXT NULL DEFAULT NULL COMMENT 'Catatan kondisi pengembalian',
    `photos` JSON NULL DEFAULT NULL COMMENT 'Foto bukti kondisi barang (array JSON)',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `borrowing_returns_borrowing_id_foreign` (`borrowing_id`),
    CONSTRAINT `borrowing_returns_borrowing_id_foreign` FOREIGN KEY (`borrowing_id`) REFERENCES `borrowings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: stock_logs
-- Deskripsi: Audit trail perubahan stok dengan workflow approval
-- Fitur: Soft Delete, approval system (pending → approved/rejected)
-- FK: sparepart_id → spareparts, user_id → users, approved_by → users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stock_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sparepart_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke barang yang mengalami perubahan stok',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke user yang mengajukan perubahan',
    `type` ENUM('masuk', 'keluar') NOT NULL COMMENT 'Jenis perubahan: masuk (restock) / keluar (pengeluaran)',
    `quantity` INT UNSIGNED NOT NULL COMMENT 'Jumlah perubahan stok',
    `reason` VARCHAR(255) NOT NULL COMMENT 'Alasan perubahan stok',
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' COMMENT 'Status persetujuan',
    `approved_by` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'FK ke user yang menyetujui/menolak',
    `rejection_reason` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Alasan penolakan pengajuan',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft Delete timestamp',
    PRIMARY KEY (`id`),
    KEY `stock_logs_sparepart_id_foreign` (`sparepart_id`),
    KEY `stock_logs_user_id_foreign` (`user_id`),
    KEY `stock_logs_approved_by_foreign` (`approved_by`),
    CONSTRAINT `stock_logs_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `stock_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `stock_logs_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BAGIAN 4: TABEL SISTEM - LOG & NOTIFIKASI
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: activity_logs
-- Deskripsi: Riwayat aktivitas pengguna (auto-pruned setelah 365 hari)
-- FK: user_id → users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'FK ke user yang melakukan aktivitas',
    `action` VARCHAR(255) NOT NULL COMMENT 'Jenis aksi: login, create, update, delete, dll.',
    `description` TEXT NOT NULL COMMENT 'Deskripsi detail aktivitas',
    `properties` JSON NULL DEFAULT NULL COMMENT 'Data tambahan kontekstual (JSON)',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `activity_logs_user_id_index` (`user_id`),
    KEY `activity_logs_action_index` (`action`),
    CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: notifications
-- Deskripsi: Notifikasi sistem (low stock, overdue borrowing, dll.)
-- Relasi: Polymorphic One-to-Many ke users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` CHAR(36) NOT NULL COMMENT 'UUID sebagai Primary Key',
    `type` VARCHAR(255) NOT NULL COMMENT 'Class notifikasi (LowStockNotification, OverdueBorrowingNotification, dll.)',
    `notifiable_type` VARCHAR(255) NOT NULL COMMENT 'Polymorphic: nama model target (App\\Models\\User)',
    `notifiable_id` BIGINT UNSIGNED NOT NULL COMMENT 'Polymorphic: ID dari model target',
    `data` TEXT NOT NULL COMMENT 'Payload notifikasi dalam format JSON',
    `read_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu notifikasi dibaca (NULL = belum dibaca)',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`),
    KEY `notifications_unread_index` (`notifiable_id`, `notifiable_type`, `read_at`),
    KEY `notifications_latest_index` (`notifiable_id`, `notifiable_type`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BAGIAN 5: TABEL INFRASTRUKTUR LARAVEL
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: cache
-- Deskripsi: Cache driver database
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) NOT NULL COMMENT 'Cache key',
    `value` MEDIUMTEXT NOT NULL COMMENT 'Nilai cache',
    `expiration` INT NOT NULL COMMENT 'Waktu kedaluwarsa (UNIX timestamp)',
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: cache_locks
-- Deskripsi: Atomic lock untuk cache
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` VARCHAR(255) NOT NULL COMMENT 'Lock key',
    `owner` VARCHAR(255) NOT NULL COMMENT 'Pemilik lock',
    `expiration` INT NOT NULL COMMENT 'Waktu kedaluwarsa lock',
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SELESAI - Total: 14 Tabel
-- ============================================================
-- Tabel Inti Bisnis    (7): users, spareparts, borrowings,
--                           borrowing_returns, stock_logs,
--                           activity_logs, notifications
-- Tabel Master Data    (3): brands, categories, locations
-- Tabel Infrastruktur  (4): password_reset_tokens, sessions,
--                           personal_access_tokens, cache/cache_locks
-- ============================================================
