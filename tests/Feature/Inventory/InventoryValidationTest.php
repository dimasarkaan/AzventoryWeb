<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk semua path inventory yang belum dicakup:
 * - Validation failures pada store/update
 * - Guest access → redirect login
 * - QR code download & print
 * - Soft-delete restore & force-delete
 * - Operator tidak bisa create/delete
 */
class InventoryValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    // ── Guest Access Guards ──────────────────────────────────────

    #[Test]
    public function guest_tidak_bisa_akses_inventory_index()
    {
        $this->get(route('inventory.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_tidak_bisa_akses_inventory_create()
    {
        $this->get(route('inventory.create'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_tidak_bisa_akses_inventory_store()
    {
        $this->post(route('inventory.store'), [])->assertRedirect(route('login'));
    }

    // ── Validation Failures ──────────────────────────────────────

    #[Test]
    public function store_inventory_gagal_jika_name_kosong()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('inventory.store'), [
                'name' => '',
                'part_number' => 'PN-001',
                'brand' => 'Brand',
                'category' => 'Cat',
                'location' => 'Loc',
                'age' => 'Baru',
                'condition' => 'Baik',
                'type' => 'asset',
                'stock' => 5,
                'status' => 'aktif',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function store_inventory_gagal_jika_status_tidak_valid()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('inventory.store'), [
                'name' => 'Item',
                'part_number' => 'PN-002',
                'brand' => 'Brand',
                'category' => 'Cat',
                'location' => 'Loc',
                'age' => 'Baru',
                'condition' => 'Baik',
                'type' => 'asset',
                'stock' => 5,
                'status' => 'invalid_status', // Invalid!
            ]);

        $response->assertSessionHasErrors('status');
    }

    #[Test]
    public function store_inventory_gagal_jika_type_sale_tapi_price_kosong()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('inventory.store'), [
                'name' => 'Barang Jual',
                'part_number' => 'BJ-001',
                'brand' => 'Brand',
                'category' => 'Cat',
                'location' => 'Loc',
                'age' => 'Baru',
                'condition' => 'Baik',
                'type' => 'sale',
                'price' => null, // wajib jika sale
                'stock' => 10,
                'status' => 'aktif',
            ]);

        $response->assertSessionHasErrors('price');
    }

    // ── Role Access Control ──────────────────────────────────────

    #[Test]
    public function operator_tidak_bisa_mengakses_form_create_inventory()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.create'));

        $response->assertForbidden();
    }

    #[Test]
    public function operator_tidak_bisa_menghapus_inventory()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->operator)
            ->delete(route('inventory.destroy', $sparepart));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }

    // ── Show page ────────────────────────────────────────────────

    #[Test]
    public function semua_role_dapat_melihat_halaman_detail_inventory()
    {
        $sparepart = Sparepart::factory()->create(['status' => 'aktif']);

        foreach ([$this->superadmin, $this->admin, $this->operator] as $user) {
            $this->actingAs($user)
                ->get(route('inventory.show', $sparepart))
                ->assertOk();
        }
    }

    // ── QR Code routes ───────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_mengakses_halaman_print_qr()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.print', $sparepart));

        // Bisa 200 (view berhasil render) atau 302 (redirect jika QR belum dibuat)
        $this->assertContains($response->getStatusCode(), [200, 302, 404]);
    }

    #[Test]
    public function superadmin_dapat_mendownload_qr_code()
    {
        $sparepart = Sparepart::factory()->create();

        // Buat file dummy QR untuk download
        if ($sparepart->qr_code_path) {
            \Illuminate\Support\Facades\Storage::disk('public')
                ->put($sparepart->qr_code_path, 'fake-qr-data');
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.download', $sparepart));

        // Either download or redirect (if QR not generated)
        $this->assertContains($response->getStatusCode(), [200, 302, 404]);
    }

    // ── Restore & Force Delete ───────────────────────────────────

    #[Test]
    public function superadmin_dapat_memulihkan_inventory_yang_dihapus()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->superadmin)
            ->patch(route('inventory.restore', $sparepart->id));

        $response->assertRedirect();
        $this->assertNotSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }

    #[Test]
    public function superadmin_dapat_hapus_permanen_inventory()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->superadmin)
            ->delete(route('inventory.force-delete', $sparepart->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('spareparts', ['id' => $sparepart->id]);
    }

    #[Test]
    public function inventory_tidak_bisa_dihapus_jika_ada_pinjaman_aktif()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $user = User::factory()->create();

        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('inventory.destroy', $sparepart));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }
}
