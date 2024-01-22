<?php
namespace App\Repositories\Admin\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * [login description]
     *
     * @param   [type]  $data  [$data description]
     *
     * @return  mixed          [return description]
     */
    public function login($data): mixed;

    /**
     * [getAllUser description]
     *
     * @return  mixed   [return description]
     */
    public function getAllUser(): mixed;

    /**
     * [getInfoUser description]
     *
     * @return  mixed   [return description]
     */
    public function getInfoUser($id): mixed;

    /**
     * [updateUser description]
     *
     * @param   [type]  $data  [$data description]
     * @param   [type]  $id    [$id description]
     *
     * @return  mixed          [return description]
     */
    public function updateUser($data, $userId): mixed;

    /**
     * [deleteUser description]
     *
     * @param   [type]  $userId  [$userId description]
     *
     * @return  mixed            [return description]
     */
    public function deleteUser($userId): mixed;

    /**
     * [createUser description]
     *
     * @param   [type]  $data  [$data description]
     *
     * @return  mixed          [return description]
     */ 
    public function createUser($data): mixed;
}