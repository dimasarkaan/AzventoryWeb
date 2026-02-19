$c = Get-Content 'resources\views\dashboard\superadmin.blade.php'
$c[55..75] | Out-File -Encoding UTF8 debug_part1.txt
$c[120..140] | Out-File -Encoding UTF8 debug_part2.txt
$c[255..290] | Out-File -Encoding UTF8 debug_part3.txt
Make-Item -Path "debug_done" -ItemType File -Force
