<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * Get all users.
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get paginated users.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get user by ID.
     */
    public function getById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    /**
     * Get user by ID or fail.
     */
    public function getByIdOrFail(int $id): User
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create new user.
     */
    public function create(array $data): User
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->repository->create($data);

        // Assign role if provided
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    /**
     * Update user.
     */
    public function update(User $user, array $data): User
    {
        // Hash password if provided and not empty
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->repository->update($user, $data);

        // Sync role if provided
        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    /**
     * Delete user.
     */
    public function delete(User $user): bool
    {
        return $this->repository->delete($user);
    }

    /**
     * Search users.
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }
}
