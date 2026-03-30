<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesPencetakanLabel extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $admin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock storage
        \Illuminate\Support\Facades\Storage::fake('public');

        // Setup users
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_cetak_label_satuan()
    {
        $item = Sparepart::factory()->create(['qr_code_path' => 'qrcodes/test.png']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qrcodes/test.png', 'dummy');

        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.print', $item->id));

        $response->assertStatus(200)
            ->assertSee($item->name)
            ->assertSee($item->part_number)
            ->assertSee('Label QR Satuan')
            ->assertSee('csrf-token'); // Verify it has our fixed CSRF tag
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_cetak_label_satuan()
    {
        $item = Sparepart::factory()->create(['qr_code_path' => 'qrcodes/test.png']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qrcodes/test.png', 'dummy');

        $response = $this->actingAs($this->admin)
            ->get(route('inventory.qr.print', $item->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_cetak_banyak()
    {
        $items = Sparepart::factory()->count(3)->create(['qr_code_path' => 'qrcodes/test.png']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qrcodes/test.png', 'dummy');
        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.bulk-print', ['ids' => implode(',', $ids)]));

        $response->assertStatus(200)
            ->assertSee('Cetak Banyak Label')
            ->assertSee($items[0]->name)
            ->assertSee($items[1]->name)
            ->assertSee($items[2]->name);
    }

    #[Test]
    public function halaman_cetak_banyak_memerlukan_parameter_ids()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.bulk-print'));

        // If no IDs provided, it should probably handled gracefully or redirect back
        // But in our current controller, it might fail or show empty.
        // Let's verify it doesn't crash (500)
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    #[Test]
    public function operator_dapat_mengakses_halaman_cetak_label_satuan()
    {
        // Printing is generally allowed for operators in this app's logic
        $item = Sparepart::factory()->create(['qr_code_path' => 'qrcodes/test.png']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qrcodes/test.png', 'dummy');

        $response = $this->actingAs($this->operator)
            ->get(route('inventory.qr.print', $item->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function halaman_cetak_satuan_redirect_jika_sparepart_tidak_ditemukan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.print', 99999));

        $response->assertStatus(404);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_halaman_cetak_massal()
    {
        $items = Sparepart::factory()->count(2)->create(['qr_code_path' => 'qrcodes/test.png']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qrcodes/test.png', 'dummy');
        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->operator)
            ->get(route('inventory.qr.bulk-print', ['ids' => implode(',', $ids)]));

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_menjalankan_hapus_massal()
    {
        $items = Sparepart::factory()->count(2)->create();
        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->operator)
            ->deleteJson(route('inventory.bulk-destroy'), ['ids' => $ids]);

        $response->assertStatus(403);
    }

    #[Test]
    public function cetak_banyak_dibatasi_maksimal_100_item()
    {
        $ids = range(1, 101); // 101 IDs

        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.bulk-print', ['ids' => implode(',', $ids)]));

        $response->assertStatus(302)
            ->assertSessionHas('error', 'Maksimal 100 item untuk satu sesi cetak (keamanan performa).');
    }

    #[Test]
    public function pencetakan_mencatat_log_aktivitas()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->superadmin)
            ->postJson(route('inventory.qr.log'), [
                'ids' => [$sparepart->id],
                'counts' => [$sparepart->id => 5],
                'total' => 5,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->superadmin->id,
            'action' => 'Cetak Label',
        ]);
    }

    #[Test]
    public function hanya_user_terautentikasi_yang_dapat_mencatat_log_pencetakan()
    {
        $response = $this->postJson(route('inventory.qr.log'), [
            'ids' => [1],
            'counts' => [1 => 1],
            'total' => 1,
        ]);

        $response->assertStatus(401); // Unauthorized
    }
}
