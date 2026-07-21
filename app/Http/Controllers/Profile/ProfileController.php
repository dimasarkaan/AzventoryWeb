<?php

namespace App\Http\Controllers\Profile;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageOptimizationService;
use App\Traits\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// Controller yang mengurus halaman Profil Pengguna (My Profile).
// Menangani fitur ganti nama, ubah password, upload/hapus avatar (foto profil), serta melihat riwayat peminjaman barang sendiri.
class ProfileController extends Controller
{
    use ActivityLogger;

    protected $imageOptimizer;

    public function __construct(ImageOptimizationService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    // Menampilkan halaman form untuk mengubah data profil pengguna
    public function edit(Request $request): View
    {
        $user = $request->user();

        $totalBorrowed = $user->borrowings()->count();
        $activeBorrows = $user->borrowings()->whereNull('returned_at')->count();

        return view('profile.edit', [
            'user' => $user,
            'totalBorrowed' => $totalBorrowed,
            'activeBorrows' => $activeBorrows,
        ]);
    }

    // Memproses form penyimpanan saat pengguna mengubah Nama, Email, Username, atau mengunggah Foto Profil
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Opsi B: Lockdown identitas Operator
        if ($request->user()->role === \App\Enums\UserRole::OPERATOR) {
            unset($validatedData['name']);
            unset($validatedData['email']);
        }

        $request->user()->fill($validatedData);

        // Cek jika username sedang diubah (hanya sekali)
        if ($request->user()->isDirty('username')) {
            $request->user()->is_username_changed = true;
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($request->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($request->user()->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->avatar);
            }

            $path = $this->imageOptimizer->optimizeAndSave($request->file('avatar'), 'avatars');
            $request->user()->avatar = $path;
        }

        $user = $request->user();
        $changes = [];
        foreach ($request->validated() as $key => $value) {
            // Hanya mencatat perubahan pada field teks/angka (scalar)
            // Hindari mencatat object File/Image karena masalah serialisasi JSON
            if ($user->isDirty($key) && is_scalar($value)) {
                $changes[$key] = ['old' => $user->getOriginal($key), 'new' => $value];
            }
        }

        $user->save();

        $this->logActivity('Profil Diupdate', 'User mengupdate profil mereka.', $changes);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Menghapus akun pengguna dari database (Self-Deletion)
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Opsi A: Mencegah selain Superadmin menghapus akunnya sendiri (Anti-sabotage)
        if ($user->role !== UserRole::SUPERADMIN) {
            return back()->with('error', 'Penghapusan akun dinonaktifkan oleh kebijakan perusahaan. Hubungi Administrator jika ingin menonaktifkan akun.');
        }

        // Mencegah Superadmin terakhir menghapus dirinya sendiri
        if ($user->role === UserRole::SUPERADMIN) {
            $superAdminCount = User::where('role', UserRole::SUPERADMIN->value)->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Anda tidak dapat menghapus akun karena Anda adalah satu-satunya Superadmin yang tersisa di sistem.');
            }
        }

        if ($user->borrowings()->where('status', 'borrowed')->exists()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun karena masih memiliki pinjaman barang yang belum dikembalikan.');
        }

        $this->logActivity('Akun Dihapus', 'User menghapus akun mereka sendiri.');

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // Menyimpan preferensi/pengaturan tambahan user (seperti mode tema gelap/terang) ke dalam kolom setting berformat JSON
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $user = $request->user();
        $user->settings = array_merge($user->settings ?? [], $request->input('settings'));
        $user->save();

        $this->logActivity('Update Pengaturan', 'User memperbarui pengaturan profil/tampilan mereka.', $request->input('settings'));

        return response()->json(['status' => 'success', 'settings' => $user->settings]);
    }

    // Menghapus foto profil (Avatar) yang sedang dipakai agar kembali menjadi kosong/default
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = null;
            $user->save();

            $this->logActivity('Avatar Dihapus', 'User menghapus foto profil mereka.');

            return back()->with('status', 'avatar-deleted');
        }

        return back();
    }

    // Menampilkan halaman "Barang Saya", berisi daftar seluruh barang yang SEDANG maupun PERNAH dipinjam oleh user ini
    public function myInventory(Request $request): View
    {
        $user = $request->user();
        $activeBorrowings = $user->borrowings()->whereNull('returned_at')->with('sparepart')->latest()->get();
        $historyBorrowings = $user->borrowings()->whereNotNull('returned_at')->with(['sparepart', 'returns'])->latest('returned_at')->get();

        return view('profile.my_inventory', compact('user', 'activeBorrowings', 'historyBorrowings'));
    }
}
