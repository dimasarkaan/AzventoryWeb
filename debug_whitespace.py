path = r'resources\views\dashboard\superadmin.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

print('--- DEBUG FORECAST ---')
idx = c.find('showForecast')
if idx != -1:
    snippet = c[max(0, idx-100):min(len(c), idx+100)]
    print(repr(snippet))
else:
    print('showForecast NOT FOUND')

print('\n--- DEBUG STICKY ---')
idx = c.find('sticky top-0')
if idx != -1:
    snippet = c[max(0, idx-100):min(len(c), idx+100)]
    print(repr(snippet))
else:
    print('sticky top-0 NOT FOUND')
