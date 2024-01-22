<?php

namespace App\Services\Admin;

use App\Repositories\Admin\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use App\Exceptions\Api\LoginException;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($data): mixed
    {
       $authedUser =  $this->userRepository->login($data);
        
       return $authedUser;
    }

    public function signup($data): mixed
    {
        $user = $this->userRepository->createUser($data);

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)) {
           throw new LoginException(config('messages.LOGIN.WRONG_PASSWORD_OR_USERNAME'), "",Response::HTTP_UNAUTHORIZED);
        }
        
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if (isset($data->remember_me)) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        
        return $user = [
            'user' => Auth::user(),
            'token' => $tokenResult
        ];
    }

    public function logout($request)
    {
        return $request->user()->token()->revoke();
    }

    public function getAllUser()
    {
        $data = $this->userRepository->getAllUser();
        return $data;
    }

    public function getInfoUser($id)
    {
        $data = $this->userRepository->getInfoUser($id);
        return $data;
    }

    public function updateUser($data, $id)
    {
        return $this->userRepository->updateUser($data, $id);
    }

    public function deleteUser($id)
    {
        return $this->userRepository->deleteUser($id);
    }

    public function createUser($data)
    {
        return $this->userRepository->createUser($data);
    }
}