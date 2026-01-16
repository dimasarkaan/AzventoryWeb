<?php

namespace App\Console\Commands;

use App\Models\Sparepart;
use Illuminate\Console\Command;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;

class GenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR Codes for spareparts that do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $spareparts = Sparepart::all();

        if ($spareparts->isEmpty()) {
            $this->info('All spareparts already have QR codes.');
            return;
        }

        $this->info('Generating QR Codes for ' . $spareparts->count() . ' spareparts...');

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
        $this->info('QR Codes generated successfully.');
    }
}
