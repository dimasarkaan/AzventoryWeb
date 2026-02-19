<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ActivityLogger;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;

class UserController extends Controller
{
    use ActivityLogger;
    /**
     * Menampilkan daftar pengguna (User).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class);
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
        
        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        $this->authorize('create', \App\Models\User::class);
        return view('users.create');
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', \App\Models\User::class);
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
            'password_changed_at' => null, // Force password change on first login
        ]);

        $this->logActivity('User Dibuat', __('messages.log_user_created', ['name' => $user->name, 'role' => $user->role->label()]));

        return redirect()->route('users.index')
            ->with('success', __('messages.user_created', ['username' => $username, 'password' => $password]));
    }

    /**
     * Menampilkan detail pengguna.
     */
    public function show(\App\Models\User $user)
    {
        $this->authorize('view', $user);
        $user->load(['borrowings.sparepart']);
        
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(\App\Models\User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(UpdateUserRequest $request, \App\Models\User $user)
    {
        $this->authorize('update', $user);
        $user->update([
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
        ]);

        $this->logActivity('User Diupdate', __('messages.log_user_updated', ['name' => $user->name]));

        return redirect()->route('users.index')
            ->with('success', __('messages.user_updated', ['name' => $user->name]));
    }

    /**
     * Mereset password pengguna ke default.
     */
    public function resetPassword(\App\Models\User $user)
    {
        $defaultPassword = 'password123';
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPassword),
            'password_changed_at' => null, // Force password change on next login
        ]);

        $this->logActivity('Reset Password', __('messages.log_user_password_reset', ['name' => $user->name]));

        return back()->with('success', __('messages.user_password_reset', ['name' => $user->name, 'password' => $defaultPassword]));
    }

    /**
     * Menghapus pengguna (Soft Delete).
     */
    public function destroy(\App\Models\User $user)
    {
        $this->authorize('delete', $user);
         // Prevent deleting own account
         if (auth()->id() === $user->id) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        $this->logActivity('User Dihapus', __('messages.log_user_deleted_soft', ['name' => $user->name]));

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('messages.user_deleted'));
    }

    /**
     * Memulihkan pengguna yang dihapus (Restore).
     */
    public function restore($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);
        $user->restore();

        $this->logActivity('User Dipulihkan', __('messages.log_user_restored', ['name' => $user->name]));

        return redirect()->route('users.index', ['trash' => 'true'])
            ->with('success', __('messages.user_restored'));
    }

    /**
     * Menghapus pengguna secara permanen.
     */
    public function forceDelete($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);
        
        // Final check to prevent self-deletion even if force
        if (auth()->id() === $user->id) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        // Delete avatar if exists
        if ($user->avatar) {
             \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $this->logActivity('User Dihapus Permanen', __('messages.log_user_deleted_force', ['name' => $user->name]));
        
        $user->forceDelete();

        return redirect()->route('users.index', ['trash' => 'true'])
            ->with('success', __('messages.user_force_deleted'));
    }

    /**
     * Memulihkan banyak pengguna sekaligus.
     */
    public function bulkRestore(Request $request)
    {
        $this->authorize('restore', \App\Models\User::class);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);
        // ...
    }

    /**
     * Menghapus permanen banyak pengguna sekaligus.
     */
    public function bulkForceDelete(Request $request)
    {
         $this->authorize('forceDelete', \App\Models\User::class);
         $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->ids;
        $users = \App\Models\User::onlyTrashed()->whereIn('id', $ids)->get();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', __('messages.no_user_selected_delete'));
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

        $this->logActivity('Bulk Force Delete User', __('messages.log_bulk_user_deleted_force', ['count' => $count]));

        return redirect()->back()->with('success', __('messages.bulk_user_force_deleted', ['count' => $count]));
    }
}
