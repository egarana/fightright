<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberMembershipRequest;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Services\MemberMembershipService;
use App\Services\MemberService;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MemberMembershipController extends Controller
{
    public function __construct(
        protected MemberMembershipService $service,
        protected MemberService $memberService,
        protected MembershipService $membershipService
    ) {}

    /**
     * Display a listing of member memberships.
     */
    public function index(): Response
    {
        return Inertia::render('member-memberships/Index', [
            'memberMemberships' => $this->service->getPaginated(),
        ]);
    }

    /**
     * Show the form for creating a new member membership.
     */
    public function create(): Response
    {
        return Inertia::render('member-memberships/Create', [
            'members' => $this->memberService->getAll(),
            'memberships' => $this->membershipService->getAllActive(),
        ]);
    }

    /**
     * Store a newly created member membership.
     */
    public function store(StoreMemberMembershipRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $member = $this->memberService->getByIdOrFail($validated['member_id']);
        $membership = $this->membershipService->getByIdOrFail($validated['membership_id']);

        $this->service->assignMembership(
            $member,
            $membership,
            $validated['started_at'] ?? null
        );

        return redirect()
            ->route('member-memberships.index')
            ->with('success', 'Membership assigned successfully.');
    }

    /**
     * Display the specified member membership.
     */
    public function show(MemberMembership $memberMembership): Response
    {
        $memberMembership = $this->service->getByIdOrFail($memberMembership->id);

        return Inertia::render('member-memberships/Show', [
            'memberMembership' => $memberMembership,
        ]);
    }

    /**
     * Cancel the specified member membership.
     */
    public function cancel(MemberMembership $memberMembership): RedirectResponse
    {
        $this->service->cancel($memberMembership);

        return redirect()
            ->route('member-memberships.index')
            ->with('success', 'Membership cancelled successfully.');
    }

    /**
     * Remove the specified member membership.
     */
    public function destroy(MemberMembership $memberMembership): RedirectResponse
    {
        $this->service->delete($memberMembership);

        return redirect()
            ->route('member-memberships.index')
            ->with('success', 'Member membership deleted successfully.');
    }
}
