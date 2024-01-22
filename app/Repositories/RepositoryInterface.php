<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * [getAll description]
     *
     * @return  mixed   [return description]
     */
    public function getAll(): mixed;

    /**
     * [find description]
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  mixed        [return description]
     */
    public function find($id): mixed;

    /**
     * [create description]
     *
     * @param   array  $attributes  [$attributes description]
     *
     * @return  mixed               [return description]
     */
    public function create(array $attributes): mixed;

    /**
     * [update description]
     *
     * @param   [type] $id          [$id description]
     * @param   array  $attributes  [$attributes description]
     *
     * @return  mixed               [return description]
     */
    public function update($id, array $attributes): mixed;

    /**
     * [delete description]
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  mixed        [return description]
     */
    public function delete($id): mixed;

    /**
     * [findFirstByField description]
     *
     * @param   [type]  $field  [$field description]
     * @param   [type]  $value  [$value description]
     *
     * @return  mixed           [return description]
     */
    public function findFirstByField($field, $value = null): mixed;
}