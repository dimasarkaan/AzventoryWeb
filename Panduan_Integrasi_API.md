# Panduan Lengkap Penggunaan API Integrasi Azventory

Dokumen ini berisi panduan teknis langkah demi langkah untuk menggunakan API Azventory. Panduan ini ditujukan bagi *Developer/Programmer* yang ingin menghubungkan aplikasi pihak ketiga (seperti Website E-commerce, Toko Online, atau Sistem Kasir POS) dengan server utama Azventory.

---

## 1. Persiapan Awal (Autentikasi Token)
Server Azventory menggunakan metode **Bearer Token** (via Laravel Sanctum) untuk menjamin perlindungan server. Setiap permintaan API (*Request*) yang masuk WAJIB membawa token ini di dalam **Headers**.

**Langkah Mendapatkan Token:**
1. Login ke aplikasi web Azventory sebagai **Superadmin**.
2. Buka menu **Profil** (Kanan atas layan -> Profil).
3. Gulir ke bawah hingga menemukan kotak "**Kunci Akses API (Token)**".
4. Masukkan nama aplikasi integrasi Anda (contoh: "Toko Shopee V1" atau "Web Ecommerce Pribadi").
5. Klik **Generate Token** dan **SEGERA COPY** token yang muncul (biasanya diawali dengan angka seperti `1|qwBfz...`). Token ini hanya akan tampil sekali saja!

**Cara Mengirim Token (Headers):**
Di setiap pengiriman request API (pakai Postman, Curl, Guzzle, Fetch, dll), sisipkan *headers* berikut:
```http
Authorization: Bearer <TOKEN_ANDA_DISINI>
Accept: application/json
Content-Type: application/json
```

---

## 2. Base URL
Ganti nilai Base URL ini sesuai dengan alamat web Azventory Anda kelak saat hosting.
*   **Lokal:** `http://localhost:8000/api/v1`
*   **Live (Contoh):** `https://azventory.com/api/v1`

---

## 3. Daftar Endpoint Lengkap (GET & POST)

### A. Mendapatkan Daftar Semua Barang (Katalog)
Endpoint ini mengambil seluruh persediaan barang, sangat berguna untuk ditempel di etalase web e-commerce.

*   **Endpoint:** `GET /inventory`
*   **Format Request Lengkap:**
    ```bash
    curl -X GET "http://localhost:8000/api/v1/inventory?per_page=20" \
      -H "Accept: application/json" \
      -H "Authorization: Bearer <TOKEN_ANDA>"
    ```
*   **Contoh Response Sukses (JSON):**
    ```json
    {
        "data": [
            {
                "id": 1,
                "part_number": "MS-001",
                "name": "Mouse Bulat",
                "brand": "Logitech",
                "stock": {
                    "current": 10,
                    "unit": "Pcs"
                },
                "price": 150000
            }
        ]
    }
    ```

### B. Mendapatkan Detail Satu Barang (Berdasarkan ID)
Berguna saat pelanggan e-commerce melihat halaman detail spesifik sebuah produk.

*   **Endpoint:** `GET /inventory/{id}`
*   *(Ganti `{id}` dengan angka ID barang, contoh `GET /inventory/1`)*

### C. Menyesuaikan Stok (PENTING UNTUK E-COMMERCE!)
Ini adalah endpoint yang **paling penting** untuk melakukan *Sinkronisasi*. Endpoint ini akan menambah atau memotong stok secara otomatis ketika ada transaksi di luar aplikasi.

*   **Endpoint:** `POST /inventory/{id}/adjust-stock`
*   **Contoh Kasus 1: Mengurangi Stok (Ada pembeli di web/toko)**
    ```bash
    curl -X POST "http://localhost:8000/api/v1/inventory/1/adjust-stock" \
      -H "Accept: application/json" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer <TOKEN_ANDA>" \
      -d '{
            "type": "decrement",
            "quantity": 1,
            "description": "Terjual via Toko Online, Order #1234"
          }'
    ```

*   **Contoh Kasus 2: Menambah Stok (Return barang / Kiriman baru)**
    ```bash
    curl -X POST "http://localhost:8000/api/v1/inventory/1/adjust-stock" \
      -H "Accept: application/json" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer <TOKEN_ANDA>" \
      -d '{
            "type": "increment",
            "quantity": 5,
            "description": "Restock kiriman dari Gudang Pusat"
          }'
    ```

### D. Menambahkan "Barang Baru" dari Luar
*   **Endpoint:** `POST /inventory`
*   **Kebutuhan Payload JSON (Body):** Pastikan semua *field* wajib terisi (part_number, name, brand, location, type, condition, dll). Format lengkap bisa disontek langsung dari file `Azventory_API_Collection.postman_collection.json`.

---

## 4. Tips Mengeringkas Pekerjaan untuk Programmer

Jika programmer (atau Anda) memiliki aplikasi bernama **Postman** (aplikasi wajib untuk merakit API):
1. Buka folder/direktori utama proyek Azventory ini.
2. Temukan file bernama: `Azventory_API_Collection.postman_collection.json`.
3. Buka aplikasi Postman, klik `File` -> `Import`.
4. Pilih file berakhiran `.json` tersebut.
5. Secara ajaib, seluruh endpoint di atas (beserta payload teks dan konfigurasinya) akan otomatis tersusun rapi di aplikasi! Programmer hanya perlu mengisi Tokennya sekali di form *Variables*.
