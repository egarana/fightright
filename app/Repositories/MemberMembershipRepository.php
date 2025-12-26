<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\MemberMembership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MemberMembershipRepository
{
    public function __construct(
        protected MemberMembership $model
    ) {}

    /**
     * Get all member memberships.
     */
    public function all(): Collection
    {
        return $this->model
            ->with(['member', 'membership'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated member memberships.
     */
    /**
     * Get paginated member memberships.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['member', 'membership'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find member membership by ID.
     */
    public function find(int $id): ?MemberMembership
    {
        return $this->model->with(['member', 'membership', 'attendances'])->find($id);
    }

    /**
     * Find member membership by ID or fail.
     */
    public function findOrFail(int $id): MemberMembership
    {
        return $this->model->with(['member', 'membership', 'attendances'])->findOrFail($id);
    }

    /**
     * Get all memberships for a member.
     */
    public function getByMember(Member $member): Collection
    {
        return $this->model
            ->where('member_id', $member->id)
            ->with(['membership', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active memberships for a member.
     */
    public function getActiveByMember(Member $member): Collection
    {
        return $this->model
            ->where('member_id', $member->id)
            ->active()
            ->notExpired()
            ->with(['membership', 'attendances'])
            ->orderBy('expired_at', 'asc')
            ->get();
    }

    /**
     * Create new member membership.
     */
    public function create(array $data): MemberMembership
    {
        return $this->model->create($data);
    }

    /**
     * Update member membership.
     */
    public function update(MemberMembership $memberMembership, array $data): MemberMembership
    {
        $memberMembership->update($data);

        return $memberMembership->fresh();
    }

    /**
     * Delete member membership.
     */
    public function delete(MemberMembership $memberMembership): bool
    {
        return $memberMembership->delete();
    }

    /**
     * Update status to cancelled.
     */
    public function cancel(MemberMembership $memberMembership): MemberMembership
    {
        $memberMembership->update(['status' => 'cancelled']);

        return $memberMembership->fresh();
    }
}
