<?php

namespace App\Console\Commands;

use App\Models\Sparepart;
use Illuminate\Console\Command;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;

class GenerateQrCodes extends Command
{
    /**
     * Nama dan signature console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-qr-codes';

    /**
     * Deskripsi console command.
     *
     * @var string
     */
    protected $description = 'Generate kode QR untuk sparepart yang belum memilikinya';

    /**
     * Eksekusi console command.
     */
    public function handle()
    {
        $spareparts = Sparepart::all();

        if ($spareparts->isEmpty()) {
            $this->info('Semua sparepart sudah memiliki kode QR.');
            return;
        }

        $this->info('Membuat Kode QR untuk ' . $spareparts->count() . ' sparepart...');

        $bar = $this->output->createProgressBar($spareparts->count());
        $bar->start();

        $options = new \chillerlan\QRCode\QROptions([
            'outputBase64' => false,
        ]);

        foreach ($spareparts as $sparepart) {
            $qrCodeUrl = route('superadmin.inventory.show', $sparepart);
            $qrCodeOutput = (new \chillerlan\QRCode\QRCode($options))->render($qrCodeUrl);
            $qrCodePath = 'qrcodes/' . $sparepart->part_number . '_' . $sparepart->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeOutput);

            $sparepart->update(['qr_code_path' => $qrCodePath]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Kode QR berhasil dibuat.');
    }
}
