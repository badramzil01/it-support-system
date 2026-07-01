<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Login page
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Login action
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ✅ authenticate user
        $request->authenticate();

        // ✅ regenerate session
        $request->session()->regenerate();

        $user = Auth::user();

        // Track last login
        $user->update(['last_login_at' => now()]);

        // Admin → Admin Dashboard
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Support (N1/N2/N3) → Support Dashboard
        if (
            $user->hasRole('support') ||
            $user->hasRole('support_n1') ||
            $user->hasRole('support_n2') ||
            $user->hasRole('support_n3')
        ) {
            return redirect()->route('support.dashboard');
        }

        // All other users (client, employee) → Client Dashboard
        return redirect()->route('client.dashboard');
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ logout
        Auth::guard('web')->logout();

        // ✅ invalidate session
        $request->session()->invalidate();

        // ✅ regenerate token
        $request->session()->regenerateToken();

        // ✅ back to login
        return redirect('/login');
    }
}