<?php

namespace App\Repositories;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MemberRepository
{
    public function __construct(
        protected Member $model
    ) {}

    /**
     * Get all members.
     */
    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Get paginated members.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate($perPage);
    }

    /**
     * Find member by ID.
     */
    public function find(int $id): ?Member
    {
        return $this->model->find($id);
    }

    /**
     * Find member by ID or fail.
     */
    public function findOrFail(int $id): Member
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find member by email.
     */
    public function findByEmail(string $email): ?Member
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create new member.
     */
    public function create(array $data): Member
    {
        return $this->model->create($data);
    }

    /**
     * Update member.
     */
    public function update(Member $member, array $data): Member
    {
        $member->update($data);

        return $member->fresh();
    }

    /**
     * Delete member.
     */
    public function delete(Member $member): bool
    {
        return $member->delete();
    }

    /**
     * Get member with memberships.
     */
    public function findWithMemberships(int $id): ?Member
    {
        return $this->model
            ->with(['memberMemberships.membership', 'memberMemberships.attendances'])
            ->find($id);
    }

    /**
     * Search members by name or email.
     */
    public function search(string $query): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }
}
