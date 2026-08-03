<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesAksiMassalInventarisTest extends TestCase
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

        $log = \App\Models\ActivityLog::where('action', 'Pemulihan Massal')->latest()->first();
        $this->assertNotNull($log);
        $this->assertNotNull($log->properties);
        $this->assertStringContainsString($items[0]->name, json_encode($log->properties));
        $this->assertStringContainsString($items[1]->name, json_encode($log->properties));
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

        $log = \App\Models\ActivityLog::where('action', 'Hapus Permanen Massal')->latest()->first();
        $this->assertNotNull($log);
        $this->assertNotNull($log->properties);
        $this->assertStringContainsString($items[0]->name, json_encode($log->properties));
        $this->assertStringContainsString($items[1]->name, json_encode($log->properties));
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

    public function test_superadmin_dapat_menghapus_item_ke_tong_sampah_secara_massal()
    {
        $items = Sparepart::factory()->count(2)->create();
        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('inventory.bulk-destroy'), ['ids' => $ids]);

        $response->assertOk();

        foreach ($items as $item) {
            $this->assertSoftDeleted('spareparts', ['id' => $item->id]);
        }

        $log = \App\Models\ActivityLog::where('action', 'Hapus Massal (Soft)')->latest()->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString($items[0]->name, json_encode($log->properties));
        $this->assertStringContainsString($items[1]->name, json_encode($log->properties));
    }

    public function test_superadmin_dapat_mengosongkan_tong_sampah()
    {
        $items = Sparepart::factory()->count(2)->create();
        foreach ($items as $item) {
            $item->delete();
        }

        $response = $this->actingAs($this->superadmin)
            ->delete(route('inventory.force-delete-all'));

        $response->assertRedirect();

        foreach ($items as $item) {
            $this->assertDatabaseMissing('spareparts', ['id' => $item->id]);
        }

        $log = \App\Models\ActivityLog::where('action', 'Tong Sampah Dikosongkan')->latest()->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString($items[0]->name, json_encode($log->properties));
        $this->assertStringContainsString($items[1]->name, json_encode($log->properties));
    }

    public function test_superadmin_dapat_mencetak_label_secara_massal()
    {
        $items = Sparepart::factory()->count(2)->create();
        $ids = $items->pluck('id')->toArray();
        $counts = [$items[0]->id => 2, $items[1]->id => 3];

        $response = $this->actingAs($this->superadmin)
            ->postJson(route('inventory.qr.log'), [
                'ids' => $ids,
                'counts' => $counts,
                'total' => 5,
            ]);

        $response->assertOk();

        $log = \App\Models\ActivityLog::where('action', 'Cetak Label')->latest()->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString($items[0]->name, json_encode($log->properties));
        $this->assertStringContainsString($items[1]->name, json_encode($log->properties));
    }
}
