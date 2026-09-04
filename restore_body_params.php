<?php

$docs = [
    'app/Http/Controllers/Api/AuthController.php' => [
        'login' => [
            '     * @bodyParam email string required Alamat email yang terdaftar. Example: admin@azzahra.com',
            '     * @bodyParam password string required Kata sandi akun Anda. Example: secret123',
        ],
    ],
    'app/Http/Controllers/Inventory/BrandController.php' => [
        'store' => [
            '     * @bodyParam name string required Nama Merk. Example: Lenovo',
            '     * @bodyParam description string Deskripsi Merk. Example: Perusahaan teknologi multinasional',
        ],
        'update' => [
            '     * @bodyParam name string required Nama Merk. Example: Lenovo',
            '     * @bodyParam description string Deskripsi Merk. Example: Perusahaan teknologi multinasional',
        ],
    ],
    'app/Http/Controllers/Inventory/CategoryController.php' => [
        'store' => [
            '     * @bodyParam name string required Nama Kategori. Example: Laptop',
            '     * @bodyParam description string Deskripsi Kategori. Example: Komputer jinjing',
        ],
        'update' => [
            '     * @bodyParam name string required Nama Kategori. Example: Laptop',
            '     * @bodyParam description string Deskripsi Kategori. Example: Komputer jinjing',
        ],
    ],
    'app/Http/Controllers/Inventory/LocationController.php' => [
        'store' => [
            '     * @bodyParam name string required Nama lokasi cabang (harus unik). Example: Cabang Jakarta',
        ],
        'update' => [
            '     * @bodyParam name string required Nama lokasi cabang (harus unik). Example: Cabang Jakarta',
            '     * @bodyParam is_active boolean Status keaktifan (1 aktif, 0 nonaktif). Example: 1',
        ],
    ],
    'app/Http/Controllers/Inventory/Api/InventoryController.php' => [
        'store' => [
            '     * @bodyParam item_code string required Kode unik barang. Example: INV-001',
            '     * @bodyParam name string required Nama barang. Example: Laptop ThinkPad',
            '     * @bodyParam brand_id string ID valid dari Merk yang sudah terdaftar. Example: 9a8b7c6d-1234',
            '     * @bodyParam category_id string ID valid dari Kategori. Example: 9a8b7c6d-1234',
            '     * @bodyParam location_id string ID valid dari Lokasi Cabang. Example: 9a8b7c6d-1234',
            '     * @bodyParam stock integer required Jumlah stok barang. Example: 10',
            '     * @bodyParam min_stock integer Batas minimal stok untuk notifikasi. Example: 2',
        ],
        'update' => [
            '     * @bodyParam item_code string required Kode unik barang. Example: INV-001',
            '     * @bodyParam name string required Nama barang. Example: Laptop ThinkPad',
            '     * @bodyParam brand_id string ID valid dari Merk yang sudah terdaftar. Example: 9a8b7c6d-1234',
            '     * @bodyParam category_id string ID valid dari Kategori. Example: 9a8b7c6d-1234',
            '     * @bodyParam location_id string ID valid dari Lokasi Cabang. Example: 9a8b7c6d-1234',
            '     * @bodyParam min_stock integer Batas minimal stok untuk notifikasi. Example: 2',
        ],
        'adjustStock' => [
            '     * @bodyParam quantity integer required Jumlah penyesuaian stok (bisa minus). Example: 5',
            '     * @bodyParam notes string required Alasan penyesuaian stok. Example: Penambahan stok baru',
        ],
    ],
    'app/Http/Controllers/Inventory/Api/BorrowingController.php' => [
        'store' => [
            '     * @bodyParam inventory_id string required ID valid dari Barang Inventaris. Example: 9a8b7c6d-1234',
            '     * @bodyParam borrower_name string required Nama peminjam. Example: Budi Santoso',
            '     * @bodyParam quantity integer required Jumlah barang yang dipinjam. Example: 1',
            '     * @bodyParam expected_return_date date required Tanggal estimasi pengembalian. Example: 2024-12-31',
        ],
        'returnItem' => [
            '     * @bodyParam return_date date required Tanggal dikembalikan. Example: 2024-12-31',
            '     * @bodyParam return_condition string required Kondisi barang saat dikembalikan. Example: Baik',
        ],
    ],
];

foreach ($docs as $file => $methods) {
    if (! file_exists($file)) {
        continue;
    }
    $content = file_get_contents($file);

    foreach ($methods as $method => $params) {
        $pattern = '/(\/\*\*.*?\*\/[\s\n]*)(public\s+function\s+'.$method.'\s*\()/is';
        if (preg_match($pattern, $content, $matches)) {
            $existingDoc = $matches[1];
            // hapus @bodyParam lama
            $existingDoc = preg_replace('/^\s*\*\s*@bodyParam.*?\n/m', '', $existingDoc);

            // sisipkan params baru sebelum baris terakhir "*/"
            $replacementParams = implode("\n", $params)."\n     ";
            $newDoc = preg_replace('/(\s*\*\/[\s\n]*)$/', "\n".$replacementParams.'$1', $existingDoc);

            $content = str_replace($matches[1], $newDoc, $content);
        }
    }
    file_put_contents($file, $content);
}
echo "Body params injected.\n";
