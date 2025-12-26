<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\Membership;
use App\Repositories\MemberMembershipRepository;
use App\Repositories\MembershipRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MemberMembershipService
{
    public function __construct(
        protected MemberMembershipRepository $repository,
        protected MembershipRepository $membershipRepository
    ) {}

    /**
     * Get all member memberships.
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get paginated member memberships.
     */
    /**
     * Get paginated member memberships.
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Get member membership by ID.
     */
    public function getById(int $id): ?MemberMembership
    {
        return $this->repository->find($id);
    }

    /**
     * Get member membership by ID or fail.
     */
    public function getByIdOrFail(int $id): MemberMembership
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get all memberships for a member.
     */
    public function getByMember(Member $member): Collection
    {
        return $this->repository->getByMember($member);
    }

    /**
     * Get active memberships for a member.
     */
    public function getActiveByMember(Member $member): Collection
    {
        return $this->repository->getActiveByMember($member);
    }

    /**
     * Assign membership to member with snapshot.
     */
    public function assignMembership(Member $member, Membership $membership, ?string $startedAt = null): MemberMembership
    {
        $startedAt = $startedAt ? now()->parse($startedAt) : now();
        $expiredAt = $startedAt->copy()->addDays($membership->duration_days);

        return $this->repository->create([
            'member_id' => $member->id,
            'membership_id' => $membership->id,
            'snapshot_membership_name' => $membership->name,
            'snapshot_max_attendance_qty' => $membership->max_attendance_qty,
            'snapshot_duration_days' => $membership->duration_days,
            'snapshot_price' => $membership->price,
            'started_at' => $startedAt,
            'expired_at' => $expiredAt,
            'status' => 'active',
        ]);
    }

    /**
     * Cancel member membership.
     */
    public function cancel(MemberMembership $memberMembership): MemberMembership
    {
        return $this->repository->cancel($memberMembership);
    }

    /**
     * Delete member membership.
     */
    public function delete(MemberMembership $memberMembership): bool
    {
        return $this->repository->delete($memberMembership);
    }
}
