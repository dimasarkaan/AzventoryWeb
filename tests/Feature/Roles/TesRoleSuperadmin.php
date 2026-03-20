<?php

namespace Tests\Feature\Roles;

use App\Models\Sparepart;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesRoleSuperadmin extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user superadmin
        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        // Pastikan email terverifikasi dan password sudah diubah
        $this->superadmin->email_verified_at = now();
        $this->superadmin->password_changed_at = now();
        $this->superadmin->save();

        // MOCK penyimpanan gambar dan qr code
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('dummy/path.webp');
        });
        $this->mock(\App\Services\QrCodeService::class, function ($mock) {
            $mock->shouldReceive('generate')->andReturn('dummy/qrcode.svg');
            $mock->shouldReceive('generateLabelSvg')->andReturn('<svg>...</svg>');
            $mock->shouldReceive('getLabelFilename')->andReturn('dummy_label.svg');
        });
    }

    #[Test]
    public function superadmin_dapat_mengakses_semua_dashboard()
    {
        $response = $this->actingAs($this->superadmin)->get(route('dashboard.superadmin'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('dashboard.admin'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('dashboard.operator'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_manajemen_user()
    {
        $response = $this->actingAs($this->superadmin)->get(route('users.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_master_data()
    {
        $response = $this->actingAs($this->superadmin)->get(route('brands.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('categories.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('locations.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_semua_fitur_inventaris()
    {
        $response = $this->actingAs($this->superadmin)->get(route('inventory.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_laporan_dan_log()
    {
        $response = $this->actingAs($this->superadmin)->get(route('reports.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('reports.activity-logs.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_profil_dan_peminjaman_saya()
    {
        $response = $this->actingAs($this->superadmin)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)->get(route('profile.inventory'));
        $response->assertStatus(200);
    }
}
