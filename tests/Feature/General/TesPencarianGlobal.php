<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk GlobalSearchController.
 * Memastikan fitur pencarian global berjalan dengan benar
 * untuk semua role dan kondisi berbeda.
 */
class TesPencarianGlobal extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    #[Test]
    public function pencarian_terlalu_pendek_mengembalikan_array_kosong()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('global-search').'?query=a');

        $response->assertOk()
            ->assertJson([
                'menus' => [],
                'spareparts' => [],
                'users' => [],
            ]);
    }

    #[Test]
    public function pencarian_sparepart_mengembalikan_hasil_yang_sesuai()
    {
        $sparepart = Sparepart::factory()->create([
            'name' => 'Filter Udara Premium',
            'part_number' => 'FA-9999',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('global-search').'?query=Filter');

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Filter Udara Premium']);
    }

    #[Test]
    public function pencarian_tidak_mengembalikan_data_user_untuk_role_admin()
    {
        $user = User::factory()->create(['name' => 'Budi Santoso', 'role' => UserRole::OPERATOR]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('global-search').'?query=Budi');

        $response->assertOk();
        $data = $response->json('users');
        $this->assertEmpty($data, 'Admin tidak seharusnya mendapat hasil pencarian user.');
    }

    #[Test]
    public function superadmin_dapat_mencari_user_dari_pencarian_global()
    {
        $user = User::factory()->create(['name' => 'Candra Operator Khusus', 'role' => UserRole::OPERATOR]);

        $response = $this->actingAs($this->superadmin)
            ->getJson(route('global-search').'?query=Candra');

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Candra Operator Khusus']);
    }

    #[Test]
    public function pencarian_mengembalikan_menu_yang_relevan_untuk_superadmin()
    {
        $response = $this->actingAs($this->superadmin)
            ->getJson(route('global-search').'?query=Laporan');

        $response->assertOk();
        $menus = $response->json('menus');
        $this->assertNotEmpty($menus);
        $menuTitles = array_column($menus, 'title');
        $this->assertContains('Laporan', $menuTitles);
    }

    #[Test]
    public function pencarian_tanpa_autentikasi_diarahkan_ke_login()
    {
        $response = $this->getJson(route('global-search').'?query=Filter');
        $response->assertUnauthorized();
    }
}

