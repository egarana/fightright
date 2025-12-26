<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if user has required role(s), return 404 if not (for security).
 * This hides the existence of protected resources from unauthorized users.
 */
class CheckRoleOrNotFound
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // If no user or user doesn't have any of the required roles, return 404
        if (!$user || !$user->hasAnyRole($roles)) {
            abort(404);
        }

        return $next($request);
    }
}
