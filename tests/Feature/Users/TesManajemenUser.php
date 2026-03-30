<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase; // Import Hash facade

class TesManajemenUser extends TestCase
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

    public function test_superadmin_dapat_melihat_daftar_user()
    {
        $response = $this->actingAs($this->superadmin)->get(route('users.index'));
        $response->assertStatus(200);
    }

    public function test_superadmin_dapat_membuat_admin_baru()
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'jabatan' => 'Kepala Gudang',
            'status' => 'aktif',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'jabatan' => 'Kepala Gudang',
        ]);
    }

    public function test_superadmin_dapat_membuat_operator_baru()
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'name' => 'New Operator',
            'email' => 'newoperator@example.com',
            'role' => 'operator',
            'jabatan' => 'Staff Gudang',
            'status' => 'aktif',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newoperator@example.com',
            'role' => 'operator',
            'jabatan' => 'Staff Gudang',
        ]);
    }

    public function test_superadmin_dapat_menghapus_user()
    {
        // Buat user untuk dihapus
        $targetUser = User::factory()->create([
            'role' => 'operator',
            'password_changed_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $targetUser));

        $response->assertRedirect();
        $this->assertSoftDeleted('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_superadmin_dapat_mereset_password_user()
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

    public function test_superadmin_dapat_memperbarui_data_user()
    {
        $targetUser = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'operator',
            'jabatan' => 'Staff',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->superadmin)->put(route('users.update', $targetUser), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin',
            'jabatan' => 'Kepala',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin',
            'jabatan' => 'Kepala',
            'status' => 'nonaktif',
        ]);
    }

    public function test_user_tidak_dapat_dihapus_jika_memiliki_pinjaman_aktif()
    {
        $targetUser = User::factory()->create(['role' => 'operator']);

        // Buat pinjaman aktif
        $sparepart = \App\Models\Sparepart::factory()->create();
        \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $targetUser->id,
            'borrower_name' => $targetUser->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $targetUser));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $targetUser->id]);
    }

    public function test_bulk_force_delete_melompati_user_dengan_pinjaman_aktif()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create(); // User ini punya pinjaman

        $sparepart = \App\Models\Sparepart::factory()->create();
        \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user2->id,
            'borrower_name' => $user2->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $user1->delete();
        $user2->delete();

        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.bulk-force-delete'), [
                'ids' => [$user1->id, $user2->id],
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertSoftDeleted('users', ['id' => $user2->id]); // Masih ada di sampah (tidak terhapus permanen)
    }

    public function test_superadmin_tidak_dapat_menghapus_diri_sendiri_secara_bulk()
    {
        $this->superadmin->delete(); // Soft delete diri sendiri (mungkin via DB atau logic lain, tapi destroy melarangnya)

        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.bulk-force-delete'), [
                'ids' => [$this->superadmin->id],
            ]);

        $this->assertSoftDeleted('users', ['id' => $this->superadmin->id]);
        $this->assertDatabaseHas('users', ['id' => $this->superadmin->id]);
    }

    public function test_superadmin_melihat_link_whatsapp_jika_user_punya_nomor_telepon()
    {
        $targetUser = User::factory()->create([
            'phone' => '08123456789',
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('08123456789');
    }
}
