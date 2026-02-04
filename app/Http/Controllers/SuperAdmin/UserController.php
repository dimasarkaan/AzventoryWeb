<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ActivityLogger;

class UserController extends Controller
{
    use ActivityLogger;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::query();

        // Handle Trash View
        if ($request->has('trash') && $request->trash == 'true') {
            $query->onlyTrashed();
        }

        // Search Scope
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function($sub) use ($request) {
                $sub->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        });

        // Filter Role
        $query->when($request->role && $request->role !== 'Semua Role', function ($q) use ($request) {
            $q->where('role', $request->role);
        });

        // Filter Status
        $query->when($request->status && $request->status !== 'Semua Status', function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        // Exclude Current User (even in trash, though unlikely to be there)
        $query->where('id', '!=', auth()->id());

        $users = $query->latest()->paginate(10)->withQueryString();
        
        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\SuperAdmin\User\StoreUserRequest $request)
    {
        // Auto-generate temporary username based on email
        $username = explode('@', $request->email)[0] . rand(100, 999);
        // Ensure uniqueness (simple check, collision rare for low volume)
        while(\App\Models\User::where('username', $username)->exists()){
            $username = explode('@', $request->email)[0] . rand(100, 999);
        }

        $password = 'password123'; // Default password

        $user = \App\Models\User::create([
            'name' => $username, // Default name
            'username' => $username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
        ]);

        $this->logActivity('User Dibuat', "Menambahkan user baru: {$user->name} ({$user->role->label()})");

        return redirect()->route('superadmin.users.index')
            ->with('success', "User berhasil dibuat. Username Sementara: {$username}, Password Default: {$password}");
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\User $user)
    {
        $user->load(['borrowings.sparepart']);
        
        return view('superadmin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\User $user)
    {
        return view('superadmin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\SuperAdmin\User\UpdateUserRequest $request, \App\Models\User $user)
    {
        $user->update([
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
        ]);

        $this->logActivity('User Diupdate', "Mengupdate data user: {$user->name}");

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Reset the user's password to default.
     */
    public function resetPassword(\App\Models\User $user)
    {
        $defaultPassword = 'password123';
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPassword),
        ]);

        $this->logActivity('Reset Password', "Mereset password user: {$user->name} ke default.");

        return back()->with('success', "Password untuk {$user->name} telah direset menjadi: {$defaultPassword}");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\User $user)
    {
         // Prevent deleting own account
         if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->logActivity('User Dihapus', "Menghapus user: {$user->name}");

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function restore($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $user->restore();

        $this->logActivity('User Dipulihkan', "Memulihkan user: {$user->name}");

        return redirect()->route('superadmin.users.index', ['trash' => 'true'])
            ->with('success', 'User berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        
        // Final check to prevent self-deletion even if force
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Delete avatar if exists
        if ($user->avatar) {
             \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $this->logActivity('User Dihapus Permanen', "Menghapus permanen user: {$user->name}");
        
        $user->forceDelete();

        return redirect()->route('superadmin.users.index', ['trash' => 'true'])
            ->with('success', 'User berhasil dihapus permanen.');
    }

    /**
     * Bulk restore soft-deleted users.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->ids;
        $count = \App\Models\User::onlyTrashed()->whereIn('id', $ids)->count();
        
        if ($count === 0) {
             return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk dipulihkan.');
        }

        \App\Models\User::onlyTrashed()->whereIn('id', $ids)->restore();

        $this->logActivity('Bulk Restore User', "$count user berhasil dipulihkan.");

        return redirect()->back()->with('success', "$count user berhasil dipulihkan.");
    }

    /**
     * Bulk permanently delete users.
     */
    public function bulkForceDelete(Request $request)
    {
         $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->ids;
        $users = \App\Models\User::onlyTrashed()->whereIn('id', $ids)->get();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk dihapus.');
        }

        $count = 0;
        foreach ($users as $user) {
            // Prevent self-deletion just in case
            if ($user->id === auth()->id()) continue;

             if ($user->avatar) {
                 \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $user->forceDelete();
            $count++;
        }

        $this->logActivity('Bulk Force Delete User', "$count user dihapus permanen.");

        return redirect()->back()->with('success', "$count user berhasil dihapus permanen.");
    }
}
