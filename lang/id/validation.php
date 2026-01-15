<?php

return [

    'required' => 'Kolom :attribute wajib diisi.',
    'unique' => ':attribute sudah digunakan.',
    'email' => 'Kolom :attribute harus berupa alamat email yang valid.',
    'max' => [
        'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'numeric' => 'Kolom :attribute harus minimal :min.',
        'integer' => 'Kolom :attribute harus minimal :min.',
        'string' => 'Kolom :attribute harus memiliki minimal :min karakter.',
    ],
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'integer' => 'Kolom :attribute harus berupa bilangan bulat.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'in' => ':attribute yang dipilih tidak valid.',
    'current_password' => 'Kata sandi saat ini tidak cocok.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',

    'password' => [
        'letters' => ':attribute harus mengandung setidaknya satu huruf.',
        'numbers' => ':attribute harus mengandung setidaknya satu angka.',
    ],

    'attributes' => [
        'name' => 'Nama Sparepart',
        'part_number' => 'Part Number',
        'category' => 'Kategori',
        'location' => 'Lokasi Gudang',
        'condition' => 'Kondisi Barang',
        'price' => 'Harga',
        'stock' => 'Stok Awal',
        'status' => 'Status',
        'login' => 'Username atau Email',
        'password' => 'Kata Sandi',
        'current_password' => 'Kata Sandi Saat Ini',
    ],

];
