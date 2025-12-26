<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyCheckedInException;
use App\Exceptions\MembershipExpiredException;
use App\Exceptions\NotCheckedInException;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use App\Services\MemberMembershipService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $service,
        protected MemberMembershipService $memberMembershipService
    ) {}

    /**
     * Display a listing of attendances.
     */
    public function index(): Response
    {
        $search = request('search');

        return Inertia::render('attendances/Index', [
            'attendances' => $this->service->getPaginated(15, [
                'search' => $search,
            ]),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Display today's attendances.
     */
    public function today(): Response
    {
        return Inertia::render('attendances/Today', [
            'attendances' => $this->service->getToday(),
        ]);
    }

    /**
     * Show the check-in form.
     */
    /**
     * Show the check-in form.
     */
    public function showCheckIn(): Response
    {
        $search = request('search');

        return Inertia::render('attendances/CheckIn', [
            'memberMemberships' => $this->memberMembershipService->getPaginated(15, [
                'status' => 'active',
                'search' => $search,
            ]),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Process check-in.
     */
    public function checkIn(CheckInRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $memberMembership = $this->memberMembershipService->getByIdOrFail(
            $validated['member_membership_id']
        );

        try {
            $this->service->checkIn($memberMembership, $validated['notes'] ?? null);

            return redirect()
                ->route('attendances.today')
                ->with('success', 'Check-in successful.');
        } catch (MembershipExpiredException $e) {
            return redirect()
                ->back()
                ->withErrors(['member_membership_id' => $e->getMessage()]);
        } catch (AlreadyCheckedInException $e) {
            return redirect()
                ->back()
                ->withErrors(['member_membership_id' => $e->getMessage()]);
        }
    }
}
