<?php

namespace Tests\Feature\Inventory;

use App\Models\Borrowing;
use App\Models\BorrowingReturn;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesPeminjamanKompleks extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Pengembalian parsial secara bertahap menghitung sisa dengan benar.
     */
    public function test_pengembalian_parsial_bertahap_menghitung_sisa_dengan_benar()
    {
        $user = User::factory()->create();
        $sparepart = Sparepart::factory()->create(['stock' => 10]);

        // Pinjam 5
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 5,
            'status' => 'borrowed',
            'borrowed_at' => now(),
        ]);

        $this->assertEquals(5, $borrowing->remaining_quantity);

        // Kembalikan 2
        BorrowingReturn::create([
            'borrowing_id' => $borrowing->id,
            'quantity' => 2,
            'return_date' => now(),
            'condition' => 'good',
        ]);

        $this->assertEquals(3, $borrowing->fresh()->remaining_quantity);

        // Kembalikan lagi 3 (total 5)
        BorrowingReturn::create([
            'borrowing_id' => $borrowing->id,
            'quantity' => 3,
            'return_date' => now(),
            'condition' => 'good',
        ]);

        $this->assertEquals(0, $borrowing->fresh()->remaining_quantity);
    }

    /**
     * Test: Integritas data saat Sparepart di-soft delete.
     */
    public function test_integritas_data_saat_barang_dihapus_soft_delete()
    {
        $sparepart = Sparepart::factory()->create(['name' => 'Barang Rahasia']);
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => User::factory()->create()->id,
            'borrower_name' => 'Someone',
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
        ]);

        // Soft delete barang
        $sparepart->delete();

        $this->assertSoftDeleted('spareparts', ['id' => $sparepart->id]);

        // Cek apakah borrowing masih bisa mengakses nama barang via withTrashed()
        $borrowingLoad = Borrowing::find($borrowing->id);

        // Eloquent relationship usually returns null if deleted, unless we specify withTrashed in model rel
        // If not specified in model, it returns null.
        $this->assertNull($borrowingLoad->sparepart);

        // Verify we can still find it if we use withTrashed manually or if relationship handles it
        $sparepartDeleted = Sparepart::withTrashed()->find($sparepart->id);
        $this->assertNotNull($sparepartDeleted);
        $this->assertEquals('Barang Rahasia', $sparepartDeleted->name);
    }

    /**
     * Test: Integritas data saat User (peminjam) dihapus.
     */
    public function test_integritas_data_saat_user_dihapus()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $borrowing = Borrowing::create([
            'sparepart_id' => Sparepart::factory()->create()->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
        ]);

        // Delete user (User model Azventory usually has soft deletes too, let's verify)
        // Check User.php if it uses SoftDeletes
        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'deleted_at' => null]);

        // Relationship check
        $this->assertNull($borrowing->fresh()->user);
    }
}
