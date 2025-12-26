<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Services\AttendanceService;
use App\Services\MemberCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicMemberController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Show the public member profile.
     * 
     * @param string $hash The obfuscated member ID from URL
     */
    public function show(string $hash): Response
    {
        $member = MemberCodeService::findByHash($hash);

        if (!$member) {
            abort(404);
        }

        // Get all memberships (including expired) with attendances for slot tracking
        $memberships = $member->memberMemberships()
            ->with(['membership', 'attendances' => fn($q) => $q->orderBy('created_at', 'asc')])
            ->get()
            ->map(function (MemberMembership $mm) {
                return array_merge($mm->toArray(), [
                    'remaining_qty' => $mm->remaining_qty,
                    'is_expired' => $mm->isExpired(),
                    'can_check_in' => $mm->canCheckIn(),
                    'used_count' => $mm->attendances->count(),
                ]);
            });

        return Inertia::render('public/member/Show', [
            'member' => $member,
            'memberships' => $memberships,
            'isAdmin' => auth()->check(),
        ]);
    }

    /**
     * Log a visit for a member's membership (admin only).
     * 
     * @param string $hash The obfuscated member ID from URL
     */
    public function checkIn(Request $request, string $hash): RedirectResponse
    {
        $member = MemberCodeService::findByHash($hash);

        if (!$member) {
            abort(404);
        }

        $validated = $request->validate([
            'member_membership_id' => 'required|integer',
        ]);

        // Find the membership and verify ownership
        $membership = MemberMembership::where('id', $validated['member_membership_id'])
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Validate can check-in
        if (!$membership->canCheckIn()) {
            return back()->with('error', 'Cannot check-in. Membership expired or quota exhausted.');
        }

        // Log the attendance with the authenticated user as recorder
        $this->attendanceService->checkIn($membership, auth()->user());

        return back()->with('success', 'Visit logged successfully.');
    }
}
