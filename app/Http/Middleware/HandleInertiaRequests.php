<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user,
                'roles' => $user?->getRoleNames() ?? [],
                'can' => [
                    // Users - super-admin only
                    'manage_users' => $user?->hasRole('super-admin') ?? false,

                    // Memberships - super-admin & owner can manage (create/edit/delete)
                    'manage_memberships' => $user?->hasAnyRole(['super-admin', 'owner']) ?? false,

                    // Members - super-admin, owner, manager can edit
                    'edit_members' => $user?->hasAnyRole(['super-admin', 'owner', 'manager']) ?? false,
                    // Members - super-admin & owner can delete
                    'delete_members' => $user?->hasAnyRole(['super-admin', 'owner']) ?? false,

                    // Member Memberships - super-admin, owner, manager can delete/cancel
                    'manage_member_memberships' => $user?->hasAnyRole(['super-admin', 'owner', 'manager']) ?? false,

                    // Attendances - super-admin, owner, manager can delete
                    'delete_attendances' => $user?->hasAnyRole(['super-admin', 'owner', 'manager']) ?? false,

                    // Revenue/financial data - super-admin & owner only
                    'view_revenue' => $user?->hasAnyRole(['super-admin', 'owner']) ?? false,
                ],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
