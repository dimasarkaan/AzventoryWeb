<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesBatasRoleApi extends TestCase
{
    use RefreshDatabase;

    protected User $operator;

    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->sparepart = Sparepart::factory()->create();
    }

    private function operatorTokenHeader(): array
    {
        $token = $this->operator->createToken('op-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    #[Test]
    public function operator_tidak_dapat_menambah_inventaris_via_api()
    {
        $response = $this->postJson(route('api.inventory.store'), [
            'name' => 'Illegal Item',
            'part_number' => 'PN-ILLEGAL',
            'brand' => 'Evil',
            'location' => 'Rak Z',
            'type' => 'asset',
            'stock' => 100,
            'category' => 'Tools',
            'condition' => 'Baik',
            'status' => 'aktif',
        ], $this->operatorTokenHeader());

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_mengubah_inventaris_via_api()
    {
        $response = $this->putJson(route('api.inventory.update', $this->sparepart->id), [
            'name' => 'Hack Name',
        ], $this->operatorTokenHeader());

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_menyesuaikan_stok_langsung_via_api()
    {
        $response = $this->putJson(route('api.inventory.adjust-stock', $this->sparepart->id), [
            'type' => 'increment',
            'quantity' => 100,
        ], $this->operatorTokenHeader());

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_menghapus_inventaris_via_api()
    {
        $response = $this->deleteJson(route('api.inventory.destroy', $this->sparepart->id), [], $this->operatorTokenHeader());

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tetap_dapat_melihat_daftar_inventaris_via_api()
    {
        $response = $this->getJson(route('api.inventory.index'), $this->operatorTokenHeader());

        $response->assertStatus(200);
    }
}
