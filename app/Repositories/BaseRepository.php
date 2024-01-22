<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * [$_model description]
     *
     * @var [type]
     */
    protected $_model;

    /**
     * EloquentRepository constructor.
     */
    public function __construct()
    {
        $this->_model = $this->setModel();
    }

    /**
     * [getModel description]
     *
     * @return  string  [return description]
     */
    abstract public function getModel(): mixed;

    /**
     * [setModel description]
     *
     * @return  [type]  [return description]
     */
    public function setModel()
    {
        return $this->_model = app()->make(
            $this->getModel()
        );
    }

    /**
     * [getAll description]
     *
     * @return  Collect [return description]
     */
    public function getAll(): mixed
    {
        return $this->_model->all();
    }

    /**
     * [find description]
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  mixed        [return description]
     */
    public function find($id): mixed
    {
        $result = $this->_model->find($id);
        return $result;
    }

    /**
     * [create description]
     *
     * @param   array  $attributes  [$attributes description]
     *
     * @return  mixed               [return description]
     */
    public function create(array $attributes): mixed
    {
        return $this->_model->create($attributes);
    }

    /**
     * [update description]
     *
     * @param   [type] $id          [$id description]
     * @param   array  $attributes  [$attributes description]
     *
     * @return  bool|mixed                [return description]
     */
    public function update($id, array $attributes): mixed
    {
        $result = $this->find($id);

        if ($result) {
            $result->update($attributes);
            return $result;
        }
        return false;
    }

    /**
     * [delete description]
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  bool         [return description]
     */
    public function delete($id): bool
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();
            return true;
        }

        return false;
    }

    /**
     * [findFirstByField description]
     *
     * @param   [type]  $field  [$field description]
     * @param   [type]  $value  [$value description]
     *
     * @return  mixed           [return description]
     */
    public function findFirstByField($field, $value = null): mixed
    {
        $model = $this->_model->where($field, '=', $value)->first();

        return $model;
    }
}