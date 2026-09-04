<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\MissingPriceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TesCrudInventarisTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);
    }

    public function test_halaman_index_create_edit_tampil_sukses()
    {
        $sparepart = Sparepart::factory()->create();

        $responseIndex = $this->actingAs($this->superadmin)->get(route('inventory.index'));
        $responseIndex->assertStatus(200);

        $responseCreate = $this->actingAs($this->superadmin)->get(route('inventory.create'));
        $responseCreate->assertStatus(200);

        $responseEdit = $this->actingAs($this->superadmin)->get(route('inventory.edit', $sparepart));
        $responseEdit->assertStatus(200);
    }

    public function test_quick_view_dan_show_tampil_sukses()
    {
        $sparepart = Sparepart::factory()->create();

        $responseQuick = $this->actingAs($this->superadmin)->get(route('inventory.quick-view', $sparepart));
        $responseQuick->assertStatus(200);

        $responseShow = $this->actingAs($this->superadmin)->get(route('inventory.show', $sparepart));
        $responseShow->assertStatus(200);
    }

    public function test_berhasil_membuat_sparepart_baru()
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $response = $this->actingAs($this->superadmin)->post(route('inventory.store'), [
            'name' => 'Sparepart Baru',
            'part_number' => 'NEW-123',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'condition' => 'Baik',
            'age' => 'Baru',
            'color' => 'Hitam',
            'type' => 'asset',
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Unit',
            'price' => 500000,
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('inventory.create'));
        $this->assertDatabaseHas('spareparts', [
            'part_number' => 'NEW-123',
            'stock' => 10,
        ]);
    }

    public function test_berhasil_update_sparepart()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->superadmin)->put(route('inventory.update', $sparepart), [
            'name' => $sparepart->name,
            'part_number' => $sparepart->part_number,
            'brand_id' => $sparepart->brand_id,
            'category_id' => $sparepart->category_id,
            'location_id' => $sparepart->location_id,
            'condition' => $sparepart->condition,
            'age' => $sparepart->age,
            'color' => $sparepart->color,
            'type' => $sparepart->type,
            'stock' => 20,
            'minimum_stock' => $sparepart->minimum_stock,
            'unit' => $sparepart->unit,
            'price' => $sparepart->price,
            'status' => $sparepart->status,
        ]);

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 20,
        ]);
    }

    public function test_soft_delete_restore_force_delete_berfungsi()
    {
        $sparepart = Sparepart::factory()->create();

        // Soft Delete
        $response = $this->actingAs($this->superadmin)->delete(route('inventory.destroy', $sparepart));
        $response->assertRedirect(route('inventory.index'));
        $this->assertSoftDeleted('spareparts', ['id' => $sparepart->id]);

        // Restore
        $response2 = $this->actingAs($this->superadmin)->patch(route('inventory.restore', $sparepart->uuid));
        $response2->assertRedirect();
        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'deleted_at' => null]);

        // Force Delete
        $sparepart->delete(); // Soft delete again
        $response3 = $this->actingAs($this->superadmin)->delete(route('inventory.force-delete', $sparepart->uuid));
        $response3->assertRedirect();
        $this->assertDatabaseMissing('spareparts', ['id' => $sparepart->id]);
    }

    public function test_api_check_part_number_berfungsi()
    {
        Sparepart::factory()->create(['part_number' => 'CHECK-001']);

        $response = $this->actingAs($this->superadmin)->get(route('inventory.check-part-number', ['part_number' => 'CHECK-001']));
        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);

        $response2 = $this->actingAs($this->superadmin)->get(route('inventory.check-part-number', ['part_number' => 'NOT-EXIST']));
        $response2->assertStatus(200);
        $response2->assertJson(['exists' => false]);
    }

    public function test_hapus_ditolak_jika_barang_sedang_dipinjam()
    {
        $sparepart = Sparepart::factory()->create();
        Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('inventory.destroy', $sparepart));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'deleted_at' => null]);
    }

    public function test_hapus_massal_ditolak_jika_barang_sedang_dipinjam()
    {
        $sparepart = Sparepart::factory()->create();
        Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->superadmin)->deleteJson(route('inventory.bulk-destroy'), [
            'ids' => [$sparepart->id],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'deleted_at' => null]);
    }

    public function test_operator_tidak_bisa_akses_halaman_trash()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.index', ['trash' => 'true']));
        $response->assertStatus(403);
    }

    public function test_operator_tidak_bisa_bulk_print()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.qr.bulk-print'));
        $response->assertStatus(403);
    }

    public function test_trigger_notifikasi_jika_harga_kosong_untuk_tipe_sale()
    {
        Notification::fake();

        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $this->actingAs($this->superadmin)->post(route('inventory.store'), [
            'name' => 'Barang Sale',
            'part_number' => 'SALE-123',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'condition' => 'Baik',
            'age' => 'Baru',
            'color' => 'Hitam',
            'type' => 'sale', // Type Sale
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Unit',
            'price' => 0, // Harga kosong/0
            'status' => 'aktif',
        ]);

        Notification::assertSentTo(
            [$this->superadmin], MissingPriceNotification::class
        );
    }

    public function test_update_memicu_merge_modal_jika_duplikat()
    {
        $sparepart1 = Sparepart::factory()->create(['part_number' => 'DUP-123', 'condition' => 'Baik']);
        $sparepart2 = Sparepart::factory()->create(['part_number' => 'DUP-456', 'condition' => 'Baik']);

        // Update sparepart2 menjadi sama dengan sparepart1
        $response = $this->actingAs($this->superadmin)->put(route('inventory.update', $sparepart2), [
            'name' => $sparepart1->name,
            'part_number' => 'DUP-123', // Sama dengan sparepart1
            'brand_id' => $sparepart1->brand_id,
            'category_id' => $sparepart1->category_id,
            'location_id' => $sparepart1->location_id,
            'condition' => $sparepart1->condition,
            'age' => $sparepart1->age,
            'color' => $sparepart1->color,
            'type' => $sparepart1->type,
            'stock' => 10,
            'minimum_stock' => $sparepart1->minimum_stock,
            'unit' => $sparepart1->unit,
            'price' => $sparepart1->price,
            'status' => $sparepart1->status,
        ]);

        // Harus dikembalikan (redirect) dengan session untuk memicu modal
        $response->assertRedirect();
        $response->assertSessionHas('duplicate_detected');
    }

    public function test_update_merge_confirmed_menggabungkan_stok()
    {
        $sparepart1 = Sparepart::factory()->create(['part_number' => 'DUP-123', 'stock' => 5]);
        $sparepart2 = Sparepart::factory()->create(['part_number' => 'DUP-456', 'stock' => 10]);

        $response = $this->actingAs($this->superadmin)->put(route('inventory.update', $sparepart2), [
            'name' => $sparepart1->name,
            'part_number' => 'DUP-123',
            'brand_id' => $sparepart1->brand_id,
            'category_id' => $sparepart1->category_id,
            'location_id' => $sparepart1->location_id,
            'condition' => $sparepart1->condition,
            'age' => $sparepart1->age,
            'color' => $sparepart1->color,
            'type' => $sparepart1->type,
            'stock' => 10,
            'minimum_stock' => $sparepart1->minimum_stock,
            'unit' => $sparepart1->unit,
            'price' => $sparepart1->price,
            'status' => $sparepart1->status,
            'merge_confirmed' => 'true', // Konfirmasi merge
            'duplicate_id' => $sparepart1->id,
        ]);

        $response->assertRedirect(route('inventory.index'));

        // Sparepart2 dihapus (karena digabung ke Sparepart1)
        $this->assertSoftDeleted('spareparts', ['id' => $sparepart2->id]);

        // Sparepart1 stoknya bertambah (5 + 10)
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart1->id,
            'stock' => 15,
        ]);
    }

    public function test_bulk_print_menolak_jika_melebihi_100_item()
    {
        // Membuat string ID dummy sebanyak 101 item
        $ids = implode(',', range(1, 101));

        $response = $this->actingAs($this->superadmin)->get(route('inventory.qr.bulk-print', ['ids' => $ids]));

        $response->assertSessionHas('error'); // Ditolak karena > 100
    }

    public function test_bulk_destroy_menolak_jika_id_kosong()
    {
        $response = $this->actingAs($this->superadmin)->deleteJson(route('inventory.bulk-destroy'), [
            'ids' => [],
        ]);

        $response->assertStatus(422); // 422 Unprocessable Entity karena array kosong
    }
}
