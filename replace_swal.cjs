const fs = require('fs');
const files = [
  'resources/views/dashboard/admin.blade.php',
  'resources/views/dashboard/superadmin.blade.php',
  'resources/views/inventory/borrow/show.blade.php',
  'resources/views/inventory/partials/alpine_script.blade.php',
  'resources/views/inventory/show.blade.php',
  'resources/views/profile/my_inventory.blade.php'
];

files.forEach(file => {
  let content = fs.readFileSync(file, 'utf8');
  let original = content;

  // Replace the simple Swal.fire calls that we just added with window.showAlert
  content = content.replace(/Swal\.fire\('Error', 'Gagal mengambil screenshot\. Gunakan opsi Cetak\/PDF\.', 'error'\);/g, "window.showAlert('Error', 'Gagal mengambil screenshot. Gunakan opsi Cetak/PDF.', 'error');");
  content = content.replace(/Swal\.fire\('Error', 'Gagal menghubungi server\.', 'error'\);/g, "window.showAlert('Error', 'Gagal menghubungi server.', 'error');");
  content = content.replace(/Swal\.fire\('Error', 'Tidak dapat mengakses kamera: ' \+ err\.message, 'error'\);/g, "window.showAlert('Error', 'Tidak dapat mengakses kamera: ' + err.message, 'error');");
  content = content.replace(/Swal\.fire\('Peringatan', 'Maksimal 5 foto', 'warning'\);/g, "window.showAlert('Peringatan', 'Maksimal 5 foto', 'warning');");
  content = content.replace(/Swal\.fire\('Peringatan', msg, 'warning'\);/g, "window.showAlert('Peringatan', msg, 'warning');");
  content = content.replace(/Swal\.fire\('Info', message, 'info'\);/g, "window.showAlert('Info', message, 'info');");
  content = content.replace(/Swal\.fire\('Error', data\.message \|\| 'Terjadi kesalahan sistem\.', 'error'\);/g, "window.showAlert('Error', data.message || 'Terjadi kesalahan sistem.', 'error');");

  // Some old ones from operator.blade.php maybe? Let's just fix operator.blade.php too
  // Actually let's just stick to the 6 files first.

  if (content !== original) {
    fs.writeFileSync(file, content, 'utf8');
    console.log('Updated ' + file);
  }
});
