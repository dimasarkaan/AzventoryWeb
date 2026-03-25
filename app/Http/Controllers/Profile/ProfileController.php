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

class ProfileController extends Controller
{
    use ActivityLogger;

    protected $imageOptimizer;

    public function __construct(ImageOptimizationService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    /**
     * Menampilkan form edit profil user.
     */
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

    /**
     * Memperbarui informasi profil user.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

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

        $request->user()->save();

        $this->logActivity('Profil Diupdate', 'User mengupdate profil mereka.');

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

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

    /**
     * Memperbarui pengaturan user (JSON settings).
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $user = $request->user();
        $user->settings = array_merge($user->settings ?? [], $request->input('settings'));
        $user->save();

        return response()->json(['status' => 'success', 'settings' => $user->settings]);
    }

    /**
     * Menghapus foto profil (avatar) user.
     */
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

    /**
     * Menampilkan riwayat peminjaman user.
     */
    public function myInventory(Request $request): View
    {
        $user = $request->user();
        $activeBorrowings = $user->borrowings()->whereNull('returned_at')->with('sparepart')->latest()->get();
        $historyBorrowings = $user->borrowings()->whereNotNull('returned_at')->with(['sparepart', 'returns'])->latest('returned_at')->get();

        return view('profile.my_inventory', compact('user', 'activeBorrowings', 'historyBorrowings'));
    }
}
