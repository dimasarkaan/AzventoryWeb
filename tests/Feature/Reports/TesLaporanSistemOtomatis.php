<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\MonthlyReportMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesLaporanSistemOtomatis extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
    }

    #[Test]
    public function perintah_laporan_bulanan_berhasil_mengirim_email_ke_superadmin()
    {
        Mail::fake();

        // Buat data agar laporan tidak kosong
        \App\Models\Sparepart::factory()->create();

        $this->artisan('app:send-monthly-reports')
            ->assertExitCode(0);

        Mail::assertSent(MonthlyReportMail::class, function ($mail) {
            return $mail->hasTo($this->superadmin->email);
        });
    }
}
