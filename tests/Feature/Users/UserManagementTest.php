<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat Superadmin
        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(), // Lewati middleware
            'password' => 'password',
        ]);
    }

    public function test_superadmin_can_view_user_list()
    {
        $response = $this->actingAs($this->superadmin)->get(route('users.index'));
        $response->assertStatus(200);
    }

    public function test_superadmin_can_create_new_admin()
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'jabatan' => 'Kepala Gudang',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'jabatan' => 'Kepala Gudang',
        ]);
    }

    public function test_superadmin_can_create_new_operator()
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'email' => 'newoperator@example.com',
            'role' => 'operator',
            'jabatan' => 'Staff Gudang',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newoperator@example.com',
            'role' => 'operator',
            'jabatan' => 'Staff Gudang',
        ]);
    }

    public function test_superadmin_can_delete_user()
    {
        // Buat user untuk dihapus
        $targetUser = User::factory()->create([
            'role' => 'operator',
            'password_changed_at' => now(), 
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $targetUser));

        $response->assertRedirect(route('users.index'));
        $this->assertSoftDeleted('users', [
            'id' => $targetUser->id,
        ]);
    }
    public function test_superadmin_can_reset_user_password()
    {
        $targetUser = User::factory()->create([
            'role' => 'admin',
            'password_changed_at' => now(),
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($this->superadmin)->patch(route('users.reset-password', $targetUser));

        $response->assertRedirect();
        
        // Verifikasi password berubah (default adalah 'password123')
        $targetUser->refresh();
        $this->assertTrue(Hash::check('password123', $targetUser->password));
    }
}
