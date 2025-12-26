<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $data = [];

        // Common stats for all roles
        $data['members'] = [
            'total' => Member::count(),
            'new_this_month' => Member::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $data['memberships'] = [
            'active' => MemberMembership::where('status', 'active')->count(),
            'expiring_soon' => MemberMembership::where('status', 'active')
                ->where('expired_at', '<=', now()->addDays(7))
                ->where('expired_at', '>', now())
                ->count(),
        ];

        $data['attendances'] = [
            'today' => Attendance::whereDate('check_in_at', today())->count(),
            'currently_in' => Attendance::whereDate('check_in_at', today())
                ->whereNull('check_out_at')
                ->count(),
        ];

        // Revenue stats - only for super-admin and owner
        if ($user->hasAnyRole(['super-admin', 'owner'])) {
            $data['revenue'] = [
                'total' => MemberMembership::sum('snapshot_price'),
                'this_month' => MemberMembership::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('snapshot_price'),
            ];
        }

        // User stats - only for super-admin
        if ($user->hasRole('super-admin')) {
            $data['users'] = [
                'total' => User::count(),
            ];
        }

        return Inertia::render('dashboard/Index', [
            'stats' => $data,
        ]);
    }
}
