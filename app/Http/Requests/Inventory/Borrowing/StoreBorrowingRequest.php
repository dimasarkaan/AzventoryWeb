<?php

namespace App\Http\Requests\Inventory\Borrowing;

use App\Models\Sparepart;
use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    // Mengecek apakah pengguna berhak mengajukan pinjaman (selalu true karena sudah diurus oleh Route/Policy)
    public function authorize(): bool
    {
        return true; // Main auth handled by Policy/Route
    }

    // Syarat wajib formulir Peminjaman Barang (Harus isi jumlah pinjam dan tanggal target kembali)
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'expected_return_at' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:+1 year'],
        ];
    }

    // Aturan Tambahan: Menjalankan pengecekan ganda secara langsung ke database
    // Memastikan jumlah stok fisik mencukupi untuk dipinjam sebelum membiarkan formulir lolos
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (auth()->check() && auth()->user()->borrowings()->overdue()->exists()) {
                $validator->errors()->add('borrow_error', 'Sistem mengunci fitur peminjaman. Anda masih memiliki pinjaman barang yang melewati batas waktu (Overdue). Harap kembalikan barang tersebut terlebih dahulu.');
            }

            $sparepart = $this->route('sparepart'); // Get sparepart from route binding

            if ($sparepart instanceof Sparepart) {
                $check = $sparepart->canBeBorrowed($this->input('quantity', 0));

                if ($check !== true) {
                    // Check is either true or error string
                    $validator->errors()->add('borrow_error', $check);
                }
            }
        });
    }

    // Memberikan nama samaran (Alias) agar pesan error lebih enak dibaca (contoh: 'quantity' jadi 'Jumlah Pinjam')
    public function attributes(): array
    {
        return [
            'quantity' => 'Jumlah Pinjam',
            'notes' => 'Catatan',
            'expected_return_at' => 'Rencana Tanggal Pengembalian',
        ];
    }

    // Dokumentasi API (Scribe)
    public function bodyParameters(): array
    {
        return [
            'quantity' => [
                'description' => 'Jumlah barang yang ingin dipinjam.',
                'example' => 2,
            ],
            'notes' => [
                'description' => 'Catatan keperluan meminjam.',
                'example' => 'Untuk keperluan meeting presentasi di lantai 3.',
            ],
            'expected_return_at' => [
                'description' => 'Rencana tanggal pengembalian barang.',
                'example' => date('Y-m-d', strtotime('+3 days')),
            ],
        ];
    }
}
