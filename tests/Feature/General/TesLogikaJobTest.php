<?php

namespace Tests\Feature\General;

use App\Jobs\ExportActivityLogJob;
use App\Jobs\GenerateReportJob;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\ReportReadyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TesLogikaJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: GenerateReportJob benar-benar menghasilkan file dan mengirim notifikasi.
     */
    public function test_generate_report_job_menghasilkan_file_dan_notifikasi()
    {
        Storage::fake('local');
        Notification::fake();

        $user = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($user); // Set authenticated user for view rendering

        $spareparts = Sparepart::factory()->count(5)->create();

        // Buat Job
        $job = new GenerateReportJob(
            $user,
            null, // startDate
            null, // endDate
            'all', // location
            'inventory_list' // type
        );

        // Jalankan handle() secara manual (simulasi worker)
        $job->handle(app(\App\Services\ReportService::class));

        // Verifikasi file tersimpan di storage
        $files = Storage::disk('local')->allFiles('reports');
        $this->assertNotEmpty($files);
        $this->assertStringContainsString('LaporanInventaris', $files[0]);

        // Verifikasi notifikasi terkirim
        Notification::assertSentTo(
            [$user],
            ReportReadyNotification::class,
            function ($notification) {
                // Properties are protected, but we can check instance or toArray
                return $notification instanceof ReportReadyNotification;
            }
        );
    }

    /**
     * Test: ExportActivityLogJob benar-benar menghasilkan file PDF log aktivitas.
     */
    public function test_export_activity_log_job_menghasilkan_file_dan_notifikasi()
    {
        Storage::fake('local');
        Notification::fake();

        $user = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($user);

        $logs = ActivityLog::factory()->count(10)->create();

        // Buat Job
        $job = new ExportActivityLogJob(
            $user,
            ['start_date' => null, 'end_date' => null]
        );

        // Jalankan handle()
        $job->handle(app(\App\Services\ReportService::class));

        // Verifikasi file
        $files = Storage::disk('local')->allFiles('reports');
        $this->assertNotEmpty($files);
        $this->assertStringContainsString('LogAktivitas', $files[0]);

        // Verifikasi notifikasi
        Notification::assertSentTo(
            [$user],
            ReportReadyNotification::class,
            function ($notification) {
                return $notification instanceof ReportReadyNotification;
            }
        );
    }
}
