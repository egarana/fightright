<?php

namespace App\Services;

use App\Exceptions\MembershipExpiredException;
use App\Models\Attendance;
use App\Models\MemberMembership;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService
{
    public function __construct(
        protected AttendanceRepository $repository
    ) {}

    /**
     * Get all attendances.
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get paginated attendances.
     */
    /**
     * Get paginated attendances.
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Get attendance by ID.
     */
    public function getById(int $id): ?Attendance
    {
        return $this->repository->find($id);
    }

    /**
     * Get attendance by ID or fail.
     */
    public function getByIdOrFail(int $id): Attendance
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get today's attendances.
     */
    public function getToday(): Collection
    {
        return $this->repository->getToday();
    }

    /**
     * Get attendances for a member membership.
     */
    public function getByMemberMembership(MemberMembership $memberMembership): Collection
    {
        return $this->repository->getByMemberMembership($memberMembership);
    }

    /**
     * Check-in member with validation.
     *
     * @param MemberMembership $memberMembership The membership to check-in
     * @param User|null $recordedBy The admin/staff who is recording this attendance
     * @param string|null $notes Optional notes for the attendance
     * @throws MembershipExpiredException
     * @throws QuotaExhaustedException
     * @throws AlreadyCheckedInException
     */
    public function checkIn(MemberMembership $memberMembership, ?User $recordedBy = null, ?string $notes = null): Attendance
    {
        // Validate membership status
        if ($memberMembership->status !== 'active') {
            throw new MembershipExpiredException('Membership is not active.');
        }

        // Validate not expired
        if ($memberMembership->isExpired()) {
            // Update status to expired
            $memberMembership->update(['status' => 'expired']);
            throw new MembershipExpiredException('Membership has expired.');
        }

        // Validate quota available (null = unlimited)
        $remaining = $memberMembership->remaining_qty;
        if ($remaining !== null && $remaining <= 0) {
            throw new MembershipExpiredException('Attendance quota has been used up.');
        }

        // Check if already checked-in - REMOVED for visit logging simplification
        // $existingCheckIn = $this->repository->findCurrentlyCheckedIn($memberMembership);
        // if ($existingCheckIn) {
        //     throw new AlreadyCheckedInException('Member is already checked-in.');
        // }

        // Create attendance with snapshot (including who recorded it)
        return $this->repository->create([
            'member_membership_id' => $memberMembership->id,
            'snapshot_member_name' => $memberMembership->member->name,
            'snapshot_membership_name' => $memberMembership->snapshot_membership_name,
            'snapshot_remaining_before' => $memberMembership->remaining_qty,
            'check_in_at' => now(),
            'notes' => $notes,
            'recorded_by_user_id' => $recordedBy?->id,
            'snapshot_recorded_by_name' => $recordedBy?->name,
        ]);
    }
}
