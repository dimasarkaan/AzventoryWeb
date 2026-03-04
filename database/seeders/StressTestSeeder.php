<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StressTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (! $user) {
            $user = User::factory()->create(['role' => 'superadmin']);
        }

        $sparepart = Sparepart::first();
        if (! $sparepart) {
            $sparepart = Sparepart::create([
                'name' => 'Stress Test Item',
                'part_number' => 'STRESS-001',
                'brand' => 'StressBrand',
                'category' => 'Testing',
                'location' => 'Warehouse A',
                'type' => 'asset',
                'stock' => 1000,
                'minimum_stock' => 10,
                'condition' => 'Baik',
                'age' => 'Baru',
                'status' => 'aktif',
            ]);
        }

        $this->command->info('Starting stress test seeding: 10,000 logs...');

        $batchSize = 1000;
        $totalLogs = 10000;

        for ($i = 0; $i < $totalLogs / $batchSize; $i++) {
            $logs = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $type = rand(0, 1) ? 'masuk' : 'keluar';
                $quantity = rand(1, 50);
                $date = Carbon::now()->subDays(rand(0, 365));

                $logs[] = [
                    'sparepart_id' => $sparepart->id,
                    'user_id' => $user->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reason' => 'Stress test log #'.($i * $batchSize + $j),
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
            StockLog::insert($logs);
            $this->command->info('Inserted batch '.($i + 1));
        }

        $this->command->info('Stress test seeding completed successfully.');
    }
}
