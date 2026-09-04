<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixScribeDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scribe:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merapikan format dan menerjemahkan dokumentasi Scribe yang di-generate otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = [
            storage_path('app/private/scribe/openapi.yaml'),
            resource_path('views/scribe/index.blade.php'),
        ];

        $patched = 0;

        foreach ($files as $path) {
            if (! file_exists($path)) {
                $this->warn("File tidak ditemukan: {$path}");

                continue;
            }

            $c = file_get_contents($path);
            $original = $c;

            $c = $this->translateEnglishStrings($c);
            $c = $this->cleanupDescriptions($c);

            if ($c !== $original) {
                file_put_contents($path, $c);
                $this->info('✓ Berhasil dipatch: '.basename($path));
                $patched++;
            } else {
                $this->line('  Tidak ada perubahan: '.basename($path));
            }
        }

        $this->info("Selesai. {$patched} file diperbarui.");
    }

    /**
     * Terjemahkan semua string bahasa Inggris bawaan Scribe.
     */
    private function translateEnglishStrings(string $c): string
    {
        // Teks validasi dari Scribe / Laravel
        $translations = [
            // Hapus "Kolom value" yang mengganggu
            'Kolom value ' => '',

            // Terjemahkan teks validasi Scribe
            'Must match the regex' => 'Harus sesuai dengan pola regex',
            'Must be a file.' => 'Harus berupa file.',
            'Must not be greater than' => 'Tidak boleh lebih dari',
            'This field is required when' => 'Wajib diisi jika',

            // Label Scribe
            'Example:' => 'Contoh:',

            // Path parameter descriptions
            "description: 'The ID of the user.'" => "description: 'ID pengguna.'",
            "description: 'The ID of the inventory.'" => "description: 'ID barang inventaris.'",
            "description: 'The ID of the borrowing.'" => "description: 'ID peminjaman.'",
            "description: 'The ID of the brand.'" => "description: 'ID merk.'",
            "description: 'The ID of the category.'" => "description: 'ID kategori.'",
            "description: 'The ID of the location.'" => "description: 'ID lokasi.'",
            "description: 'The ID of the sparepart.'" => "description: 'ID barang.'",
            "description: 'The ID of the notification.'" => "description: 'ID notifikasi.'",

            // Exists rule
            'The <code>id</code> of an existing record in the brands table.' => 'Harus berupa ID yang valid dari tabel merk.',
            'The <code>id</code> of an existing record in the categories table.' => 'Harus berupa ID yang valid dari tabel kategori.',
            'The <code>id</code> of an existing record in the locations table.' => 'Harus berupa ID yang valid dari tabel lokasi.',

            // Dummy data
            'consequatur' => 'password123',
            'vmqeopfuudtdsufvyvddq' => 'Data Contoh',
            'Dolores dolorum amet iste laborum eius est dolor.' => 'Penyesuaian stok dari sistem.',
        ];

        foreach ($translations as $en => $id) {
            $c = str_replace($en, $id, $c);
        }

        return $c;
    }

    /**
     * Bersihkan dan rapikan deskripsi (capitalize kalimat, tanpa mengubah format YAML).
     */
    private function cleanupDescriptions(string $c): string
    {
        // Capitalize setiap kalimat setelah ". " di dalam description value
        $c = preg_replace_callback("/description: '((?:[^']|'')*)'/", function ($matches) {
            $desc = $matches[1];

            // Lewati jika deskripsi kosong
            if (empty(trim($desc))) {
                return $matches[0];
            }

            // Proteksi: simpan regex patterns (format /^....$/) sebelum proses
            $regexPlaceholders = [];
            $desc = preg_replace_callback('/\/\^[^$]+\$\//', function ($m) use (&$regexPlaceholders) {
                $key = '###REGEX_'.count($regexPlaceholders).'###';
                $regexPlaceholders[$key] = $m[0];

                return $key;
            }, $desc);

            // Capitalize setiap kalimat yang dimulai setelah ". "
            $desc = preg_replace_callback('/\.\s+([a-z])/', function ($m) {
                return '. '.strtoupper($m[1]);
            }, $desc);

            // Kembalikan regex placeholders
            foreach ($regexPlaceholders as $key => $val) {
                $desc = str_replace($key, $val, $desc);
            }

            return "description: '{$desc}'";
        }, $c);

        return $c;
    }
}
