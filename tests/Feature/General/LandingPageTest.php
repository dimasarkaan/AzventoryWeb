<?php

namespace Tests\Feature\General;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    #[Test]
    public function halaman_landing_dapat_diakses()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Solusi Manajemen Stok');
        $response->assertSee('Azzahra Computer');
    }

    #[Test]
    public function halaman_landing_memiliki_tombol_cta_yang_benar()
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

    #[Test]
    public function halaman_landing_memiliki_kartu_fitur()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Terpusat');
        $response->assertSee('QR Code Scanner');
        $response->assertSee('Monitoring Aktivitas');
    }

    #[Test]
    public function halaman_landing_memiliki_kredit_footer()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dimas Arkaan');
        $response->assertSee(date('Y'));
    }
}
