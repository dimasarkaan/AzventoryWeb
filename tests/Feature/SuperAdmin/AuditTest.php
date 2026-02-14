<?php

namespace Tests\Feature\SuperAdmin;

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
        // Create a SuperAdmin user to access protected routes
        $this->superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Seed some data
        Sparepart::factory()->count(5)->create();
    }

    /** @test */
    public function inventory_index_uses_cache_for_dropdowns()
    {
        Cache::shouldReceive('remember')
            ->times(7) // categories, brands, locations, colors, units, names, partNumbers
            ->andReturn(collect(['Test']));

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.inventory.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function pdf_report_generation_is_queued()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf'
            ]));

        // Should return back with success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert Job was pushed
        Queue::assertPushed(GenerateReportJob::class);
    }

    /** @test */
    public function excel_report_generation_is_synchronous()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel'
            ]));

        // Should be a file download stream (200 OK)
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');

        // Assert Job was NOT pushed
        Queue::assertNotPushed(GenerateReportJob::class);
    }
}
