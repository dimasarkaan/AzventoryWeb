<?php

namespace Tests\Feature\Users;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TesManajemenUserTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $admin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);
    }

    public function test_render_daftar_user_tidak_menampilkan_akun_sendiri()
    {
        $response = $this->actingAs($this->superadmin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertViewHas('users', function ($users) {
            return ! $users->contains('id', $this->superadmin->id);
        });
        $response->assertSee($this->operator->email);
    }

    public function test_pembuatan_akun_baru_dan_pembuatan_username_otomatis()
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'role' => UserRole::OPERATOR->value,
            'jabatan' => 'Staff IT',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'password_changed_at' => null, // Harus null karena baru dibuat
        ]);

        $user = User::where('email', 'budi.santoso@example.com')->first();
        $this->assertTrue(str_starts_with($user->username, 'budi.santoso')); // Validasi prefix username
    }

    public function test_ubah_data_pengguna_lain()
    {
        $response = $this->actingAs($this->superadmin)->put(route('users.update', $this->operator), [
            'name' => 'Operator Edited',
            'email' => $this->operator->email,
            'username' => $this->operator->username,
            'role' => UserRole::ADMIN->value,
            'jabatan' => 'Head Staff',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $this->operator->id,
            'name' => 'Operator Edited',
            'role' => UserRole::ADMIN->value,
        ]);
    }

    public function test_hapus_sementara_soft_delete()
    {
        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $this->operator));

        $response->assertRedirect(route('users.index'));
        $this->assertSoftDeleted('users', [
            'id' => $this->operator->id,
        ]);
    }

    public function test_pemulihan_dan_hapus_permanen()
    {
        // 1. Soft Delete dulu
        $this->operator->delete();
        $this->assertSoftDeleted('users', ['id' => $this->operator->id]);

        // 2. Restore
        $responseRestore = $this->actingAs($this->superadmin)->patch(route('users.restore', $this->operator->uuid));
        $responseRestore->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $this->operator->id,
            'deleted_at' => null,
        ]);

        // 3. Delete Permanen (Soft delete lagi dulu)
        $this->operator->delete();
        $responseForce = $this->actingAs($this->superadmin)->delete(route('users.force-delete', $this->operator->uuid));
        $responseForce->assertRedirect();

        $this->assertDatabaseMissing('users', [
            'id' => $this->operator->id,
        ]);
    }

    public function test_reset_kata_sandi_ke_default()
    {
        $response = $this->actingAs($this->superadmin)->patch(route('users.reset-password', $this->operator));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user = $this->operator->fresh();
        $this->assertNull($user->password_changed_at); // dipaksa ganti password lagi
        $this->assertTrue(Hash::check('password123', $user->password)); // memastikan password
    }

    public function test_anti_bunuh_diri_mencegah_edit_dan_hapus_diri_sendiri()
    {
        // Edit Diri Sendiri (Hanya lewat profil, bukan via User Management)
        $resEdit = $this->actingAs($this->superadmin)->put(route('users.update', $this->superadmin), [
            'name' => 'Name',
            'email' => 'email@g.com',
            'username' => 'user',
            'role' => UserRole::OPERATOR->value,
            'jabatan' => 'Head Staff',
            'status' => 'nonaktif', // mencoba nonaktif
        ]);
        $resEdit->assertSessionHasErrors(['role', 'status']);

        // Reset Password Diri Sendiri
        $resReset = $this->actingAs($this->superadmin)->patch(route('users.reset-password', $this->superadmin));
        $resReset->assertSessionHas('error');

        // Soft Delete Diri Sendiri
        $resDel = $this->actingAs($this->superadmin)->delete(route('users.destroy', $this->superadmin));
        $resDel->assertStatus(403);
    }

    public function test_pencegahan_hutang_tolak_hapus_jika_ada_pinjaman()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        Borrowing::factory()->create([
            'user_id' => $this->operator->id,
            'sparepart_id' => $sparepart->id,
            'status' => 'borrowed', // Masih dipinjam
        ]);

        // Coba soft delete
        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $this->operator));
        $response->assertSessionHas('error', 'Tidak dapat menghapus pengguna karena masih memiliki pinjaman barang aktif.');

        $this->assertDatabaseHas('users', ['id' => $this->operator->id, 'deleted_at' => null]);
    }

    public function test_validasi_role_operator_forbidden_akses_manajemen_user()
    {
        $response = $this->actingAs($this->operator)->get(route('users.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->operator)->get(route('users.create'));
        $responseCreate->assertStatus(403);
    }
}
