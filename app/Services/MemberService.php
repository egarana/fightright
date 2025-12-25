<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\MemberRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MemberService
{
    public function __construct(
        protected MemberRepository $repository
    ) {}

    /**
     * Get all members.
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get paginated members.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get member by ID.
     */
    public function getById(int $id): ?Member
    {
        return $this->repository->find($id);
    }

    /**
     * Get member by ID or fail.
     */
    public function getByIdOrFail(int $id): Member
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get member with memberships.
     */
    public function getWithMemberships(int $id): ?Member
    {
        return $this->repository->findWithMemberships($id);
    }

    /**
     * Create new member.
     */
    public function create(array $data): Member
    {
        return $this->repository->create($data);
    }

    /**
     * Update member.
     */
    public function update(Member $member, array $data): Member
    {
        return $this->repository->update($member, $data);
    }

    /**
     * Delete member.
     */
    public function delete(Member $member): bool
    {
        return $this->repository->delete($member);
    }

    /**
     * Search members.
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }
}
