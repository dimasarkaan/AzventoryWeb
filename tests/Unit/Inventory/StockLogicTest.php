<?php

namespace Tests\Unit\Inventory;

use PHPUnit\Framework\TestCase;

class StockLogicTest extends TestCase
{
    /**
     * Tes logika keamanan stok sederhana (Level Unit).
     * Ini menguji logika secara terisolasi tanpa menyentuh database.
     */
    public function test_check_if_stock_is_low()
    {
        $currentStock = 2;
        $minimumStock = 5;

        $isLow = $currentStock < $minimumStock;

        $this->assertTrue($isLow, 'Stok harus dianggap rendah jika kurang dari minimum.');
    }

    public function test_check_if_stock_is_safe()
    {
        $currentStock = 10;
        $minimumStock = 5;

        $isLow = $currentStock < $minimumStock;

        $this->assertFalse($isLow, 'Stok harus aman jika lebih besar dari minimum.');
    }

    public function test_stock_cannot_be_negative_logic()
    {
        $stock = 0;
        $reduction = 5;
        
        // Mensimulasikan logika yang mungkin ada di kelas Service
        $newStock = max(0, $stock - $reduction);

        $this->assertEquals(0, $newStock, 'Stok tidak boleh di bawah nol.');
    }
}
