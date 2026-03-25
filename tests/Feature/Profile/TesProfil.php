<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TesProfil extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_profil_dapat_tampil(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_informasi_profil_dapat_diperbarui(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('testuser', $user->username);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_status_verifikasi_email_tidak_berubah_ketika_alamat_email_tidak_berubah(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => $user->username,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_dapat_menghapus_akun_mereka(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }

    public function test_password_yang_benar_harus_diberikan_untuk_menghapus_akun(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_halaman_inventaris_saya_dapat_tampil(): void
    {
        $user = User::factory()->create();
        $sparepart = \App\Models\Sparepart::factory()->create();
        
        // Buat pinjaman
        \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($user)->get(route('profile.inventory'));

        $response->assertStatus(200);
        $response->assertSee($sparepart->name);
    }

    public function test_superadmin_terakhir_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertRedirect('/profile')
            ->assertSessionHas('error', 'Anda tidak dapat menghapus akun karena Anda adalah satu-satunya Superadmin yang tersisa di sistem.');

        $this->assertNotNull($user->fresh());
    }

    public function test_superadmin_dapat_menghapus_akun_jika_ada_superadmin_lain(): void
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);
        User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]); // Buat superadmin lain

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $this->assertSoftDeleted($user);
    }

    public function test_foto_profil_dapat_dihapus(): void
    {
        $user = User::factory()->create([
            'avatar' => 'avatars/test.jpg'
        ]);
        
        // Buat file palsu di storage
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Storage::disk('public')->put('avatars/test.jpg', 'content');

        $response = $this
            ->actingAs($user)
            ->delete('/profile/avatar');

        $response->assertRedirect();
        $response->assertSessionHas('status', 'avatar-deleted');

        $user->refresh();
        $this->assertNull($user->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing('avatars/test.jpg');
    }

    public function test_user_tidak_dapat_menghapus_akun_jika_memiliki_pinjaman_aktif(): void
    {
        $user = User::factory()->create();
        $sparepart = \App\Models\Sparepart::factory()->create();
        
        // Buat peminjaman aktif
        \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertRedirect('/profile')
            ->assertSessionHas('error', fn ($value) => str_contains($value, 'belum dikembalikan'));

        $this->assertNotNull($user->fresh());
    }

    public function test_update_profile_dengan_nomor_wa_invalid(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '12345', // Invalid
            ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_update_password_sama_dengan_password_saat_ini(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password123'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'old-password123',
                'password' => 'old-password123',
                'password_confirmation' => 'old-password123',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
        $this->assertTrue(Hash::check('old-password123', $user->fresh()->password));
    }

    public function test_update_username_dengan_format_invalid(): void
    {
        $user = User::factory()->create(['is_username_changed' => false]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'username' => 'invalid username!', // Space and !
            ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_username_otomatis_menjadi_lowercase(): void
    {
        $user = User::factory()->create(['is_username_changed' => false]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'username' => 'DIMAS_ARKAAN', // Uppercase
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('dimas_arkaan', $user->fresh()->username);
    }

    public function test_username_minimal_3_karakter(): void
    {
        $user = User::factory()->create(['is_username_changed' => false]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'username' => 'di', // Too short
            ]);

        $response->assertSessionHasErrors('username');
    }
}

