import os

path = r'resources\views\dashboard\superadmin.blade.php'
print(f'CWD: {os.getcwd()}')
print(f'File exists: {os.path.exists(path)}')

with open(path, 'rb') as f:
    c = f.read()

print(f'File size: {len(c)} bytes')

def check_bytes(desc, b_str):
    if b_str in c:
        print(f'[FOUND] {desc}')
    else:
        print(f'[MISSING] {desc}')

check_bytes('Forecasting checkbox (showForecast)', b'showForecast')
check_bytes('Sticky class', b'sticky top-0')
check_bytes('Quick Summary (md:hidden)', b'md:hidden')
check_bytes('Quick Summary Content ($totalStock)', b'$totalStock')

print('First 100 bytes:')
print(c[:100])
