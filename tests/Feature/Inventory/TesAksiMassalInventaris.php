<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesAksiMassalInventaris extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $admin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->operator = User::factory()->create(['role' => 'operator']);
    }

    public function test_superadmin_dapat_memulihkan_item_secara_massal()
    {
        $items = Sparepart::factory()->count(3)->create();
        foreach ($items as $item) {
            $item->delete();
        }

        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->superadmin)
            ->post(route('inventory.bulk-restore'), ['ids' => $ids]);

        $response->assertRedirect();

        foreach ($items as $item) {
            $this->assertDatabaseHas('spareparts', [
                'id' => $item->id,
                'deleted_at' => null,
            ]);
        }
    }

    public function test_superadmin_dapat_menghapus_permanen_item_secara_massal()
    {
        $items = Sparepart::factory()->count(3)->create();
        foreach ($items as $item) {
            $item->delete();
        }

        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->superadmin)
            ->delete(route('inventory.bulk-force-delete'), ['ids' => $ids]);

        $response->assertRedirect();

        foreach ($items as $item) {
            $this->assertDatabaseMissing('spareparts', ['id' => $item->id]);
        }
    }

    public function test_operator_tidak_dapat_memulihkan_secara_massal()
    {
        $items = Sparepart::factory()->count(3)->create();
        foreach ($items as $item) {
            $item->delete();
        }

        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->operator)
            ->post(route('inventory.bulk-restore'), ['ids' => $ids]);

        $response->assertForbidden();
    }

    public function test_operator_tidak_dapat_menghapus_permanen_secara_massal()
    {
        $items = Sparepart::factory()->count(3)->create();
        foreach ($items as $item) {
            $item->delete();
        }

        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->operator)
            ->delete(route('inventory.bulk-force-delete'), ['ids' => $ids]);

        $response->assertForbidden();
    }
}
