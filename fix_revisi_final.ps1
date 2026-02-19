$path = "resources\views\dashboard\superadmin.blade.php"
$content = Get-Content $path -Raw -Encoding UTF8

# Block 1: Forecasting Checkbox
$forecastBlock = @"
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showForecast" @change="toggle('showForecast')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_forecasting') }}</span>
                                </label>
"@

# Block 2: Sticky Class
$stickyBlock = 'class="flex flex-col gap-2 sticky top-0 z-40 bg-white/95 backdrop-blur-sm pt-1 pb-2 -mx-4 px-4 sm:relative sm:top-auto sm:z-auto sm:bg-transparent sm:backdrop-filter-none sm:mx-0 sm:px-0 sm:pt-0 sm:pb-0">'

# Block 3: Quick Summary
$quickSummaryBlock = @"
            <div class="md:hidden overflow-x-auto -mx-4 px-4 mb-4">
                <div class="flex items-center gap-2 min-w-max">
                    <div class="flex items-center gap-1.5 bg-primary-50 text-primary-700 border border-primary-100 rounded-full px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="text-xs font-semibold whitespace-nowrap">{{ $totalSpareparts }} Barang</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-success-50 text-success-700 border border-success-100 rounded-full px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span class="text-xs font-semibold whitespace-nowrap">{{ number_format($totalStock) }} Stok</span>
                    </div>
                    @if($totalOverdueCount > 0)
                    <div class="flex items-center gap-1.5 bg-danger-50 text-danger-700 border border-danger-200 rounded-full px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-semibold whitespace-nowrap">{{ $totalOverdueCount }} Terlambat</span>
                    </div>
                    @endif
                    @if($pendingApprovalsCount > 0)
                    <a href="{{ route('inventory.stock-approvals.index') }}" class="flex items-center gap-1.5 bg-warning-50 text-warning-700 border border-warning-200 rounded-full px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-xs font-semibold whitespace-nowrap">{{ $pendingApprovalsCount }} Perlu Disetujui</span>
                    </a>
                    @endif
                </div>
            </div>
"@

# Helper function to remove CRLF/LF distinction for matching
function Normalize-Text($text) {
    return $text -replace "\r\n", "`n" -replace "\r", "`n"
}

$contentNorm = Normalize-Text $content
$forecastBlockNorm = Normalize-Text $forecastBlock
$stickyBlockNorm = Normalize-Text $stickyBlock
$quickSummaryBlockNorm = Normalize-Text $quickSummaryBlock

if ($contentNorm.Contains($forecastBlockNorm)) {
    $content = $content.Replace($forecastBlock, "")
    Write-Host "Fixed: Forecasting Checkbox removed."
} else {
    Write-Host "Warning: Forecasting Checkbox not found (exact match)."
}

if ($content.Contains($stickyBlock)) {
    $content = $content.Replace($stickyBlock, 'class="flex flex-col gap-2">')
    Write-Host "Fixed: Sticky Class removed."
} else {
    # Fallback search if exact string fails (maybe attributes order?)
    if ($content -match 'sticky top-0 z-40') {
        # regex replace safely
        $content = $content -replace 'class="[^"]*sticky top-0 z-40[^"]*"', 'class="flex flex-col gap-2"'
        Write-Host "Fixed: Sticky Class removed (Regex)."
    } else {
        Write-Host "Warning: Sticky Class not found."
    }
}

if ($contentNorm.Contains($quickSummaryBlockNorm)) {
    $content = $content.Replace($quickSummaryBlock, "{{-- Mobile Quick Summary removed --}}")
    Write-Host "Fixed: Quick Summary removed."
} else {
    Write-Host "Warning: Quick Summary not found (exact match)."
}

[System.IO.File]::WriteAllText((Resolve-Path $path), $content, [System.Text.Encoding]::UTF8)
Write-Host "Done saving file."
