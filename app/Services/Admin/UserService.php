<?php

namespace App\Services\Admin;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Repositories\Admin\User\UserRepositoryInterface;
use App\Http\Requests\SignUpUserRequest;
use App\Exceptions\Api\LoginException;
use App\Exceptions\Api\UserException;
use App\Models\User;

/**
 * Class UserService
 *
 * Service class handling user-related business logic including authentication,
 * user management, and CRUD operations.
 *
 * @package App\Services\Admin
 *
 * Authentication Methods:
 * @method array login(array $credentials) Authenticate user and generate access token
 * @method array signup(SignUpUserRequest $request) Register new user and generate token
 * @method bool logout() Revoke current user's access token
 *
 * User Management Methods:
 * @method LengthAwarePaginator getAllUsers() Get paginated list of all users
 * @method User getUserInfo(int|string $id) Get user information by ID
 * @method User update(int $id, array $data) Update user information
 * @method bool deleteUser(int $id) Delete user from system
 * @method User createUser(array $data) Create new user in system
 *
 * Class Properties:
 * @property UserRepositoryInterface $userRepository Repository for user data operations
 *
 * Exceptions:
 * @throws LoginException When authentication operations fail
 * @throws UserException When user operations fail
 *
 * Dependencies:
 * @see \App\Repositories\Admin\User\UserRepositoryInterface
 * @see \App\Models\User
 * @see \App\Exceptions\Api\LoginException
 * @see \App\Exceptions\Api\UserException
 * @see \Illuminate\Pagination\LengthAwarePaginator
 */
class UserService
{
    /**
     * User repository instance.
     *
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Create a new UserService instance.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate user and generate access token.
     *
     * @param  array $credentials User login credentials (email, password)
     * @return array             ['user' => User, 'token' => AccessToken]
     * @throws LoginException    When authentication fails
     */
    public function login(array $credentials): array
    {
       $user =  $this->userRepository->findByCredentials($credentials);

       if (!$user) {
            throw new LoginException(config('messages.LOGIN.WRONG_PASSWORD_OR_USERNAME'), "",Response::HTTP_UNAUTHORIZED);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if (isset($credentials['remember_me'])) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        return [
            'user' => Auth::user(),
            'token' => $tokenResult
        ];
    }

    /**
     * Register a new user and generate access token.
     *
     * @param  SignUpUserRequest $request Registration data
     * @return array                     ['user' => User, 'token' => AccessToken]
     * @throws LoginException            When registration or authentication fails
     */
    public function signup(SignUpUserRequest $request): array
    {
        $user = $this->userRepository->createUser($request->all());

        $credentials = $request->only(['email', 'password']);
        if(!Auth::attempt($credentials)) {
           throw new LoginException(
            config('messages.LOGIN.WRONG_PASSWORD_OR_USERNAME'),
            ""
            ,Response::HTTP_UNAUTHORIZED);
        }

        $accessTokenResult = $user->createToken('Personal Access Token');
        $token = $accessTokenResult->token;

        if ($request->has('remember_me')) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        return [
            'user' => Auth::user(),
            'token' => $accessTokenResult
        ];
    }

    /**
     * Revoke the current user's access token.
     *
     * @return bool True if token was revoked, false otherwise
     */
    public function logout(): bool
    {
        $user = Auth::user();
        $token = $user?->token();

        if ($token) {
            $token->revoke();
            return true;
        }
        return false;
    }

    /**
     * Get paginated list of all users.
     *
     * @return LengthAwarePaginator Paginated user collection
     */
    public function getAllUsers(): LengthAwarePaginator
    {
        return $this->userRepository->getAllUsers();
    }

    /**
     * Get user information by ID.
     *
     * @param  int|string $id User ID
     * @return User          User model instance
     * @throws UserException When user not found
     */
    public function getUserInfo(int $id): User
    {
        return $this->userRepository->getInfoUser($id);
    }

    /**
     * Update user information by ID.
     *
     * @param  int   $id   User ID
     * @param  array $data Updated user data
     * @return User       Updated user model
     * @throws UserException When user not found or update fails
     */
    public function update(int $id, array $data): User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'), "", Response::HTTP_NOT_FOUND);
        }

        $updatedUser = $this->userRepository->updateById($id, $data);

        if (!$updatedUser) {
            throw new UserException(config('messages.USER.UPDATE_USER_FAIL'), "", Response::HTTP_NOT_FOUND);
        }

        return $updatedUser;
    }

    /**
     * Delete user by ID.
     *
     * @param  int   $id User ID
     * @return bool     True if deletion successful
     * @throws UserException When deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->userRepository->deleteById($id);
    }

    /**
     * Create a new user.
     *
     * @param  array $data User data
     * @return User       Created user model
     * @throws UserException When creation fails
     */
    public function create(array $data): User
    {
        return $this->userRepository->createUser($data);
    }
}
