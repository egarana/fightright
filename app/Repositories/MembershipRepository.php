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
    public function paginate(int $perPage = 15, ?string $sort = null, ?string $search = null, ?string $fields = null): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Handle search with dynamic fields
        if ($search) {
            $searchFields = $fields ? explode(',', $fields) : ['name'];
            $allowedFields = ['name', 'description'];
            $validFields = array_intersect($searchFields, $allowedFields);

            if (!empty($validFields)) {
                $query->where(function ($q) use ($search, $validFields) {
                    foreach ($validFields as $field) {
                        $q->orWhere($field, 'like', "%{$search}%");
                    }
                });
            }
        }

        // Handle sort
        if ($sort) {
            $direction = 'asc';
            $field = $sort;

            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $field = substr($sort, 1);
            }

            // Only allow sorting on valid columns
            $allowedSorts = ['name', 'duration_days', 'max_attendance_qty', 'price', 'is_active', 'created_at', 'updated_at'];
            if (in_array($field, $allowedSorts)) {
                $query->orderBy($field, $direction);
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($perPage);
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
