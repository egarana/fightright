<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     * Only super-admin can impersonate.
     */
    public function start(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        // Check if current user is super-admin
        if (!$currentUser->hasRole('super-admin')) {
            abort(403);
        }

        // Cannot impersonate yourself
        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Cannot impersonate yourself.');
        }

        // Cannot impersonate another super-admin
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Cannot impersonate another super-admin.');
        }

        // Store original user ID in session
        Session::put('impersonator_id', $currentUser->id);

        // Login as the target user
        Auth::login($user);

        return redirect()->route('dashboard.index')
            ->with('success', "Now impersonating {$user->name}");
    }

    /**
     * Stop impersonating and return to original user.
     */
    public function stop(): RedirectResponse
    {
        $impersonatorId = Session::get('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard.index');
        }

        // Find and login as original user
        $originalUser = User::find($impersonatorId);

        if ($originalUser) {
            // Clear impersonation session
            Session::forget('impersonator_id');

            // Login as original user
            Auth::login($originalUser);

            return redirect()->route('dashboard.index')
                ->with('success', 'Stopped impersonating. Welcome back!');
        }

        return redirect()->route('dashboard.index');
    }
}
