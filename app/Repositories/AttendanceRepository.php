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
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['memberMembership.member', 'memberMembership.membership'])
            ->orderBy('check_in_at', 'desc')
            ->paginate($perPage);
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
     * Delete attendance.
     */
    public function delete(Attendance $attendance): bool
    {
        return $attendance->delete();
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
