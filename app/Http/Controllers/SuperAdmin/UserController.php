<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::query();

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

        // Exclude Current User
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
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:superadmin,admin,operator',
            'jabatan' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

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

        return redirect()->route('superadmin.users.index')
            ->with('success', "User berhasil dibuat. Username Sementara: {$username}, Password Default: {$password}");
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\User $user)
    {
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
    public function update(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'role' => 'required|in:superadmin,admin,operator',
            'jabatan' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update([
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
        ]);

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

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
