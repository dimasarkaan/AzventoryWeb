# Panduan Pembaruan Sistem Azventory (Tim IT)

Ikuti langkah-langkah berikut secara berurutan melalui **SSH Terminal di cPanel**.

---

## Langkah 1 — Ambil Kode Terbaru

```bash
git pull origin main
```

---

## Langkah 2 — Update Paket PHP

```bash
composer install --optimize-autoloader --no-dev
```

---

## Langkah 3 — Jalankan Migrasi Database

```bash
php artisan migrate --force
```

> Perintah ini aman. Data yang sudah ada tidak akan terhapus.

---

## Langkah 4 — Update Konfigurasi di File `.env` *(Jika Diminta Developer)*

> Lewati langkah ini jika developer tidak memberi instruksi khusus.

Developer akan mengirimkan nilai kunci baru via **WhatsApp pribadi**. Buka file `.env` di server (via cPanel File Manager atau SSH), lalu cari dan ganti baris berikut:

```ini
PUSHER_APP_KEY=<nilai yang dikasih developer>
PUSHER_APP_SECRET=<nilai yang dikasih developer>
```

Simpan file setelah selesai.

---

## Langkah 5 — Bersihkan Cache

```bash
php artisan optimize:clear
php artisan view:clear
```

---

## Langkah 6 — Verifikasi

- Buka URL aplikasi Azventory di browser
- Pastikan tidak ada halaman Error 500
- Login dengan akun Superadmin dan cek semua fitur berjalan normal
