<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Traits\ActivityLogger;

use App\Services\ImageOptimizationService;

class ProfileController extends Controller
{
    use ActivityLogger;

    protected $imageOptimizer;

    public function __construct(ImageOptimizationService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Check if username is being changed (one-time only)
        if ($request->user()->isDirty('username')) {
            $request->user()->is_username_changed = true;
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
             // Delete old avatar if exists
             if ($request->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($request->user()->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->avatar);
            }
            
            $path = $this->imageOptimizer->optimizeAndSave($request->file('avatar'), 'avatars');
            $request->user()->avatar = $path;
        }

        $request->user()->save();

        $this->logActivity('Profil Diupdate', "User mengupdate profil mereka.");

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $this->logActivity('Akun Dihapus', "User menghapus akun mereka sendiri.");

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
