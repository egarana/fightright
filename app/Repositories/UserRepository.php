<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function __construct(
        protected User $model
    ) {}

    /**
     * Get all users.
     */
    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Get paginated users.
     */
    public function paginate(int $perPage = 15, ?string $sort = null, ?string $search = null, ?string $fields = null): LengthAwarePaginator
    {
        $query = $this->model->with('roles');

        // Handle search with dynamic fields
        if ($search) {
            $searchFields = $fields ? explode(',', $fields) : ['name', 'email'];
            $allowedFields = ['name', 'email'];
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

            $allowedSorts = ['name', 'email', 'created_at', 'updated_at', 'roles'];
            if (in_array($field, $allowedSorts)) {
                if ($field === 'roles') {
                    // Sort by the first role name using subquery
                    $query->orderBy(
                        \Spatie\Permission\Models\Role::select('name')
                            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
                            ->whereColumn('model_has_roles.model_id', 'users.id')
                            ->where('model_has_roles.model_type', User::class)
                            ->limit(1),
                        $direction
                    );
                } else {
                    $query->orderBy($field, $direction);
                }
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find user by ID.
     */
    public function find(int $id): ?User
    {
        return $this->model->with('roles')->find($id);
    }

    /**
     * Find user by ID or fail.
     */
    public function findOrFail(int $id): User
    {
        return $this->model->with('roles')->findOrFail($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create new user.
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Update user.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    /**
     * Delete user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Search users by name or email.
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
