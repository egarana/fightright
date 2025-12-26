<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberMembershipRequest;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Services\MemberMembershipService;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberMembershipController extends Controller
{
    public function __construct(
        protected MemberMembershipService $service,
        protected MembershipService $membershipService
    ) {}

    /**
     * Display a listing of member's memberships.
     */
    public function index(Request $request, Member $member): Response
    {
        $perPage = $request->input('per_page', 15);
        $sort = $request->input('sort');
        $search = $request->input('search');

        $query = MemberMembership::query()
            ->where('member_id', $member->id)
            ->with('membership');

        // Handle search
        if ($search) {
            $query->where('snapshot_membership_name', 'like', "%{$search}%");
        }

        // Handle sort
        if ($sort) {
            $direction = 'asc';
            $field = $sort;

            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $field = substr($sort, 1);
            }

            $allowedSorts = ['snapshot_membership_name', 'started_at', 'expired_at', 'status', 'created_at'];
            if (in_array($field, $allowedSorts)) {
                $query->orderBy($field, $direction);
            } else {
                $query->orderByDesc('created_at');
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $memberMemberships = $query->paginate($perPage);

        return Inertia::render('members/memberships/Index', [
            'member' => $member,
            'memberMemberships' => $memberMemberships,
            'memberships' => $this->membershipService->getAllActive(),
        ]);
    }

    /**
     * Show the form for creating a new membership assignment.
     */
    public function create(Member $member): Response
    {
        $memberships = $this->membershipService->getAllActive();

        return Inertia::render('members/memberships/Create', [
            'member' => $member,
            'memberships' => $memberships,
        ]);
    }

    /**
     * Store a newly created membership assignment.
     */
    public function store(StoreMemberMembershipRequest $request, Member $member): RedirectResponse
    {
        $validated = $request->validated();
        $membership = $this->membershipService->getByIdOrFail($validated['membership_id']);

        $this->service->assignMembership(
            $member,
            $membership,
            $validated['started_at'] ?? null
        );

        return redirect()
            ->route('members.memberships.index', $member)
            ->with('success', 'Membership assigned successfully.');
    }

    /**
     * Remove the specified membership assignment.
     */
    public function destroy(Member $member, MemberMembership $memberMembership): RedirectResponse
    {
        $this->service->delete($memberMembership);

        return redirect()
            ->route('members.memberships.index', $member)
            ->with('success', 'Membership removed successfully.');
    }
}
