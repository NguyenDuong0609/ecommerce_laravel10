<?php
namespace App\Repositories\Admin\User;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Interface UserRepositoryInterface
 *
 * Defines contract for user data persistence and retrieval operations.
 * Extends base RepositoryInterface for common repository methods.
 *
 * @package App\Repositories\Admin\User
 *
 * Authentication Methods:
 * @method User findByCredentials(array $credentials) Find and authenticate user by credentials
 *
 * CRUD Operations:
 * @method User createUser(array $data) Create new user in the system
 * @method LengthAwarePaginator getAllUsers() Retrieve paginated list of all users
 * @method User getInfoUser(int|string $id) Get user information by ID
 * @method Model|false updateById(int $id, array $attributes) Update existing user
 * @method bool deleteById(int $id) Delete user from system
 *
 * @see \App\Repositories\RepositoryInterface
 * @see \App\Models\User
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Find and authenticate user by credentials.
     *
     * @param  array $credentials User login credentials (email, password)
     * @return User|null              Authenticated user instance
     * @throws LoginException    When authentication fails
     */
    public function findByCredentials(array $credentials): ?User;

    /**
     * Get paginated list of all users.
     *
     * @return LengthAwarePaginator Paginated collection of users
     * @throws UserException        When users retrieval fails
     */
    public function getAllUsers(): LengthAwarePaginator;

    /**
     * Get user information by ID.
     *
     * @param  int|string $id User ID
     * @return User          User model instance
     * @throws UserException When user not found
     */
    public function getInfoUser(int $id): User;

    /**
     * Update user information by ID.
     *
     * @param  int   $id         User ID to update
     * @param  array $attributes Updated user data
     * @return Model|false      Updated user model or false on failure
     * @throws UserException    When update fails
     */
    public function updateById(int $id,array $attributes): Model|false;

    /**
     * Delete user by ID.
     *
     * @param  int   $userId User ID to delete
     * @return bool         True if deletion successful
     * @throws UserException When deletion fails
     */
    public function deleteById(int $userId): bool;

    /**
     * Create a new user.
     *
     * @param  array $data User data (name, email, password, etc.)
     * @return User       Created user instance
     * @throws UserException When user creation fails
     */
    public function createUser(array $data): User;
}
