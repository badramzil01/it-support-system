<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('permission:create_ticket')
     *        ->middleware('permission:create_ticket|update_ticket')
     *        ->middleware('permission:create_ticket,update_ticket')
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin always bypasses permission checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            // Support pipe-separated permissions
            $perms = array_map('trim', explode('|', $permission));
            foreach ($perms as $perm) {
                if ($user->hasPermissionTo($perm)) {
                    return $next($request);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        abort(403, 'You do not have the required permission.');
    }
}