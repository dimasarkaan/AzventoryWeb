<!doctype html>
<html>
<head>
    <title>Dokumentasi API Azventory</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="http://127.0.0.1:8000/logo.svg?v=2" type="image/svg+xml">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom Logo for Scalar */
        .scalar-logo {
            content: url('http://127.0.0.1:8000/logo.svg');
            width: 24px;
            height: 24px;
        }
        
        /* Tema Terang (Light Mode) */
        :root {
            --scalar-color-accent: #2563eb; /* Azventory Primary 600 */
            --scalar-button-1: #2563eb;
            --scalar-button-1-hover: #1d4ed8;
            --scalar-button-1-color: #ffffff;
            --scalar-background-2: #f8fafc; /* Azventory Background */
        }
        
        /* Sembunyikan elemen bawaan yang tidak diperlukan (UX Improvement) */
        .dark-mode {
            --scalar-color-accent: #3b82f6; /* Azventory Primary 500 */
            --scalar-button-1: #3b82f6;
            --scalar-button-1-hover: #60a5fa;
            --scalar-button-1-color: #ffffff;
        }
        
        /* Sembunyikan tulisan Download & Watermark */
        a[href*="scalar.com"], 
        .scalar-client-powered-by {
            display: none !important;
        }

        /* Sembunyikan fitur Ask AI (Gagal Fetch karena butuh OpenAI key) */
        button[aria-label="Ask AI Agent"],
        .scalar-app [aria-label*="Ask AI"],
        .ask-ai-button {
            display: none !important;
        }
    </style>
</head>
<body>

<script id="api-reference" type="application/json"
    data-configuration='{"proxy": "", "hideDownloadButton": false, "hideModels": true, "agent": {"disabled": true}, "hiddenClients": {"ruby": true, "python": true, "java": true, "c": true, "csharp": true, "swift": true, "kotlin": true, "objectivec": true, "go": true, "clojure": true, "ocaml": true, "r": true, "node": true}}'
    data-configuration="{&quot;agent&quot;: {&quot;disabled&quot;: true}}"
>
openapi: 3.0.3
info:
  title: 'Dokumentasi API Azventory'
  description: 'Dokumentasi resmi API Azventory'
  version: 1.0.0
servers:
  -
    url: 'http://127.0.0.1:8000'
tags:
  -
    name: 'Laporan & Aktivitas'
    description: "\nModul ini mencatat riwayat penggunaan aplikasi.\n\nAnda bisa melihat log aktivitas pengguna (siapa yang mengubah data, kapan, dan apa yang diubah) serta mengambil ringkasan statistik seperti total barang dan stok yang menipis."
  -
    name: 'Manajemen Akses (Auth)'
    description: "\nAPI ini menangani proses login dan logout.\n\nSaat berhasil login, sistem akan memberikan token yang digunakan untuk mengakses fitur-fitur lain di aplikasi."
  -
    name: 'Manajemen Inventaris'
    description: "\nIni adalah fungsi inti aplikasi untuk mengelola barang.\n\nAnda bisa menggunakan API ini untuk menambah daftar barang baru, melihat stok, memperbarui informasi barang, serta mencatat riwayat barang masuk atau keluar."
  -
    name: 'Manajemen Pengguna'
    description: "\nBagian ini berisi API untuk mengelola akun staf atau karyawan yang bisa login ke aplikasi Azventory. Anda bisa menambahkan pengguna baru, mengubah data mereka, mengatur ulang kata sandi, serta menentukan peran masing-masing pengguna (Superadmin, Admin, Operator, dll)."
  -
    name: 'Master Data'
    description: "\nAPI ini digunakan untuk mengatur data dasar aplikasi seperti daftar Merk, Kategori, dan Lokasi Cabang. Data ini diperlukan sebelum Anda bisa menambahkan barang baru ke dalam sistem."
  -
    name: 'Peminjaman Barang'
    description: "\nBagian ini digunakan untuk mencatat peminjaman barang inventaris.\n\nAPI ini melacak siapa yang meminjam, kapan barang harus dikembalikan, dan bagaimana kondisi barang saat dikembalikan."
  -
    name: 'Profil Pengguna'
    description: "\nAPI ini khusus untuk pengguna yang sedang login agar bisa melihat dan mengubah informasi profil mereka sendiri, termasuk mengubah foto dan kata sandi."
  -
    name: 'Sistem & Notifikasi'
    description: "\nAPI ini digunakan untuk mengambil pemberitahuan otomatis dari sistem.\n\nNotifikasi ini biasanya berisi info penting seperti pengingat saat stok barang mulai habis atau batas waktu pengembalian barang pinjaman."
components:
  securitySchemes:
    default:
      type: http
      scheme: bearer
      description: 'Gunakan Bearer token yang bisa didapatkan saat login API atau dari halaman profil.'
security:
  -
    default: []
paths:
  /api/v1/activity-logs:
    get:
      summary: 'Mendapatkan daftar log aktivitas global (Hanya Superadmin).'
      operationId: mendapatkanDaftarLogAktivitasGlobalHanyaSuperadmin
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Laporan & Aktivitas'
  '/api/v1/activity-logs/user/{id}':
    get:
      summary: 'Mendapatkan log aktivitas untuk pengguna tertentu (Bisa oleh Admin).'
      operationId: mendapatkanLogAktivitasUntukPenggunaTertentuBisaOlehAdmin
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Laporan & Aktivitas'
    parameters:
      -
        in: path
        name: id
        description: 'ID pengguna.'
        example: 081d7a1a-8a65-4c61-81d8-3edb9cae5e1a
        required: true
        schema:
          type: string
  /api/v1/stats:
    get:
      summary: 'Mendapatkan ringkasan statistik sistem.'
      operationId: mendapatkanRingkasanStatistikSistem
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Laporan & Aktivitas'
  /api/v1/login:
    post:
      summary: ''
      operationId: postApiV1Login
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  success: false
                  message: 'Email atau password salah'
                properties:
                  success:
                    type: boolean
                    example: false
                  message:
                    type: string
                    example: 'Email atau password salah'
      tags:
        - 'Manajemen Akses (Auth)'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                email:
                  type: string
                  description: 'harus berupa alamat email yang valid.'
                  example: qkunze@example.com
                password:
                  type: string
                  description: ''
                  example: password123
              required:
                - email
                - password
  /api/v1/logout:
    post:
      summary: ''
      operationId: postApiV1Logout
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Akses (Auth)'
  /api/v1/inventory:
    get:
      summary: 'Mendapatkan daftar barang inventaris.'
      operationId: mendapatkanDaftarBarangInventaris
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
    post:
      summary: 'Menyimpan barang inventaris baru.'
      operationId: menyimpanBarangInventarisBaru
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
      requestBody:
        required: true
        content:
          multipart/form-data:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'Nama lengkap barang inventaris. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/''"]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Laptop Lenovo ThinkPad T14'
                part_number:
                  type: string
                  description: 'Nomor unik, kode seri, atau part number barang. Harus sesuai dengan pola regex /^[a-zA-Z0-9][a-zA-Z0-9\-\_\/]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: LNV-T14-2023
                brand_id:
                  type: string
                  description: 'ID unik dari merk barang yang sudah ada di database. Harus berupa ID yang valid dari tabel merk.'
                  example: 1
                category_id:
                  type: string
                  description: 'ID unik dari kategori barang yang sudah ada di database. Harus berupa ID yang valid dari tabel kategori.'
                  example: 2
                location_id:
                  type: string
                  description: 'ID unik dari lokasi penyimpanan yang sudah ada di database. Harus berupa ID yang valid dari tabel lokasi.'
                  example: 3
                age:
                  type: string
                  description: 'Status pemakaian barang.'
                  example: Baru
                  enum:
                    - Baru
                    - 'Pernah Dipakai (Bekas)'
                condition:
                  type: string
                  description: 'Kondisi fisik dan fungsional barang saat ini. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/''"]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Mulus 100% dan Berfungsi Normal'
                color:
                  type: string
                  description: 'Warna dominan barang (opsional). Harus sesuai dengan pola regex /^[a-zA-Z][a-zA-Z\s\-]*$/. Tidak boleh lebih dari 50 karakter.'
                  example: Hitam
                  nullable: true
                type:
                  type: string
                  description: 'Tipe klasifikasi barang (asset: aset tetap, sale: barang habis pakai/dijual).'
                  example: asset
                  enum:
                    - sale
                    - asset
                price:
                  type: number
                  description: 'Harga satuan barang dalam Rupiah. Wajib diisi jika type adalah sale. Wajib diisi jika <code>type</code> is <code>sale</code>. Harus minimal 0.'
                  example: 15500000.0
                  nullable: true
                stock:
                  type: integer
                  description: 'Jumlah kuantitas stok saat ini. Harus minimal 0.'
                  example: 10
                minimum_stock:
                  type: integer
                  description: 'Batas minimum peringatan stok (opsional). Harus minimal 0.'
                  example: 2
                  nullable: true
                unit:
                  type: string
                  description: 'Satuan hitung barang (opsional). Harus sesuai dengan pola regex /^[a-zA-Z0-9\s]*$/. Tidak boleh lebih dari 50 karakter.'
                  example: Unit
                  nullable: true
                status:
                  type: string
                  description: 'Status ketersediaan barang di sistem.'
                  example: aktif
                  enum:
                    - aktif
                    - nonaktif
                image:
                  type: string
                  format: binary
                  description: 'File foto barang (jpeg, png, jpg, webp). Harus berupa file. Harus berupa file gambar.'
                  nullable: true
                existing_image:
                  type: string
                  description: ''
                  example: password123
                  nullable: true
              required:
                - name
                - part_number
                - brand_id
                - category_id
                - location_id
                - age
                - condition
                - type
                - stock
                - status
  '/api/v1/inventory/{uuid}':
    get:
      summary: 'Mendapatkan detail satu barang inventaris.'
      operationId: mendapatkanDetailSatuBarangInventaris
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
    put:
      summary: 'Memperbarui barang inventaris.'
      operationId: memperbaruiBarangInventaris
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
      requestBody:
        required: true
        content:
          multipart/form-data:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'Nama lengkap barang inventaris yang ingin diubah. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/''"]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Laptop Lenovo ThinkPad T14 Gen 3'
                part_number:
                  type: string
                  description: 'Nomor unik, kode seri, atau part number barang. Harus sesuai dengan pola regex /^[a-zA-Z0-9][a-zA-Z0-9\-\_\/]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: LNV-T14-2023-V2
                brand_id:
                  type: string
                  description: 'ID unik dari merk barang yang sudah ada di database. Harus berupa ID yang valid dari tabel merk.'
                  example: 1
                category_id:
                  type: string
                  description: 'ID unik dari kategori barang yang sudah ada di database. Harus berupa ID yang valid dari tabel kategori.'
                  example: 2
                location_id:
                  type: string
                  description: 'ID unik dari lokasi penyimpanan yang sudah ada di database. Harus berupa ID yang valid dari tabel lokasi.'
                  example: 3
                condition:
                  type: string
                  description: 'Kondisi fisik dan fungsional barang saat ini. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/''"]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Layar ada baret sedikit, fungsi normal'
                color:
                  type: string
                  description: 'Warna dominan barang. Harus sesuai dengan pola regex /^[a-zA-Z][a-zA-Z\s\-]*$/. Tidak boleh lebih dari 50 karakter.'
                  example: Silver
                  nullable: true
                type:
                  type: string
                  description: 'Tipe klasifikasi barang (asset atau sale).'
                  example: asset
                  enum:
                    - sale
                    - asset
                price:
                  type: number
                  description: 'Harga satuan barang dalam Rupiah. Wajib diisi jika type adalah sale. Wajib diisi jika <code>type</code> is <code>sale</code>. Harus minimal 0.'
                  example: 14000000.0
                  nullable: true
                stock:
                  type: integer
                  description: 'Jumlah kuantitas stok barang. Harus minimal 0.'
                  example: 8
                minimum_stock:
                  type: integer
                  description: 'Batas minimum peringatan stok. Harus minimal 0.'
                  example: 2
                  nullable: true
                unit:
                  type: string
                  description: 'Satuan hitung barang. Harus sesuai dengan pola regex /^[a-zA-Z0-9\s]*$/. Tidak boleh lebih dari 50 karakter.'
                  example: Unit
                  nullable: true
                status:
                  type: string
                  description: 'Status ketersediaan barang.'
                  example: aktif
                  enum:
                    - aktif
                    - nonaktif
                image:
                  type: string
                  format: binary
                  description: 'File foto barang (jpeg, png, jpg, webp) jika ingin mengganti gambar. Harus berupa file. Harus berupa file gambar.'
                  nullable: true
                existing_image:
                  type: string
                  description: 'Nama file gambar saat ini, kirim kosong (null/empty) jika gambar dihapus oleh user.'
                  example: laptop_lenovo.jpg
                  nullable: true
                age:
                  type: string
                  description: 'Status pemakaian barang.'
                  example: 'Pernah Dipakai (Bekas)'
                  enum:
                    - Baru
                    - 'Pernah Dipakai (Bekas)'
              required:
                - name
                - part_number
                - brand_id
                - category_id
                - location_id
                - condition
                - type
                - stock
                - status
                - age
    delete:
      summary: 'Menghapus barang inventaris.'
      operationId: menghapusBarangInventaris
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
    parameters:
      -
        in: path
        name: uuid
        description: ''
        example: 019fb1c3-fa05-70ad-992f-586203643141
        required: true
        schema:
          type: string
      -
        in: path
        name: inventory
        description: 'UUID dari barang inventaris.'
        example: 9a5f3b...
        required: true
        schema:
          type: string
  '/api/v1/inventory/{id}/logs':
    get:
      summary: 'Mendapatkan riwayat mutasi stok untuk barang tertentu.'
      operationId: mendapatkanRiwayatMutasiStokUntukBarangTertentu
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
    parameters:
      -
        in: path
        name: id
        description: 'ID barang inventaris.'
        example: password123
        required: true
        schema:
          type: string
  '/api/v1/inventory/{id}/adjust-stock':
    put:
      summary: 'Menyesuaikan stok (tambah/kurang) untuk penjualan atau pasokan.'
      operationId: menyesuaikanStoktambahkurangUntukPenjualanAtauPasokan
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Inventaris'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                type:
                  type: string
                  description: ''
                  example: increment
                  enum:
                    - increment
                    - decrement
                quantity:
                  type: integer
                  description: 'Jumlah penyesuaian stok (bisa minus).'
                  example: 5
                description:
                  type: string
                  description: ''
                  example: 'Penyesuaian stok dari sistem.'
                  nullable: true
                notes:
                  type: string
                  description: 'Alasan penyesuaian stok.'
                  example: 'Penambahan stok baru'
              required:
                - type
                - quantity
                - notes
    parameters:
      -
        in: path
        name: id
        description: 'ID barang inventaris.'
        example: password123
        required: true
        schema:
          type: string
  /api/v1/users:
    get:
      summary: 'Mendapatkan daftar semua user.'
      operationId: mendapatkanDaftarSemuaUser
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Pengguna'
    post:
      summary: 'Membuat user baru via API.'
      operationId: membuatUserBaruViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Pengguna'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'Nama lengkap pengguna. Harus sesuai dengan pola regex /^[a-zA-Z][a-zA-Z\s\.''\-]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'John Doe'
                email:
                  type: string
                  description: 'Alamat email valid (harus unik). Harus berupa alamat email yang valid. Tidak boleh lebih dari 255 karakter.'
                  example: johndoe@example.com
                role:
                  type: string
                  description: 'Peran pengguna (superadmin, admin, operator).'
                  example: admin
                  enum:
                    - superadmin
                    - admin
                    - operator
                jabatan:
                  type: string
                  description: 'Jabatan pengguna di perusahaan. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/''"]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Staff IT'
                status:
                  type: string
                  description: 'Status akun (aktif, nonaktif).'
                  example: aktif
                  enum:
                    - aktif
                    - nonaktif
              required:
                - name
                - email
                - role
                - jabatan
                - status
  '/api/v1/users/{uuid}':
    get:
      summary: 'Detail user.'
      operationId: detailUser
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Pengguna'
    put:
      summary: 'Update user via API.'
      operationId: updateUserViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Pengguna'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'Nama lengkap pengguna. Harus sesuai dengan pola regex /^[a-zA-Z][a-zA-Z\s\.''\-]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Jane Doe'
                email:
                  type: string
                  description: 'Alamat email valid (harus unik selain milik pengguna ini sendiri). Harus berupa alamat email yang valid. Tidak boleh lebih dari 255 karakter.'
                  example: janedoe@example.com
                role:
                  type: string
                  description: 'Peran pengguna (superadmin, admin, operator).'
                  example: operator
                  enum:
                    - superadmin
                    - admin
                    - operator
                jabatan:
                  type: string
                  description: 'Jabatan pengguna di perusahaan. Harus sesuai dengan pola regex /^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/''"]*$/. Harus memiliki minimal 3 karakter. Tidak boleh lebih dari 255 karakter.'
                  example: 'Staff Gudang'
                status:
                  type: string
                  description: 'Status akun (aktif, nonaktif).'
                  example: aktif
                  enum:
                    - aktif
                    - nonaktif
              required:
                - name
                - email
                - role
                - jabatan
                - status
    delete:
      summary: 'Hapus user via API.'
      operationId: hapusUserViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Pengguna'
    parameters:
      -
        in: path
        name: uuid
        description: ''
        example: 081d7a1a-8a65-4c61-81d8-3edb9cae5e1a
        required: true
        schema:
          type: string
      -
        in: path
        name: user
        description: 'UUID dari pengguna.'
        example: 4d2f8e...
        required: true
        schema:
          type: string
  '/api/v1/users/{id}/reset-password':
    post:
      summary: 'Reset Password via API.'
      operationId: resetPasswordViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Manajemen Pengguna'
    parameters:
      -
        in: path
        name: id
        description: 'UUID dari pengguna.'
        example: 4d2f8e...
        required: true
        schema:
          type: string
  /api/v1/brands:
    get:
      summary: 'Menampilkan seluruh daftar Merek yang ada beserta hitungan jumlah barang di tiap Mereknya'
      operationId: menampilkanSeluruhDaftarMerekYangAdaBesertaHitunganJumlahBarangDiTiapMereknya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
    post:
      summary: 'Menyimpan data Merek baru ke dalam database'
      operationId: menyimpanDataMerekBaruKeDalamDatabase
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'tidak boleh lebih dari 191 karakter.'
                  example: Data Contoh
              required:
                - name
  '/api/v1/brands/{id}':
    put:
      summary: 'Mengedit/Memperbarui informasi nama Merek atau mengganti statusnya (Aktif/Non-aktif)'
      operationId: mengeditMemperbaruiInformasiNamaMerekAtauMenggantiStatusnyaAktifNonAktif
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
      requestBody:
        required: false
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: ''
                  example: null
                is_active:
                  type: boolean
                  description: ''
                  example: true
    delete:
      summary: 'Menghapus data Merek selamanya dari sistem'
      operationId: menghapusDataMerekSelamanyaDariSistem
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
    parameters:
      -
        in: path
        name: id
        description: 'ID merk.'
        example: 1
        required: true
        schema:
          type: integer
  /api/v1/categories:
    get:
      summary: 'Menampilkan seluruh daftar kategori beserta jumlah barang di tiap kategorinya'
      operationId: menampilkanSeluruhDaftarKategoriBesertaJumlahBarangDiTiapKategorinya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
    post:
      summary: 'Menyimpan data kategori baru ke dalam database'
      operationId: menyimpanDataKategoriBaruKeDalamDatabase
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'tidak boleh lebih dari 255 karakter.'
                  example: Data Contoh
              required:
                - name
  '/api/v1/categories/{id}':
    put:
      summary: 'Memperbarui informasi kategori yang sudah ada (Nama atau Status Aktifnya)'
      operationId: memperbaruiInformasiKategoriYangSudahAdaNamaAtauStatusAktifnya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
      requestBody:
        required: false
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: ''
                  example: null
                is_active:
                  type: boolean
                  description: ''
                  example: false
    delete:
      summary: 'Menghapus data kategori dari sistem selamanya'
      operationId: menghapusDataKategoriDariSistemSelamanya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
    parameters:
      -
        in: path
        name: id
        description: 'ID kategori.'
        example: password123
        required: true
        schema:
          type: string
  /api/v1/locations:
    get:
      summary: 'Menampilkan daftar seluruh lokasi yang ada, lengkap dengan jumlah barang yang tersimpan di dalamnya'
      operationId: menampilkanDaftarSeluruhLokasiYangAdaLengkapDenganJumlahBarangYangTersimpanDiDalamnya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
    post:
      summary: 'Memproses pembuatan lokasi gudang baru'
      operationId: memprosesPembuatanLokasiGudangBaru
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'tidak boleh lebih dari 191 karakter.'
                  example: Data Contoh
              required:
                - name
  '/api/v1/locations/{id}':
    put:
      summary: 'Menyimpan perubahan data lokasi (mengganti nama atau status aktifnya)'
      operationId: menyimpanPerubahanDataLokasimenggantiNamaAtauStatusAktifnya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
      requestBody:
        required: false
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: ''
                  example: null
                is_active:
                  type: boolean
                  description: ''
                  example: false
    delete:
      summary: 'Menghapus data lokasi/gudang dari sistem selamanya'
      operationId: menghapusDataLokasigudangDariSistemSelamanya
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Master Data'
    parameters:
      -
        in: path
        name: id
        description: 'ID lokasi.'
        example: 1
        required: true
        schema:
          type: integer
  /api/v1/borrowings:
    get:
      summary: 'Mendapatkan daftar peminjaman aktif/riwayat.'
      operationId: mendapatkanDaftarPeminjamanAktifriwayat
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Peminjaman Barang'
  '/api/v1/inventory/{sparepart_uuid}/borrow':
    post:
      summary: 'Mencatat peminjaman baru via API.'
      operationId: mencatatPeminjamanBaruViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Peminjaman Barang'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                quantity:
                  type: integer
                  description: 'Jumlah barang yang ingin dipinjam. Harus minimal 1.'
                  example: 2
                notes:
                  type: string
                  description: 'Catatan keperluan meminjam.'
                  example: 'Untuk keperluan meeting presentasi di lantai 3.'
                  nullable: true
                expected_return_at:
                  type: string
                  description: 'Rencana tanggal pengembalian barang. Bukan tanggal yang valid. Harus berupa tanggal setelah atau sama dengan <code>today</code>.'
                  example: '2026-08-27'
              required:
                - quantity
                - expected_return_at
    parameters:
      -
        in: path
        name: sparepart_uuid
        description: ''
        example: 019fb1c3-fa05-70ad-992f-586203643141
        required: true
        schema:
          type: string
      -
        in: path
        name: sparepart
        description: 'UUID dari barang inventaris.'
        example: 9a5f3b...
        required: true
        schema:
          type: string
  '/api/v1/borrowings/{id}':
    get:
      summary: 'Detail peminjaman.'
      operationId: detailPeminjaman
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Peminjaman Barang'
    parameters:
      -
        in: path
        name: id
        description: 'ID peminjaman.'
        example: password123
        required: true
        schema:
          type: string
      -
        in: path
        name: borrowing
        description: 'UUID dari data peminjaman.'
        example: 7b3e1c...
        required: true
        schema:
          type: string
  '/api/v1/borrowings/{borrowing_id}/return':
    post:
      summary: 'Pengembalian barang via API.'
      operationId: pengembalianBarangViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Peminjaman Barang'
      requestBody:
        required: true
        content:
          multipart/form-data:
            schema:
              type: object
              properties:
                return_quantity:
                  type: integer
                  description: 'Jumlah barang yang dikembalikan. Harus minimal 1.'
                  example: 2
                return_condition:
                  type: string
                  description: 'Kondisi barang saat dikembalikan (good, bad, lost).'
                  example: good
                  enum:
                    - good
                    - bad
                    - lost
                return_notes:
                  type: string
                  description: 'Catatan pengembalian (opsional).'
                  example: 'Dikembalikan dengan kondisi mulus.'
                  nullable: true
                return_photos:
                  type: array
                  description: 'Harus berupa file. Harus berupa file gambar.'
                  items:
                    type: string
                    format: binary
              required:
                - return_quantity
                - return_condition
    parameters:
      -
        in: path
        name: borrowing_id
        description: 'ID peminjaman.'
        example: 1
        required: true
        schema:
          type: integer
      -
        in: path
        name: borrowing
        description: 'UUID dari data peminjaman yang akan dikembalikan.'
        example: 7b3e1c...
        required: true
        schema:
          type: string
  /api/v1/me:
    get:
      summary: 'Mendapatkan profil pengguna yang sedang login.'
      operationId: mendapatkanProfilPenggunaYangSedangLogin
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Profil Pengguna'
    put:
      summary: 'Update profil via API.'
      operationId: updateProfilViaAPI
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Profil Pengguna'
      requestBody:
        required: false
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                  description: 'tidak boleh lebih dari 255 karakter.'
                  example: Data Contoh
                email:
                  type: string
                  description: ''
                  example: null
  /api/v1/notifications:
    get:
      summary: ''
      operationId: getApiV1Notifications
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Sistem & Notifikasi'
  '/api/v1/notifications/{id}/read':
    post:
      summary: ''
      operationId: postApiV1NotificationsIdRead
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Sistem & Notifikasi'
    parameters:
      -
        in: path
        name: id
        description: 'ID notifikasi.'
        example: password123
        required: true
        schema:
          type: string
  /api/v1/notifications/mark-all-read:
    post:
      summary: ''
      operationId: postApiV1NotificationsMarkAllRead
      description: ''
      parameters: []
      responses:
        401:
          description: ''
          content:
            application/json:
              schema:
                type: object
                example:
                  message: Unauthenticated.
                properties:
                  message:
                    type: string
                    example: Unauthenticated.
      tags:
        - 'Sistem & Notifikasi'

</script>
<script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
</body>
</html>
