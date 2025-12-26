<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $service
    ) {}

    /**
     * Display a listing of attendances.
     */
    public function index(): Response
    {
        $search = request('search');
        $recordedBy = request('recordedBy');
        $perPage = request('per_page', 15);

        // Get unique admins who have recorded attendances for filter dropdown
        $admins = Attendance::whereNotNull('snapshot_recorded_by_name')
            ->distinct()
            ->pluck('snapshot_recorded_by_name')
            ->sort()
            ->values()
            ->toArray();

        return Inertia::render('attendances/Index', [
            'attendances' => $this->service->getPaginated($perPage, [
                'search' => $search,
                'recorded_by' => $recordedBy,
            ]),
            'admins' => $admins,
            'filters' => [
                'search' => $search,
                'recordedBy' => $recordedBy,
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
}
