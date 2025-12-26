<?php

namespace App\Services;

use App\Models\Membership;
use App\Repositories\MembershipRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MembershipService
{
    public function __construct(
        protected MembershipRepository $repository
    ) {}

    /**
     * Get all memberships.
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get all active memberships.
     */
    public function getAllActive(): Collection
    {
        return $this->repository->allActive();
    }

    /**
     * Get paginated memberships.
     */
    public function getPaginated(int $perPage = 15, ?string $sort = null, ?string $search = null, ?string $fields = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $sort, $search, $fields);
    }

    /**
     * Get membership by ID.
     */
    public function getById(int $id): ?Membership
    {
        return $this->repository->find($id);
    }

    /**
     * Get membership by ID or fail.
     */
    public function getByIdOrFail(int $id): Membership
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create new membership.
     */
    public function create(array $data): Membership
    {
        return $this->repository->create($data);
    }

    /**
     * Update membership.
     */
    public function update(Membership $membership, array $data): Membership
    {
        return $this->repository->update($membership, $data);
    }

    /**
     * Delete membership.
     */
    public function delete(Membership $membership): bool
    {
        return $this->repository->delete($membership);
    }
}
