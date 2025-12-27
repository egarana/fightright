<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function __construct(
        protected MemberService $service
    ) {}

    /**
     * Display a listing of members.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['membership_id']);

        return Inertia::render('members/Index', [
            'members' => $this->service->getPaginated(
                perPage: $request->input('per_page', 15),
                sort: $request->input('sort'),
                search: $request->input('search'),
                fields: $request->input('fields'),
                filters: $filters,
            ),
            'membershipTypes' => \App\Models\Membership::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new member.
     */
    public function create(): Response
    {
        return Inertia::render('members/Create');
    }

    /**
     * Store a newly created member.
     */
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('members.index', ['sort' => '-created_at'])
            ->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member): Response
    {
        $member = $this->service->getWithMemberships($member->id);

        return Inertia::render('members/Show', [
            'member' => $member,
        ]);
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member): Response
    {
        return Inertia::render('members/Edit', [
            'member' => $member,
        ]);
    }

    /**
     * Update the specified member.
     */
    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $this->service->update($member, $request->validated());

        return redirect()
            ->route('members.index', ['sort' => '-updated_at'])
            ->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member): RedirectResponse
    {
        $this->service->delete($member);

        return redirect()
            ->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }
}
