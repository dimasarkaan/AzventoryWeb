<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Manajemen Pengguna
 *
 * Bagian ini berisi API untuk mengelola akun staf atau karyawan yang bisa login ke aplikasi Azventory. Anda bisa menambahkan pengguna baru, mengubah data mereka, mengatur ulang kata sandi, serta menentukan peran masing-masing pengguna (Superadmin, Admin, Operator, dll).
 */
class UserController extends Controller
{
    use ActivityLogger;

    /**
     * Memastikan hanya Superadmin yang bisa mengakses controller ini.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->role !== \App\Enums\UserRole::SUPERADMIN) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya Superadmin yang diizinkan mengakses manajemen user.',
                ], 403);
            }

            return $next($request);
        });
    }

    /**
     * Mendapatkan daftar semua user.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('trash') && $request->trash == 'true') {
            $query->onlyTrashed();
        }

        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('username', 'like', '%'.$request->search.'%');
            });
        });

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate($request->input('per_page', 20))->withQueryString();

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    /**
     * Membuat user baru via API.
     */
    public function store(\App\Http\Requests\Users\StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        // Generate username otomatis (seperti di Web)
        $username = explode('@', $validated['email'])[0].rand(100, 999);
        while (User::withTrashed()->where('username', $username)->exists()) {
            $username = explode('@', $validated['email'])[0].rand(100, 999);
        }

        $password = 'password123';

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => $validated['role'],
            'jabatan' => $validated['jabatan'] ?? null,
            'status' => $validated['status'],
            'password_changed_at' => null,
        ]);

        $this->logActivity('User Dibuat (API)', "User baru '{$user->name}' dibuat via API.");

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dibuat',
            'data' => [
                'user' => $user,
                'temporary_username' => $username,
                'temporary_password' => $password,
            ],
        ], 201);
    }

    /**
     * Detail user.
     *
     * @urlParam user string required UUID dari pengguna. Example: 4d2f8e...
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['borrowings.sparepart']);

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    /**
     * Update user via API.
     *
     * @urlParam user string required UUID dari pengguna. Example: 4d2f8e...
     */
    public function update(\App\Http\Requests\Users\UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Anda tidak dapat mengubah data sensitif akun Anda sendiri dari sini. Silakan gunakan menu Profil.'], 400);
        }

        $user->update($request->validated());

        $this->logActivity('User Diupdate (API)', "Data user '{$user->name}' diperbarui via API.");

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui',
            'data' => $user,
        ]);
    }

    /**
     * Reset Password via API.
     *
     * @urlParam id string required UUID dari pengguna. Example: 4d2f8e...
     */
    public function resetPassword(User $user)
    {
        $this->authorize('update', $user);

        $password = 'password123';

        $user->update([
            'password' => Hash::make($password),
            'password_changed_at' => null,
        ]);

        // Revoke all API tokens to force re-login on mobile devices for security
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        $this->logActivity('Reset Password (API)', "Password user '{$user->name}' direset via API.");

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset ke default',
            'temporary_password' => $password,
        ]);
    }

    /**
     * Hapus user via API.
     *
     * @urlParam user string required UUID dari pengguna. Example: 4d2f8e...
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun Anda sendiri'], 400);
        }

        if ($user->borrowings()->where('status', 'borrowed')->exists()) {
            return response()->json(['message' => 'Sistem menolak penghapusan. Pengguna ini masih memiliki pinjaman barang yang belum dikembalikan.'], 400);
        }

        $user->delete();

        $this->logActivity('User Dihapus (API)', "User '{$user->name}' dihapus (soft-delete) via API.");

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil dihapus.',
        ]);
    }
}
