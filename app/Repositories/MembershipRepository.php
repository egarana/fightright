<?php

namespace App\Repositories;

use App\Models\Membership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MembershipRepository
{
    public function __construct(
        protected Membership $model
    ) {}

    /**
     * Get all memberships.
     */
    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Get all active memberships.
     */
    public function allActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    /**
     * Get paginated memberships.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate($perPage);
    }

    /**
     * Find membership by ID.
     */
    public function find(int $id): ?Membership
    {
        return $this->model->find($id);
    }

    /**
     * Find membership by ID or fail.
     */
    public function findOrFail(int $id): Membership
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create new membership.
     */
    public function create(array $data): Membership
    {
        return $this->model->create($data);
    }

    /**
     * Update membership.
     */
    public function update(Membership $membership, array $data): Membership
    {
        $membership->update($data);

        return $membership->fresh();
    }

    /**
     * Delete membership.
     */
    public function delete(Membership $membership): bool
    {
        return $membership->delete();
    }
}
