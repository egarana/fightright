<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\MemberMembership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository
{
    public function __construct(
        protected Attendance $model
    ) {}

    /**
     * Get all attendances.
     */
    public function all(): Collection
    {
        return $this->model
            ->with(['memberMembership.member', 'memberMembership.membership'])
            ->orderBy('check_in_at', 'desc')
            ->get();
    }

    /**
     * Get paginated attendances.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['memberMembership.member', 'memberMembership.membership'])
            ->orderBy('check_in_at', 'desc');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('snapshot_member_name', 'like', "%{$search}%")
                    ->orWhere('snapshot_membership_name', 'like', "%{$search}%");
            });
        }

        // Filter by recorded_by admin name(s) - supports comma-separated values
        if (!empty($filters['recorded_by'])) {
            $recordedByNames = array_map('trim', explode(',', $filters['recorded_by']));
            $query->whereIn('snapshot_recorded_by_name', $recordedByNames);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find attendance by ID.
     */
    public function find(int $id): ?Attendance
    {
        return $this->model
            ->with(['memberMembership.member', 'memberMembership.membership'])
            ->find($id);
    }

    /**
     * Find attendance by ID or fail.
     */
    public function findOrFail(int $id): Attendance
    {
        return $this->model
            ->with(['memberMembership.member', 'memberMembership.membership'])
            ->findOrFail($id);
    }

    /**
     * Get attendances for a member membership.
     */
    public function getByMemberMembership(MemberMembership $memberMembership): Collection
    {
        return $this->model
            ->where('member_membership_id', $memberMembership->id)
            ->orderBy('check_in_at', 'desc')
            ->get();
    }

    /**
     * Create new attendance.
     */
    public function create(array $data): Attendance
    {
        return $this->model->create($data);
    }

    /**
     * Update attendance (e.g., for check-out).
     */
    public function update(Attendance $attendance, array $data): Attendance
    {
        $attendance->update($data);

        return $attendance->fresh();
    }



    /**
     * Get today's attendances.
     */
    public function getToday(): Collection
    {
        return $this->model
            ->whereDate('check_in_at', today())
            ->with(['memberMembership.member', 'memberMembership.membership'])
            ->orderBy('check_in_at', 'desc')
            ->get();
    }

    /**
     * Find currently checked-in attendance for a member membership.
     */
    public function findCurrentlyCheckedIn(MemberMembership $memberMembership): ?Attendance
    {
        return $this->model
            ->where('member_membership_id', $memberMembership->id)
            ->whereNull('check_out_at')
            ->first();
    }
}
