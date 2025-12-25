<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipRequest;
use App\Http\Requests\UpdateMembershipRequest;
use App\Models\Membership;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MembershipController extends Controller
{
    public function __construct(
        protected MembershipService $service
    ) {}

    /**
     * Display a listing of memberships.
     */
    public function index(): Response
    {
        return Inertia::render('memberships/Index', [
            'memberships' => $this->service->getPaginated(),
        ]);
    }

    /**
     * Show the form for creating a new membership.
     */
    public function create(): Response
    {
        return Inertia::render('memberships/Create');
    }

    /**
     * Store a newly created membership.
     */
    public function store(StoreMembershipRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('memberships.index')
            ->with('success', 'Membership created successfully.');
    }

    /**
     * Show the form for editing the specified membership.
     */
    public function edit(Membership $membership): Response
    {
        return Inertia::render('memberships/Edit', [
            'membership' => $membership,
        ]);
    }

    /**
     * Update the specified membership.
     */
    public function update(UpdateMembershipRequest $request, Membership $membership): RedirectResponse
    {
        $this->service->update($membership, $request->validated());

        return redirect()
            ->route('memberships.index')
            ->with('success', 'Membership updated successfully.');
    }

    /**
     * Remove the specified membership.
     */
    public function destroy(Membership $membership): RedirectResponse
    {
        $this->service->delete($membership);

        return redirect()
            ->route('memberships.index')
            ->with('success', 'Membership deleted successfully.');
    }
}
