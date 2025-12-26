<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if user has required permission from auth.can, return 404 if not.
 * This middleware checks against the shared Inertia permissions.
 */
class CheckPermissionOrNotFound
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(404);
        }

        // Check permission based on type
        $hasPermission = match ($permission) {
            'manage_users' => $user->hasRole('super-admin'),
            'manage_memberships' => $user->hasAnyRole(['super-admin', 'owner']),
            'edit_members' => $user->hasAnyRole(['super-admin', 'owner', 'manager']),
            'delete_members' => $user->hasAnyRole(['super-admin', 'owner']),
            'manage_member_memberships' => $user->hasAnyRole(['super-admin', 'owner', 'manager']),
            'delete_attendances' => $user->hasAnyRole(['super-admin', 'owner', 'manager']),
            'view_revenue' => $user->hasAnyRole(['super-admin', 'owner']),
            default => false,
        };

        if (!$hasPermission) {
            abort(404);
        }

        return $next($request);
    }
}
