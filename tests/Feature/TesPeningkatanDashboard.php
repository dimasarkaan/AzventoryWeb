<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesPeningkatanDashboard extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPERADMIN,
            'settings' => [],
        ]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function ia_dapat_memperbarui_pengaturan_dashboard_secara_persisten()
    {
        $response = $this->patchJson(route('profile.settings.update'), [
            'settings' => ['showStats' => false],
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('showStats', $this->user->fresh()->settings);
        $this->assertFalse($this->user->fresh()->settings['showStats']);
    }

    #[Test]
    public function service_dashboard_menangani_rentang_tanggal_kustom()
    {
        $service = new DashboardService;
        $start = '2024-01-01';
        $end = '2024-01-31';

        [$startDate, $endDate, $period] = $service->getDateRange('custom', null, null, $start, $end);

        $this->assertEquals('custom', $period);
        $this->assertEquals('2024-01-01', $startDate->format('Y-m-d'));
        $this->assertEquals('2024-01-31', $endDate->format('Y-m-d'));
    }

    #[Test]
    public function modal_aktivitas_dapat_diklik_di_dashboard()
    {
        // This is a browser test ideally, but we can verify the view has the @click
        $response = $this->get(route('dashboard.superadmin'));
        $response->assertStatus(200);
        $response->assertSee('@click="viewActivityDetails(log)"', false);
        $response->assertSee('showActivityModal', false);
    }
}

