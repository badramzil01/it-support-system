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

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('support')) {
            return redirect()->route('support.dashboard');
        }

        if ($user->hasRole('employee')) {
            
            // ✅ redirect to chatbot
            return redirect('/chat');
        }

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