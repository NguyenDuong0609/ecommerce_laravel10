<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

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
     * @return Model|false
     */
    public function updateById(int $id, array $attributes): Model|false
    {
        $model = $this->find($id);

        if ($model) {
            $model->update($attributes);
            return $model;
        }
        return false;
    }

    /**
        * Delete model by ID.
        *
        * @param int $id
        * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->find($id);
        if (!$model) {
            return false;
        }

        return (bool)$model->delete();
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
