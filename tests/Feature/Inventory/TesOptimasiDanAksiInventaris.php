<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesOptimasiDanAksiInventaris extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Storage::fake('public');
    }

    #[Test]
    public function superadmin_dapat_mengubah_status_aktif_brand()
    {
        $brand = Brand::create(['name' => 'BRAND_STATUS_TEST_ABC', 'is_active' => true]);

        $this->actingAs($this->superadmin)
            ->patchJson(route('brands.update', $brand->id), [
                'name' => 'BRAND_STATUS_TEST_ABC_UPDATED',
                'is_active' => false,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'is_active' => false]);
    }

    #[Test]
    public function item_brand_nonaktif_tidak_tampil_saat_pendaftaran_barang()
    {
        Brand::create(['name' => 'BRAND_ACTIVE_UNIQUE_XYZ', 'is_active' => true]);
        Brand::create(['name' => 'BRAND_INACTIVE_UNIQUE_XYZ', 'is_active' => false]);

        $this->actingAs($this->superadmin)
            ->get(route('inventory.create'))
            ->assertStatus(200)
            ->assertSee('BRAND_ACTIVE_UNIQUE_XYZ')
            ->assertDontSee('BRAND_INACTIVE_UNIQUE_XYZ');
    }

    #[Test]
    public function admin_dapat_menjalankan_hapus_massal()
    {
        $items = Sparepart::factory()->count(2)->create();
        $ids = $items->pluck('id')->toArray();

        $this->actingAs($this->admin)
            ->deleteJson(route('inventory.bulk-destroy'), ['ids' => $ids])
            ->assertStatus(200);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('spareparts', ['id' => $id]);
        }
    }

    #[Test]
    public function cetak_label_massal_dapat_diakses_oleh_admin()
    {
        $items = Sparepart::factory()->count(2)->create();
        $ids = $items->pluck('id')->toArray();

        foreach ($items as $item) {
            $item->update(['qr_code_path' => 'qr_codes/'.$item->id.'.svg']);
            Storage::disk('public')->put($item->qr_code_path, 'dummy');
        }

        $this->actingAs($this->admin)
            ->get(route('inventory.qr.bulk-print', ['ids' => implode(',', $ids)]))
            ->assertStatus(200);
    }

    #[Test]
    public function admin_dibatasi_akses_halaman_tong_sampah()
    {
        $this->actingAs($this->admin)
            ->get(route('inventory.index', ['trash' => 'true']))
            ->assertForbidden();
    }

    #[Test]
    public function admin_dibatasi_akses_restore_barang_dari_sampah()
    {
        $item = Sparepart::factory()->create();
        $item->delete();

        $this->actingAs($this->admin)
            ->patch(route('inventory.restore', $item->id))
            ->assertForbidden();
    }
}
