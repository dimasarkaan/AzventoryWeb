<?php

namespace Tests\Feature\General;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_views_are_localized()
    {
        // Login Page (Landing page redirects here via dashboard)
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali!'); // ui.auth_welcome_title
        $response->assertSee('Username atau Email'); // ui.auth_label_login
    }

    /** @test */
    public function superadmin_views_are_localized()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        // Dashboard
        $response = $this->actingAs($user)->get(route('dashboard.superadmin'));
        $response->assertStatus(200);
        $response->assertSee('Overview Status Inventaris'); // localized string in dashboard

        // Inventory Index
        $response = $this->actingAs($user)->get(route('inventory.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Inventaris'); // localized title
        $response->assertSee('Tambah Inventaris'); // localized button

        // Profile
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil Saya'); // ui.profile_title
        $response->assertSee('Informasi Profil'); // ui.profile_info_title
    }
}
