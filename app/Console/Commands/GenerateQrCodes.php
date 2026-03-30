<?php

namespace App\Console\Commands;

use App\Models\Sparepart;
use Illuminate\Console\Command;

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
    public function handle(\App\Services\QrCodeService $qrCodeService)
    {
        $spareparts = Sparepart::all();

        if ($spareparts->isEmpty()) {
            $this->info('Tidak ada sparepart ditemukan.');

            return;
        }

        $this->info('Meregenerasi Kode QR untuk '.$spareparts->count().' sparepart...');

        $bar = $this->output->createProgressBar($spareparts->count());
        $bar->start();

        foreach ($spareparts as $sparepart) {
            $qrCodeService->generate($sparepart);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Seluruh Kode QR berhasil dioptimasi.');
    }
}
