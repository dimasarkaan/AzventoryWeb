# Panduan Integrasi API Azventory v1.5 (FINAL)
Dokumen teknis komprehensif untuk pengembang internal dan integrasi sistem pihak ketiga.

---

## 🔐 Autentikasi (Sanctum Bearer Token)
Azventory menggunakan **Laravel Sanctum**. Setiap permintaan wajib menyertakan token pada header permintaan.

### 1. Header Standar
```http
Authorization: Bearer <TOKEN_ANDA>
Accept: application/json
Content-Type: application/json
```

---

## 🛠️ Endpoint Mega CRUD (100% Coverage)

### 1. Inventaris (`/inventory`)
- **Semua Operasi**: `GET` (List), `POST` (Create), `GET /{id}` (Show), `PUT /{id}` (Update), `DELETE /{id}` (Delete).
- **Stok**: `PUT /{id}/adjust-stock` (Increment/Decrement), `GET /{id}/logs` (Mutation History).

### 2. Peminjaman (`/borrowings`)
- **Semua Operasi**: `GET` (List), `POST` (Create), `GET /{id}` (Show).
- **Kembali**: `POST /{id}/return` (Return item process).

### 3. Data Master (`/brands`, `/categories`, `/locations`)
- **Full CRUD**: Kini mendukung **Create, Update, dan Delete** via API untuk semua tabel master data.
- **Konsistensi**: Mengubah nama Merk/Kategori via API otomatis memperbarui string di seluruh data barang terkait (Integritas Data).

### 4. Manajemen User (`/users`)
- **Kontrol Penuh**: List (inc. trash), Create, Show, Update, Delete, dan Reset Password.
- **Akses**: Terkunci khusus untuk Superadmin.

### 5. Sistem & Profil (`/me`, `/stats`, `/notifications`, `/activity-logs`)
- **Statistik**: `/stats` untuk ringkasan real-time.
- **Audit**: `/activity-logs` untuk melihat jejak audit sistem.
- **Notifikasi**: `/notifications` untuk manajemen peringatan stok.

---

## 🚀 Tutorial: Create Item via API
```javascript
const response = await fetch('https://domain.com/api/v1/inventory', {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${TOKEN}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
        part_number: "SP-001",
        name: "Contoh Barang",
        brand: "SAMSUNG",
        location: "Gudang A",
        category: "Storage",
        type: "asset",
        stock: 10,
        condition: "Baru",
        status: "aktif"
    })
});
```

---

## 📂 Postman Collection v1.5
Gunakan file `Azventory_API_v1.postman_collection.json` di root proyek.
- **Struktur Folder Baru**: Dikelompokkan berdasarkan modul CRUD (Inventory, Master Data, Borrowing, Management).
- **Pewarisan Auth**: Otomatis menggunakan token dari variabel koleksi.

---
*Terakhir diperbarui: 29 Maret 2026*
