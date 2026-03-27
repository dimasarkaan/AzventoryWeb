<?php

namespace Tests\Feature\Reports;

use App\Jobs\GenerateReportJob;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesAudit extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user SuperAdmin untuk mengakses rute yang dilindungi
        $this->superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Seed beberapa data
        Sparepart::factory()->count(5)->create();
    }

    #[Test]
    public function indeks_inventaris_menggunakan_cache_untuk_dropdown()
    {
        Cache::shouldReceive('remember')
            ->times(8) // kategori, merek, lokasi, warna, satuan, nama, partNumbers, conditions
            ->andReturn(collect(['Test']));

        $response = $this->actingAs($this->superAdmin)
            ->get(route('inventory.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function pembuatan_laporan_pdf_dimasukkan_ke_antrean()
    {
        Queue::fake();
        Sparepart::factory()->count(1001)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf',
            ]));

        // Seharusnya kembali dengan pesan sukses
        $response->assertRedirect();
        $response->assertSessionHas('info');

        // Pastikan Job didorong
        Queue::assertPushed(GenerateReportJob::class);
    }

    #[Test]
    public function pembuatan_laporan_excel_dilakukan_secara_sinkron()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel',
            ]));

        // Seharusnya berupa stream download file (200 OK)
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Pastikan Job TIDAK didorong
        Queue::assertNotPushed(GenerateReportJob::class);
    }
}

