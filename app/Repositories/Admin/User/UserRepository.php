<?php
namespace App\Repositories\Admin\User;

use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;

use App\Exceptions\Api\LoginException;
use App\Exceptions\Api\UserException;
use App\Enums\Paginate;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * [__contruct description]
     *
     * @return  [type]  [return description]
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * [getModel description]
     *
     * @return  mixed    [return description]
     */
    public function getModel(): mixed
    {
        return User::class;
    }

    /**
     * [login description]
     *
     * @return  mixed   [return description]
     */
    public function login($data): mixed
    {
        $validator = Validator::make($data->all(), [
            'email' => 'email|required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)) {
           throw new LoginException(config('messages.LOGIN.WRONG_PASSWORD_OR_USERNAME'), "",Response::HTTP_UNAUTHORIZED);
        }

        $user = $data->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($data->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        return $authedUser = [
            'user' => Auth::user(),
            'token' => $tokenResult
        ];
    }

    public function createUser($data): mixed
    {
        $user = $this->_model->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password)
        ]);
        if(!$user) {
            throw new UserException(config('messages.USER.CREATE_USER_FAIL'),"", Response::HTTP_BAD_GATEWAY);
        }

        return $user;
    }

    public function getAllUser(): mixed
    {
        $perPage = $_GET['limit'] ?? Paginate::LIMIT_PAGINATE;
        $data = $this->getAll()->paginate($perPage);
        if(!$data) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'),"", Response::HTTP_NOT_FOUND);
        }

        return $data;
    }

    public function getInfoUser($id): mixed
    {
        $data = $this->find($id);
        // Use Elastic search
        // $data = User::search($id)->get();
        if(!$data) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'),"", Response::HTTP_NOT_FOUND);
        }

        return $data;
    }

    public function updateUser($data, $id): mixed
    {
        $data = $this->update($id, $data);
        if(!$data) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'),"", Response::HTTP_NOT_FOUND);
        }

        return true;
    }

    public function deleteUser($id): mixed
    {
        $data = $this->delete($id);
        if(!$data) {
            throw new UserException(config('messages.USER.USER_NOT_FOUND'),"", Response::HTTP_NOT_FOUND);
        }

        return true;
    }
}