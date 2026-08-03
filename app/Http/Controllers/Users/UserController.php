<?php

// Controller khusus untuk mengelola data dan aktivitas akun Pengguna (User).
// Mulai dari pembuatan akun baru, edit profil, reset password, sampai penghapusan akun.
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ActivityLogger;

    // Menampilkan halaman daftar pengguna (User)
    public function index(Request $request)
    {
        // Memastikan pengguna memiliki hak akses untuk melihat daftar akun
        $this->authorize('viewAny', User::class);
        
        // Memulai query ke tabel users
        $query = User::query();

        // Menampilkan data yang telah dihapus (soft delete) jika parameter trash bernilai true
        if ($request->has('trash') && $request->trash == 'true') {
            $query->onlyTrashed();
        }

        // Menambahkan filter pencarian berdasarkan nama, email, atau username
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('username', 'like', '%'.$request->search.'%');
            });
        });

        // Menambahkan filter berdasarkan role jika dipilih role spesifik
        $query->when($request->role && $request->role !== 'Semua Role', function ($q) use ($request) {
            $q->where('role', $request->role);
        });

        // Menambahkan filter berdasarkan status jika dipilih status spesifik
        $query->when($request->status && $request->status !== 'Semua Status', function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        // Mengecualikan akun yang sedang login dari hasil pencarian agar tidak dapat memanipulasi akunnya sendiri
        $query->where('id', '!=', auth()->id());

        // Mengambil data terbaru dengan paginasi 10 item per halaman
        $users = $query->latest()->paginate(10)->withQueryString();

        // Mengembalikan tampilan halaman daftar pengguna dengan membawa variabel users
        return view('users.index', compact('users'));
    }

    // Menampilkan halaman form untuk membuat pengguna baru
    public function create()
    {
        // Mengecek hak akses pengguna untuk mengakses form penambahan data
        $this->authorize('create', User::class);

        // Menampilkan halaman form untuk membuat pengguna baru
        return view('users.create');
    }

    // Menyimpan data pengguna baru yang diinputkan ke database
    public function store(StoreUserRequest $request)
    {
        // Memastikan pengguna memiliki izin untuk menambahkan data pengguna baru
        $this->authorize('create', User::class);
        
        // Membuat username sementara dengan format: bagian depan email + angka acak
        $username = explode('@', $request->email)[0].rand(100, 999);
        
        // Memastikan username tersebut unik dan belum ada di database (termasuk di tong sampah)
        while (User::withTrashed()->where('username', $username)->exists()) {
            $username = explode('@', $request->email)[0].rand(100, 999);
        }

        // Menyiapkan password default untuk pengguna yang baru dibuat
        $password = 'password123'; 

        // Memasukkan data pengguna baru ke dalam database
        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
            'password_changed_at' => null, // Memaksa pengguna mengubah password saat pertama kali login
        ]);

        // Mencatat aktivitas penambahan data pengguna ke dalam log sistem
        $this->logActivity('User Dibuat', __('messages.log_user_created', ['name' => $user->name, 'role' => $user->role->label()]));

        // Mengarahkan kembali ke halaman daftar pengguna dengan membawa pesan berhasil beserta info loginnya
        return redirect()->route('users.index')
            ->with('success', __('messages.user_created', ['username' => $username, 'password' => $password]));
    }

    // Menampilkan detail informasi dari seorang pengguna
    public function show(User $user)
    {
        // Memastikan pengguna memiliki izin untuk melihat detail pengguna lain
        $this->authorize('view', $user);
        
        // Memuat relasi data peminjaman beserta detail barang yang dipinjam oleh user tersebut
        $user->load(['borrowings.sparepart']);

        // Mengembalikan tampilan halaman detail pengguna
        return view('users.show', compact('user'));
    }

    // Menampilkan form untuk mengedit data pengguna yang sudah ada
    public function edit(User $user)
    {
        // Mengecek hak akses pengguna untuk melihat form pengubahan data
        $this->authorize('update', $user);

        // Mencegah pengguna mengedit role/status akunnya sendiri via User Management (harus lewat Profil)
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat mengubah data sensitif akun Anda sendiri dari sini. Silakan gunakan menu Profil.');
        }

        // Menampilkan halaman form edit beserta data pengguna yang akan diubah
        return view('users.edit', compact('user'));
    }

    // Memperbarui atau menyimpan perubahan data pengguna ke database
    public function update(UpdateUserRequest $request, User $user)
    {
        // Mencegah pengguna mengubah akunnya sendiri yang bisa berakibat fatal (seperti tak sengaja merubah peran/status diri sendiri)
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak diperbolehkan mengubah peran atau status akun Anda sendiri.');
        }

        // Menyimpan daftar perubahan atribut data pengguna untuk dicatat ke dalam log nantinya
        $changes = [];
        foreach ($request->validated() as $key => $value) {
            if ($user->{$key} != $value) {
                $changes[$key] = [
                    'old' => $user->{$key},
                    'new' => $value,
                ];
            }
        }

        // Memperbarui data pengguna berdasarkan inputan yang telah tervalidasi
        $user->update($request->validated());

        // Menyusun deskripsi aktivitas log
        $logDescription = __('messages.log_user_updated', ['name' => $user->name]);
        if (isset($changes['role'])) {
            // Jika terdapat perubahan pada role, maka rincian perubahannya akan ditambahkan ke deskripsi log
            $newRole = \App\Enums\UserRole::tryFrom($changes['role']['new']) ?? $changes['role']['new'];
            $oldRoleLabel = $changes['role']['old'] instanceof \App\Enums\UserRole ? $changes['role']['old']->label() : $changes['role']['old'];
            $newRoleLabel = $newRole instanceof \App\Enums\UserRole ? $newRole->label() : $newRole;
            $logDescription .= ' (Perubahan Peran: '.$oldRoleLabel.' -> '.$newRoleLabel.')';
        }

        // Jika ada data yang diubah, catat aktivitas tersebut ke dalam log
        $this->logActivity('User Diupdate', $logDescription, $changes);

        // Mengarahkan kembali ke halaman daftar pengguna dengan membawa pesan berhasil
        return redirect()->route('users.index')
            ->with('success', __('messages.user_updated', ['name' => $user->name]));
    }

    // Mengembalikan (reset) password pengguna ke password default (password123)
    public function resetPassword(User $user)
    {
        // Mencegah pengguna mereset kata sandinya sendiri via admin panel
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat mereset kata sandi Anda sendiri dari sini. Gunakan menu Ganti Password.');
        }

        // Mendefinisikan password default untuk fitur reset password
        $defaultPassword = 'password123';
        
        // Memperbarui data password dengan nilai yang dienkripsi
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPassword),
            'password_changed_at' => null, // Memaksa agar pengguna harus mengganti password lagi pada login berikutnya
        ]);

        // Revoke all API tokens to force re-login on mobile devices for security
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // Menyimpan jejak aktivitas reset password
        $this->logActivity('Reset Password', __('messages.log_user_password_reset', ['name' => $user->name]));

        // Kembali ke halaman sebelumnya dan menampilkan notifikasi sukses
        return back()->with('success', __('messages.user_password_reset', ['name' => $user->name, 'password' => $defaultPassword]));
    }

    // Menghapus data pengguna secara sementara (Soft Delete) agar masuk ke tempat sampah
    public function destroy(User $user)
    {
        // Mengecek izin pengguna untuk melakukan soft delete data akun
        $this->authorize('delete', $user);
        
        // Validasi agar pengguna tidak bisa menghapus akun yang sedang digunakannya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        // Validasi tambahan agar pengguna yang memiliki status peminjaman aktif belum bisa dihapus
        if ($user->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus pengguna karena masih memiliki pinjaman barang aktif.');
        }

        // Menyimpan log aktivitas ke dalam riwayat
        $this->logActivity('User Dihapus', __('messages.log_user_deleted_soft', ['name' => $user->name]));

        // Menghapus data pengguna (soft delete, data masih ada di database tapi tidak terlihat)
        $user->delete();

        // Arahkan kembali ke halaman index
        return redirect()->route('users.index')
            ->with('success', __('messages.user_deleted'));
    }

    // Memulihkan pengguna yang sudah dihapus sementara agar kembali aktif
    public function restore($id)
    {
        // Mencari data pengguna yang sudah di-soft delete berdasarkan UUID
        $user = User::withTrashed()->where('uuid', $id)->firstOrFail();
        
        // Memastikan pengguna yang login memiliki izin untuk memulihkan data
        $this->authorize('restore', $user);
        
        // Memulihkan data akun agar aktif dan dapat digunakan kembali
        $user->restore();

        // Mencatat aktivitas pemulihan ke dalam log sistem
        $this->logActivity('User Dipulihkan', __('messages.log_user_restored', ['name' => $user->name]));

        // Mengarahkan kembali ke halaman trash dengan menampilkan pesan sukses
        return redirect()->route('users.index', ['trash' => 'true'])
            ->with('success', __('messages.user_restored'));
    }

    // Menghapus data pengguna secara permanen dari database
    public function forceDelete($id)
    {
        // Mengambil data pengguna dari tempat sampah (soft deleted data) berdasarkan UUID
        $user = User::withTrashed()->where('uuid', $id)->firstOrFail();
        
        // Memeriksa izin pengguna untuk melakukan penghapusan data secara permanen
        $this->authorize('forceDelete', $user);

        // Validasi ekstra agar tidak ada yang bisa menghapus akunnya sendiri secara permanen
        if (auth()->id() === $user->id) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        // Memastikan pengguna yang akan dihapus permanen tidak memiliki pinjaman barang yang belum dikembalikan
        if ($user->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus permanen pengguna karena masih memiliki pinjaman barang aktif.');
        }

        // Jika pengguna memiliki foto profil, hapus file fotonya dari server
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        // Menyimpan log bahwa data pengguna telah dihapus secara permanen
        $this->logActivity('User Dihapus Permanen', __('messages.log_user_deleted_force', ['name' => $user->name]));

        // Melakukan penghapusan data pengguna dari database sepenuhnya
        $user->forceDelete();

        // Mengarahkan kembali ke halaman tempat sampah dengan pesan berhasil
        return redirect()->route('users.index', ['trash' => 'true'])
            ->with('success', __('messages.user_force_deleted'));
    }

    // Memulihkan beberapa pengguna sekaligus dari tempat sampah (Bulk Action)
    public function bulkRestore(Request $request)
    {
        // Memastikan bahwa pengguna memiliki hak akses untuk memulihkan data massal
        $this->authorize('restore', User::class);
        
        // Memvalidasi request agar data 'ids' wajib ada dan harus berbentuk array
        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->ids;
        $count = 0;

        // Mengambil seluruh data pengguna yang ada di tempat sampah berdasarkan kumpulan ID yang diberikan
        $users = User::onlyTrashed()->whereIn('id', $ids)->get();

        $names = [];
        // Melakukan perulangan untuk memulihkan masing-masing akun pengguna
        foreach ($users as $user) {
            /** @var \App\Models\User $user */
            $user->restore();
            $names[] = $user->name;
            $count++; // Menghitung total data yang berhasil dipulihkan
        }

        $namesList = implode(', ', $names);

        // Mencatat aktivitas pemulihan massal ke dalam log sistem
        $this->logActivity('Bulk Restore User', __('messages.log_bulk_user_restored', ['count' => $count]), [
            'names' => ['old' => '-', 'new' => $namesList]
        ]);

        // Kembali ke halaman sebelumnya dengan pesan jumlah data yang berhasil dipulihkan
        return redirect()->back()->with('success', __('messages.bulk_user_restored', ['count' => $count]));
    }

    // Menghapus banyak pengguna secara permanen sekaligus (Bulk Action)
    public function bulkForceDelete(Request $request)
    {
        // Memeriksa hak akses untuk melakukan penghapusan data massal secara permanen
        $this->authorize('forceDelete', User::class);
        
        // Memastikan kumpulan ID pengguna wajib ada dan valid sebagai array
        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->ids;
        
        // Mengambil data dari keranjang sampah yang cocok dengan kumpulan ID
        $users = User::onlyTrashed()->whereIn('id', $ids)->get();

        // Jika tidak ada data yang ditemukan, kembalikan dengan pesan error
        if ($users->isEmpty()) {
            return redirect()->back()->with('error', __('messages.no_user_selected_delete'));
        }

        $count = 0;
        $skipped = 0;
        
        $names = [];
        // Memproses satu per satu data pengguna yang akan dihapus permanen
        foreach ($users as $user) {
            /** @var \App\Models\User $user */
            
            // Melewati proses hapus jika pengguna tersebut adalah diri sendiri
            if ($user->id === auth()->id()) {
                continue;
            }

            // Melewati proses hapus jika pengguna masih memiliki barang pinjaman yang belum selesai
            if ($user->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
                $skipped++;
                continue;
            }

            // Jika pengguna punya avatar, file avatarnya akan ikut dihapus dari server
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            
            $names[] = $user->name;
            // Menghapus data akun dari database selamanya
            $user->forceDelete();
            $count++;
        }

        $namesList = implode(', ', $names);

        // Mencatat jumlah pengguna yang berhasil dihapus ke dalam log
        $this->logActivity('Bulk Force Delete User', __('messages.log_bulk_user_deleted_force', ['count' => $count]), [
            'names' => ['old' => $namesList, 'new' => '-']
        ]);

        // Menyusun pesan keberhasilan beserta informasi jika ada akun yang gagal dihapus (dilewati)
        $message = __('messages.bulk_user_force_deleted', ['count' => $count]);
        if ($skipped > 0) {
            $message .= " ($skipped pengguna dilewati karena memiliki pinjaman aktif).";
        }

        // Kembali ke halaman sebelumnya dan menampilkan pesan yang telah disusun
        return redirect()->back()->with('success', $message);
    }
}
