<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\GenerateReportJob;

class AuditTest extends TestCase
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

    /** @test */
    public function inventory_index_uses_cache_for_dropdowns()
    {
        Cache::shouldReceive('remember')
            ->times(7) // kategori, merek, lokasi, warna, satuan, nama, partNumbers
            ->andReturn(collect(['Test']));

        $response = $this->actingAs($this->superAdmin)
            ->get(route('inventory.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function pdf_report_generation_is_queued()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf'
            ]));

        // Seharusnya kembali dengan pesan sukses
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan Job didorong
        Queue::assertPushed(GenerateReportJob::class);
    }

    /** @test */
    public function excel_report_generation_is_synchronous()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel'
            ]));

        // Seharusnya berupa stream download file (200 OK)
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');

        // Pastikan Job TIDAK didorong
        Queue::assertNotPushed(GenerateReportJob::class);
    }
}
