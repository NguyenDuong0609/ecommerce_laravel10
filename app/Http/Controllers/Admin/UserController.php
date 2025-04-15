<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Exception;

use App\Http\Requests\SignUpUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\LoginRequest;
use App\Exceptions\Api\LoginException;
use App\Exceptions\Api\UserException;
use App\Transformers\AccessTokenTransformer;
use App\Transformers\UserTransformer;
use App\Services\Admin\UserService;

/**
 * Class UserController
 *
 * Controller handling all user-related operations in the admin section
 * including authentication, user management and profile operations.
 *
 * @package App\Http\Controllers\Admin
 *
 * @method JsonResponse login(LoginRequest $request) Handle user login and return access token
 * @method JsonResponse signup(SignUpUserRequest $request) Handle user registration
 * @method JsonResponse logout() Handle user logout
 * @method JsonResponse me() Get authenticated user information
 * @method JsonResponse index() List all users
 * @method JsonResponse getUserInfo(int $id) Get specific user information
 * @method JsonResponse update(int $id, UpdateUserRequest $request) Update user information
 * @method JsonResponse destroy(int $id) Delete a user
 * @method JsonResponse store(UserRequest $request) Create a new user
 *
 * @property UserService $userService Service layer for user operations
 * @property AccessTokenTransformer $accessTokenTransformer Transformer for access token responses
 */
class UserController extends Controller
{
    /**
     * The user service instance for handling business logic.
     *
     * @var UserService
     */
    protected $userService;

    /**
     * The access token transformer instance.
     *
     * @var AccessTokenTransformer
     */
    protected $accessTokenTransformer;

    /**
     * Create a new UserController instance.
     *
     * @param  UserService            $userService            Service for user operations
     * @param  AccessTokenTransformer $accessTokenTransformer Transformer for token responses
     * @return void
     */
    public function __construct(UserService $userService, AccessTokenTransformer $accessTokenTransformer)
    {
        parent::__construct();
        $this->userService = $userService;
        $this->accessTokenTransformer = $accessTokenTransformer;
    }

    /**
     * Authenticate user and generate access token.
     *
     * @param  LoginRequest $request Contains email and password credentials
     * @return JsonResponse         Returns token and user info on success
     * @throws LoginException      When authentication fails
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $authenticatedUser = $this->userService->login($credentials);

            $user = $authenticatedUser['user'];
            $token = $authenticatedUser['token'];

            Log::info(config('messages.LOGIN.LOGIN_SUCCESS'), ["user"=> $user->email]);

            return $this->response
                ->withOk($this->accessTokenTransformer->transform($authenticatedUser))
                ->cookie('token', $token->accessToken);
        } catch (LoginException $e) {
            return $e->getResponse();
         }
    }

    /**
     * Register a new user and generate access token.
     *
     * @param  SignUpUserRequest $request User registration data
     * @return JsonResponse             Returns token and user info on success
     * @throws LoginException          When registration fails
     */
    public function signup(SignUpUserRequest $request): JsonResponse
    {
        try {
            $signupResult = $this->userService->signup($request);
            Log::info(
                config('messages.SIGNUP.SIGNUP_SUCCESS'),
                ["user"=> $signupResult['user']->email]);

            return $this->response
                ->withOk($this->accessTokenTransformer->transform($signupResult))
                ->cookie('token', $signupResult['token']->accessToken);
        } catch (LoginException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Invalidate the current user's access token.
     *
     * @return JsonResponse Returns success message on logout
     * @throws Exception   When logout operation fails
     */
    public function logout(): JsonResponse
    {
        try {
            $this->userService->logout();
            Log::info(config('messages.LOGOUT.LOGOUT_SUCCESS'));

            return $this->response->withSuccess(config('messages.LOGOUT.LOGOUT_SUCCESS'));
        } catch (Exception $e) {
            Log::error(config('messages.LOGOUT.LOGOUT_FAIL'), [
                "error" => $e->getMessage(),
                "user" => Auth::user()->email
            ]);
            return $this->response->withError(config('messages.LOGOUT.LOGOUT_FAIL'));
        }
    }

    /**
     * Get authenticated user's profile information.
     *
     * @return JsonResponse            Returns user profile data
     * @throws Exception              When profile retrieval fails
     */
    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();
            $data = $this->userService->getUserInfo($user->id);
            Log::info(config('messages.USER.GET_ME_INFO_SUCCESS'));
            Log::info('Data type: ' . get_class($data));
            return $this->response->item($data, new UserTransformer());
        } catch (Exception $e) {
            Log::error(config('messages.USER.GET_ME_INFO_FAIL'), [
                "error" => $e->getMessage(),
                "user" => Auth::user()->email
            ]);
            return response()->json([
                'message' => config('messages.USER.GET_ME_INFO_FAIL'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated list of all users.
     *
     * @return JsonResponse        Returns transformed user collection
     * @throws UserException      When users retrieval fails
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();
            Log::info(config('messages.USER.GET_ALL_USER_SUCCESS'));
            return $this->response->collection($users, new UserTransformer());
        } catch (UserException $e) {
            report($e);
            return $e->getResponse();
        }
    }

    /**
     * Get specific user information by ID.
     *
     * @param  int $id           The user ID to retrieve
     * @return JsonResponse     Returns user information
     * @throws UserException   When user not found or retrieval fails
     */
    public function getUserInfo(int $id): JsonResponse
    {
        try {
            $data = $this->userService->getUserInfo($id);
            Log::info(config('messages.USER.GET_INFO_USER_SUCCESS'));

            return $this->response->withOk($data);
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Update user information.
     *
     * @param  int               $id      The user ID to update
     * @param  UpdateUserRequest $request Updated user data
     * @return JsonResponse             Returns success message
     * @throws UserException           When update operation fails
     */
    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $this->userService->update($id, $request->all());

            Log::info(config('messages.USER.UPDATE_USER_SUCCESS'));

            return $this->response->withSuccess(config('messages.USER.UPDATE_USER_SUCCESS'));
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Delete user from the system.
     *
     * @param  int $id           The user ID to delete
     * @return JsonResponse     Returns success message
     * @throws UserException   When deletion fails
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->delete($id);
            Log::info(config('messages.USER.DELETE_USER_SUCCESS'));

            return $this->response->withSuccess(config('messages.USER.DELETE_USER_SUCCESS'));
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Create a new user.
     *
     * @param  UserRequest $request New user data
     * @return JsonResponse       Returns created user data
     * @throws UserException     When creation fails
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->all());

            Log::info(config('messages.USER.CREATE_USER_SUCCESS'), [
                "user"=> $user['email'],
            ]);

           return $this->response->withSuccess(
                config('messages.USER.CREATE_USER_SUCCESS'),
                ['user' => $user]
            );
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }
}
