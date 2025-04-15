<?php
namespace App\Repositories\Admin\User;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Repositories\BaseRepository;
use App\Exceptions\Api\UserException;
use App\Enums\Paginate;
use App\Models\User;

/**
 * Class UserRepository
 *
 * Repository class handling user data persistence and retrieval operations.
 * Implements the UserRepositoryInterface and extends BaseRepository.
 *
 * @package App\Repositories\Admin\User
 *
 * Authentication Methods:
 * @method User findByCredentials(array $credentials) Find user by login credentials
 *
 * CRUD Operations:
 * @method User createUser(array $data) Create new user
 * @method LengthAwarePaginator getAllUsers() Get paginated list of users
 * @method User getInfoUser(int|string $id) Get user by ID
 * @method Model|false updateById(int $id, array $attributes) Update user by ID
 * @method bool deleteById(int $id) Delete user by ID
 *
 * Inherited Methods:
 * @method mixed getModel() Get the associated model class name
 *
 * Exceptions:
 * @throws LoginException When authentication fails
 * @throws UserException When user operations fail
 *
 * @see \App\Repositories\BaseRepository
 * @see \App\Models\User
 * @see \App\Repositories\Admin\User\UserRepositoryInterface
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Create a new UserRepository instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the model class name for the repository.
     *
     * @return string The User model class name
     */
    public function getModel(): string
    {
        return User::class;
    }

    /**
     * Find user by credentials and attempt authentication.
     *
     * @param  array $credentials Login credentials (email, password)
     * @return User|null         Authenticated user or null
     * @throws LoginException    When authentication fails
     */
    public function findByCredentials(array $credentials): ?User
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        return Auth::user();
    }

    /**
     * Create a new user record in the database.
     *
     * @param  array $data User data (name, email, password)
     * @return User       Created user instance
     * @throws UserException When user creation fails
     */
    public function createUser(array $data): User
    {
        $user = $this->_model->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password'])
        ]);

        if(!$user) {
            throw new UserException(
                config('messages.USER.CREATE_USER_FAIL'),
                "",
                Response::HTTP_BAD_GATEWAY);
        }

        return $user;
    }

    /**
     * Get paginated list of all users.
     *
     * @return LengthAwarePaginator Paginated collection of users
     * @throws UserException        When no users found
     */
    public function getAllUsers(): LengthAwarePaginator
    {
        $perPage = request('limit', Paginate::LIMIT_PAGINATE);

        $users = $this->getAll()->paginate($perPage);
        if($users->isEmpty()) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'),"", Response::HTTP_NOT_FOUND);
        }

        return $users;
    }

    /**
     * Get user information by ID.
     *
     * @param  int|string $id User ID
     * @return User          User model instance
     * @throws UserException When user not found
     */
    public function getInfoUser(int $id): User
    {
        $user = $this->find($id);
        // Use Elastic search
        // $data = User::search($id)->get()->first();
        if(!$user) {
            throw new UserException(
                config('messages.USER.USER_NOT_FOUND'),
                "",
                Response::HTTP_NOT_FOUND);
        }
        return $user;
    }

    /**
     * Update user by ID.
     *
     * @param  int   $id         User ID
     * @param  array $attributes Updated user data
     * @return Model|false      Updated user model or false on failure
     * @throws UserException    When update fails
     */
    public function updateById(int $id, array $attributes): Model|false
    {
        return parent::updateById($id, $attributes);
    }

    /**
     * Delete user by ID.
     *
     * @param  int   $id User ID
     * @return bool     True if deletion successful
     * @throws UserException When user not found or deletion fails
     */
    public function deleteById(int $id): bool
    {
        $deleted = $this->delete($id);

        if(!$deleted) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'),"", Response::HTTP_NOT_FOUND);
        }

        return true;
    }
}
