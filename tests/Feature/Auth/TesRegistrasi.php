<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesRegistrasi extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_registrasi_dapat_tampil(): void
    {
        $this->markTestSkipped('Registration is disabled.');
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_user_baru_dapat_registrasi(): void
    {
        $this->markTestSkipped('Registration is disabled.');
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

