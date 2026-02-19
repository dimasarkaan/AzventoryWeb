import re

path = r'resources\views\dashboard\superadmin.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

# Fix 1: Hapus Forecasting Checkbox
# Pattern: label block containing showForecast
# We use DOTALL to match across lines and non-greedy match *?
pattern_forecast = r'<label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">\s*<input type="checkbox" :checked="showForecast".*?</label>'
c_new, n1 = re.subn(pattern_forecast, '', c, flags=re.DOTALL)
print(f'Fix1 (Forecasting checkbox): {n1} replacements')
c = c_new

# Fix 2: Hapus sticky class
# Pattern: class="... sticky top-0 z-40 ... sm:pb-0"
# Target: class="flex flex-col gap-2"
pattern_sticky = r'class="flex flex-col gap-2 sticky top-0 z-40 bg-white/95 backdrop-blur-sm pt-1 pb-2 -mx-4 px-4 sm:relative sm:top-auto sm:z-auto sm:bg-transparent sm:backdrop-filter-none sm:mx-0 sm:px-0 sm:pt-0 sm:pb-0"'
replacement_sticky = 'class="flex flex-col gap-2"'

if pattern_sticky in c:
    c = c.replace(pattern_sticky, replacement_sticky)
    print('Fix2 (Sticky class): 1 replacement')
else:
    # Try Regex if exact string match fails (due to whitespace)
    print('Fix2 (Sticky class): exact match failed, trying regex...')
    pattern_sticky_re = r'class="flex flex-col gap-2 sticky top-0 z-40[^"]+"'
    c_new, n2 = re.subn(pattern_sticky_re, 'class="flex flex-col gap-2"', c)
    print(f'Fix2 (Sticky class Regex): {n2} replacements')
    c = c_new

with open(path, 'w', encoding='utf-8') as f:
    f.write(c)
print('Done. File saved.')
