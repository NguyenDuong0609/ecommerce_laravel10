<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

use App\Services\Admin\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Requests\SignUpUserRequest;
use App\Http\Requests\LoginRequest;
use App\Exceptions\Api\LoginException;
use App\Exceptions\Api\UserException;
use App\Http\Requests\UpdateUserRequest;
use App\Transformers\AccessTokenTransformer;
use App\Transformers\UserTransformer;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        parent::__contruct();
        $this->userService = $userService;
    }

    public function login(LoginRequest $request)
    {
        try {
            $authedUser = $this->userService->login($request);
            Log::info(config('messages.LOGIN.LOGIN_SUCCESS'), ["user"=> $authedUser['user']->email]);
            return $this->reponse->withOk((new AccessTokenTransformer())->transform($authedUser))->cookie('token', $authedUser['token']->accessToken);
        } catch (LoginException $e) {
            return $e->getResponse();
         }
    }

    public function signup(SignUpUserRequest $request)
    {
        try {
            $user = $this->userService->signup($request);
            Log::info(config('messages.SIGNUP.SIGNUP_SUCCESS'), ["user"=> $user['user']->email]);
            return $this->reponse->withOk((new AccessTokenTransformer())->transform($user))->cookie('token', $user['token']->accessToken);
        } catch (LoginException $e) {
            return $e->getResponse();
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->userService->logout($request);
            Log::info(config('messages.LOGOUT.LOGOUT_SUCCESS'));
            return $this->reponse->withSucess(config('messages.LOGOUT.LOGOUT_SUCCESS'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function me()
    {
        try {
            $data = $this->userService->getInfoUser(Auth::user());
            Log::info(config('messages.USER.GET_ME_INFO_SUCCESS'));
            return $this->reponse->collection($data, new UserTransformer());
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllUser()
    {
        try {
            $data = $this->userService->getAllUser();
            Log::info(config('messages.USER.GET_ALL_USER_SUCCESS'));
            return $this->reponse->collection($data, new UserTransformer());
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    public function getInfoUser($id)
    {
        try {
            $data = $this->userService->getInfoUser($id);
            Log::info(config('messages.USER.GET_INFO_USER_SUCCESS'));
            return $this->reponse->withOk($data);
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    public function updateUser(UpdateUserRequest $request, $id)
    {
        try {
            $this->userService->updateUser($request->all(), $id);
            Log::info(config('messages.USER.UPDATE_USER_SUCCESS'));
            return $this->reponse->withSucess(config('messages.USER.UPDATE_USER_SUCCESS'));
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    public function deleteUser($id)
    {
        try {
            $this->userService->deleteUser($id);
            Log::info(config('messages.USER.DELETE_USER_SUCCESS'));
            return $this->reponse->withSucess(config('messages.USER.DELETE_USER_SUCCESS'));
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }

    public function createUser(UserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request);
            Log::info(config('messages.USER.CREATE_USER_SUCCESS'), ["user"=> $user['email']]);
           return $this->reponse->withSucess(config('messages.USER.CREATE_USER_SUCCESS'));
        } catch (UserException $e) {
            return $e->getResponse();
        }
    }
}
