<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Mail\MonthlyReportMail;
use App\Models\User;
use App\Services\ExcelExportService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-monthly-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim laporan bulanan otomatis ke email Superadmin';

    /**
     * Execute the console command.
     */
    public function handle(ReportService $reportService, ExcelExportService $excelService)
    {
        $this->info('Memulai pembuatan laporan bulanan...');

        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $monthName = $lastMonth->translatedFormat('F Y');
        
        // Resolve date range for last month
        [$startDate, $endDate] = $reportService->resolveDateRange('last_month');

        $reports = [
            'inventory_list' => ['title' => 'Daftar Inventaris Terkini', 'start' => null, 'end' => null],
            'stock_mutation' => ['title' => 'Riwayat Mutasi Stok', 'start' => $startDate, 'end' => $endDate],
            'borrowing_history' => ['title' => 'Riwayat Peminjaman', 'start' => $startDate, 'end' => $endDate],
            'low_stock' => ['title' => 'Laporan Stok Menipis', 'start' => null, 'end' => null],
        ];

        $attachments = [];

        foreach ($reports as $type => $config) {
            $this->info("Generating report: {$config['title']}...");
            
            $queryResult = $reportService->getReportQuery($type, 'all', $config['start'], $config['end']);
            $query = $queryResult['query'];

            if (!$query) continue;

            $filename = str_replace(' ', '_', $config['title']) . '_' . $lastMonth->format('m_Y');
            
            // Generate Excel file
            $spreadsheet = $this->generateSpreadsheet($excelService, $type, $query);
            $path = $excelService->saveToFile($spreadsheet, $filename);
            
            $attachments[$filename . '.xlsx'] = $path;
        }

        if (empty($attachments)) {
            $this->warn('Tidak ada data laporan untuk dikirim.');
            return 0;
        }

        $superadmins = User::where('role', UserRole::SUPERADMIN)->get();

        if ($superadmins->isEmpty()) {
            $this->error('Tidak ada Superadmin ditemukan.');
            return 1;
        }

        foreach ($superadmins as $admin) {
            $this->info("Mengirim email ke: {$admin->email}");
            Mail::to($admin->email)->send(new MonthlyReportMail($admin, $attachments, $monthName));
        }

        $this->info('Laporan bulanan berhasil dikirim.');

        // Cleanup temp files (optional, or let them stay in public/reports for a while)
        // For now, I'll leave them or I can delete them after loop.
        // But since Mail is queued usually, we should not delete them immediately if it's sent synchronously.
        // In local, it's usually sync.
        
        return 0;
    }

    protected function generateSpreadsheet($excelService, $type, $query)
    {
        // We need to access the exact same logic as in ExcelExportService
        // Since we can't easily call the private methods, we might need to expose them or replicate.
        // Replicating is safer for now but suboptimal. 
        // Better: refactor ExcelExportService even more.
        
        // I'll use Reflection or just a hacky way since I already refactored it.
        // Actually, I'll just use the public methods I refactored.
        
        // Wait, I didn't make them return the spreadsheet.
        // I'll update ExcelExportService once more to have "getSpreadsheet" methods.

        return match ($type) {
            'inventory_list' => $this->callPrivate($excelService, 'generateInventoryListSpreadsheet', [$query]),
            'stock_mutation' => $this->callPrivate($excelService, 'generateStockMutationSpreadsheet', [$query]),
            'borrowing_history' => $this->callPrivate($excelService, 'generateBorrowingHistorySpreadsheet', [$query]),
            'low_stock' => $this->callPrivate($excelService, 'generateLowStockSpreadsheet', [$query]),
        };
    }
    
    private function callPrivate($object, $method, $args) {
        $reflection = new \ReflectionMethod(get_class($object), $method);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $args);
    }
}
