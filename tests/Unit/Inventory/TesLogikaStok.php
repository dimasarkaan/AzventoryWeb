<?php

namespace Tests\Unit\Inventory;

use PHPUnit\Framework\TestCase;

class TesLogikaStok extends TestCase
{
    /**
     * Tes logika keamanan stok sederhana (Level Unit).
     * Ini menguji logika secara terisolasi tanpa menyentuh database.
     */
    public function test_periksa_apakah_stok_rendah()
    {
        $currentStock = 2;
        $minimumStock = 5;

        $isLow = $currentStock < $minimumStock;

        $this->assertTrue($isLow, 'Stok harus dianggap rendah jika kurang dari minimum.');
    }

    public function test_periksa_apakah_stok_aman()
    {
        $currentStock = 10;
        $minimumStock = 5;

        $isLow = $currentStock < $minimumStock;

        $this->assertFalse($isLow, 'Stok harus aman jika lebih besar dari minimum.');
    }

    public function test_logika_stok_tidak_boleh_negatif()
    {
        $stock = 0;
        $reduction = 5;

        // Mensimulasikan logika yang mungkin ada di kelas Service
        $newStock = max(0, $stock - $reduction);

        $this->assertEquals(0, $newStock, 'Stok tidak boleh di bawah nol.');
    }
}

