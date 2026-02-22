<?php

namespace Tests\Feature\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    /** @test */
    public function landing_page_is_accessible()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Solusi Manajemen Stok');
        $response->assertSee('Azzahra Computer');
    }

    /** @test */
    public function landing_page_has_correct_cta_buttons()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Cek tombol Masuk Aplikasi
        $response->assertSee('Masuk Aplikasi');
        $response->assertSee(route('login'));

        // Cek tombol Fitur Utama
        $response->assertSee('Fitur Utama');
        $response->assertSee('#features');
    }

    /** @test */
    public function landing_page_has_feature_cards()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Terpusat');
        $response->assertSee('QR Code Scanner');
        $response->assertSee('Monitoring Aktivitas');
    }

    /** @test */
    public function landing_page_has_footer_credit()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dimas Arkaan');
        $response->assertSee(date('Y'));
    }
}
