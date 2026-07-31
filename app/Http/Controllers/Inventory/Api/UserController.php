<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group User Management
 *
 * API endpoints khusus Superadmin untuk CRUD manajemen pengguna dan hak akses.
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'jabatan' => 'nullable|string',
            'status' => 'required|string',
        ]);

        // Generate username otomatis (seperti di Web)
        $username = explode('@', $validated['email'])[0].rand(100, 999);
        while (User::where('username', $username)->exists()) {
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
     */
    public function show($id)
    {
        $user = User::withTrashed()->with(['borrowings.sparepart'])->where('uuid', $id)->first();

        if (! $user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    /**
     * Update user via API.
     */
    public function update(Request $request, $id)
    {
        $user = User::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|in:superadmin,admin,operator',
            'status' => 'sometimes|string|in:aktif,nonaktif',
            'jabatan' => 'nullable|string',
        ]);

        if (auth()->id() === $user->id) {
            if ($request->has('role') && $request->role !== $user->role->value) {
                return response()->json(['message' => 'Anda tidak dapat mengubah role akun Anda sendiri'], 400);
            }
            if ($request->has('status') && $request->status !== $user->status) {
                return response()->json(['message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri'], 400);
            }
        }

        $user->update($validated);

        $this->logActivity('User Diupdate (API)', "Data user '{$user->name}' diperbarui via API.");

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui',
            'data' => $user,
        ]);
    }

    /**
     * Reset Password via API.
     */
    public function resetPassword($id)
    {
        $user = User::where('uuid', $id)->firstOrFail();
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
     */
    public function destroy($id)
    {
        $user = User::where('uuid', $id)->firstOrFail();

        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri'], 400);
        }

        if ($user->borrowings()->where('status', 'borrowed')->exists()) {
            return response()->json(['message' => 'User masih memiliki pinjaman aktif'], 400);
        }

        $user->delete();

        $this->logActivity('User Dihapus (API)', "User '{$user->name}' dihapus (soft-delete) via API.");

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dihapus',
        ]);
    }
}
