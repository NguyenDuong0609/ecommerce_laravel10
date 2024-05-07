<?php

namespace App\Repositories\Admin\User;

use Illuminate\Support\Facades\Redis;
use App\Repositories\BaseRepository;
use App\Models\User;
use App\Enums\Paginate;

class UserCachedRepository extends BaseRepository implements UserRepositoryInterface
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

    public function login($data): mixed
    {
        return $this->userRepository->login($data);
    }

    /**
     * [getAllUser description]
     *
     * @return  mixed   [return description]
     */
    public function getAllUser(): mixed
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $expire = rand(2419200, 4838400);
        $cachedKey = 'ListUser_page_' . $page;

        if (!Redis::exists($cachedKey) || (Redis::get($cachedKey) == null)) {
            Redis::setex($cachedKey, $expire, serialize($this->userRepository->getAllUser()));
        }
        return unserialize(Redis::get($cachedKey));
    }

    /**
     * [getInfoUser description]
     *
     * @return  mixed   [return description]
     */
    public function getInfoUser($id): mixed
    {
        $expire = rand(2419200, 4838400);
        $cachedKey = 'InfoUser_' . $id;

        if (!Redis::exists($cachedKey) || (Redis::get($cachedKey) == null)) {
            Redis::setex($cachedKey, $expire, serialize($this->userRepository->getInfoUser($id)));
        }
        return unserialize(Redis::get($cachedKey));
    }

    /**
     * [updateUser description]
     *
     * @param   [type]  $data  [$data description]
     * @param   [type]  $id    [$id description]
     *
     * @return  mixed          [return description]
     */
    public function updateUser($data, $userId): mixed
    {
        $expire = rand(2419200, 4838400);
        $cachedKey = 'InfoUser_' . $userId;
        $this->userRepository->updateUser($data, $userId);

        if (Redis::exists($cachedKey))
            Redis::del($cachedKey);
        Redis::setex($cachedKey, $expire, serialize($this->userRepository->getInfoUser($userId)));

        return unserialize(Redis::get($cachedKey));
    }

    /**
     * [deleteUser description]
     *
     * @param   [type]  $userId  [$userId description]
     *
     * @return  mixed            [return description]
     */
    public function deleteUser($userId): mixed
    {
        $cachedKey = 'InfoUser_' . $userId;
        $this->userRepository->deleteUser($userId);

        if (Redis::exists($cachedKey))
            Redis::del($cachedKey);

        return true;
    }

    /**
     * [createUser description]
     *
     * @param   [type]  $data  [$data description]
     *
     * @return  mixed          [return description]
     */ 
    public function createUser($data): mixed
    {
        $expire = rand(2419200, 4838400);
        $user = $this->userRepository->createUser($data);
        $cachedKey = 'InfoUser_' . $user->id;

        if (!Redis::exists($cachedKey) || (Redis::get($cachedKey) == null)) {
            Redis::setex($cachedKey, $expire, serialize($user));
        }

        return unserialize(Redis::get($cachedKey));
    }
}