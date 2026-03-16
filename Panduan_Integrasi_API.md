# Panduan Integrasi API Azventory
Dokumen teknis untuk pengembang internal dan pihak ketiga.

Dokumen ini menjelaskan prosedur integrasi aplikasi eksternal (seperti Website, POS, atau Sistem Kasir) dengan server Azventory melalui REST API.

---

## Autentikasi (Bearer Token)
Azventory menggunakan Laravel Sanctum untuk mengamankan akses API. Setiap permintaan harus menyertakan token autentikasi pada bagian header.

### Cara Mendapatkan Token
1. Masuk ke aplikasi Azventory menggunakan akun SuperAdmin.
2. Buka menu Profil, lalu pilih Kunci Akses API.
3. Buat token baru dengan memberikan identitas yang jelas (misalnya: Sistem Kasir Toko).
4. Simpan token tersebut di tempat yang aman karena sistem hanya akan menampilkannya satu kali.

### Struktur Header
Sertakan detail berikut pada setiap header permintaan:
```http
Authorization: Bearer <TOKEN_ANDA>
Accept: application/json
Content-Type: application/json
```

---

## Base URL
Sesuaikan URL dasar dengan alamat server hosting yang digunakan:
- Produksi: https://api.domain-anda.com/api/v1
- Pengembangan: http://localhost:8000/api/v1

---

## Endpoint Utama

### 1. Daftar Katalog Barang
Digunakan untuk mengambil seluruh data inventaris yang berstatus aktif.
- Metode: GET
- Jalur: /inventory
- Parameter Opsional: per_page (jumlah data), page (halaman), search (kata kunci pencarian).

### 2. Detail Barang
Mengambil informasi terperinci mengenai satu item berdasarkan ID.
- Metode: GET
- Jalur: /inventory/{id}

### 3. Penyesuaian Stok
Digunakan untuk menambah atau mengurangi stok barang saat terjadi transaksi eksternal.
- Metode: PUT
- Jalur: /inventory/{id}/adjust-stock
- Payload (JSON):
  ```json
  {
      "type": "decrement", 
      "quantity": 5,
      "description": "Transaksi via Website"
  }
  ```
  Isi field "type" dengan "increment" untuk menambah stok atau "decrement" untuk mengurangi stok.

### 4. Menambah Barang Baru
Mendaftarkan item baru ke dalam katalog sistem.
- Metode: POST
- Jalur: /inventory

---

## Pengujian dengan Postman
Untuk mempercepat proses pengembangan, tersedia file koleksi Postman yang dapat digunakan:
1. Cari file Azventory_API_Collection.postman_collection.json di direktori utama proyek.
2. Impor file tersebut ke dalam aplikasi Postman.
3. Atur variabel base_url dan token pada bagian Variables di dalam koleksi tersebut.

---

## Penanganan Error
Sistem menggunakan kode status HTTP standar untuk menandai status permintaan:
- 200 OK: Berhasil.
- 401 Unauthorized: Token tidak valid atau tidak disertakan.
- 403 Forbidden: Akses ditolak karena izin akses tidak mencukupi.
- 404 Not Found: Data barang tidak ditemukan.
- 422 Unprocessable Entity: Validasi data gagal (misalnya format data salah).
- 429 Too Many Requests: Batas akses terlampaui (default: 60 permintaan per menit).

---
Seluruh token API bersifat rahasia. Keamanan data bergantung pada kerahasiaan token yang Anda kelola.
