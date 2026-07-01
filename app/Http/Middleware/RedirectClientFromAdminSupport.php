<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectClientFromAdminSupport
{
    /**
     * Redirect clients/employees away from admin/support pages.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // If user has only 'client' role (or no admin/support role), redirect to /chat
        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('support') &&
            !$user->hasRole('support_n1') &&
            !$user->hasRole('support_n2') &&
            !$user->hasRole('support_n3')
        ) {
            return redirect('/chat');
        }

        return $next($request);
    }
}