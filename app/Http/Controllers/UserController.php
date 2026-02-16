<?php

namespace App\Http\Controllers;

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
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
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
            'password_changed_at' => null, // Force password change on first login
        ]);

        $this->logActivity('User Dibuat', __('messages.log_user_created', ['name' => $user->name, 'role' => $user->role->label()]));

        return redirect()->route('users.index')
            ->with('success', __('messages.user_created', ['username' => $username, 'password' => $password]));
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\User $user)
    {
        $user->load(['borrowings.sparepart']);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\User $user)
    {
        return view('users.edit', compact('user'));
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

        $this->logActivity('User Diupdate', __('messages.log_user_updated', ['name' => $user->name]));

        return redirect()->route('users.index')
            ->with('success', __('messages.user_updated', ['name' => $user->name]));
    }

    /**
     * Reset the user's password to default.
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
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\User $user)
    {
         // Prevent deleting own account
         if (auth()->id() === $user->id) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        $this->logActivity('User Dihapus', __('messages.log_user_deleted_soft', ['name' => $user->name]));

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function restore($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $user->restore();

        $this->logActivity('User Dipulihkan', __('messages.log_user_restored', ['name' => $user->name]));

        return redirect()->route('users.index', ['trash' => 'true'])
            ->with('success', __('messages.user_restored'));
    }

    public function forceDelete($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        
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
             return redirect()->back()->with('error', __('messages.no_user_selected_restore'));
        }

        \App\Models\User::onlyTrashed()->whereIn('id', $ids)->restore();

        $this->logActivity('Bulk Restore User', __('messages.log_bulk_user_restored', ['count' => $count]));

        return redirect()->back()->with('success', __('messages.bulk_user_restored', ['count' => $count]));
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
