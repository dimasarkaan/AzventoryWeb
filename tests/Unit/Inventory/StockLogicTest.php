<?php

namespace Tests\Unit\Inventory;

use PHPUnit\Framework\TestCase;

class StockLogicTest extends TestCase
{
    /**
     * Test simple stock safety logic (Unit Level).
     * This tests logic in isolation without touching the database.
     */
    public function test_check_if_stock_is_low()
    {
        $currentStock = 2;
        $minimumStock = 5;

        $isLow = $currentStock < $minimumStock;

        $this->assertTrue($isLow, 'Stock should be considered low if less than minimum.');
    }

    public function test_check_if_stock_is_safe()
    {
        $currentStock = 10;
        $minimumStock = 5;

        $isLow = $currentStock < $minimumStock;

        $this->assertFalse($isLow, 'Stock should be safe if greater than minimum.');
    }

    public function test_stock_cannot_be_negative_logic()
    {
        $stock = 0;
        $reduction = 5;
        
        // Simulating logic that might exist in a Service class
        $newStock = max(0, $stock - $reduction);

        $this->assertEquals(0, $newStock, 'Stock should not go below zero.');
    }
}
