<?php

namespace App\Support;

use App\Transformers\EmptyTransformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;

class Transform
{
    /**
     * Fractal manager.
     *
     * @var \League\Fractal\Manager
     */
    private $fractal;

    /**
     * Create a new class instance.
     *
     * @param Manager $fractal
     */
    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;

        if (request()->has('include')) {
            $this->fractal->parseIncludes(request()->query('include'));
        }

        $this->fractal->setSerializer(new DataArraySerializer);
    }

    /**
     * Transform a collection of data.
     *
     * @param $data
     * @param TransformerAbstract|null $transformer
     * @return array
     * @throws \Exception
     */
    public function collection($data, TransformerAbstract $transformer = null)
    {
        $transformer = $transformer ?: $this->fetchDefaultTransformer($data);

        $collection = new FractalCollection($data, $transformer);

        if ($data instanceof LengthAwarePaginator) {
            $collection->setPaginator(new IlluminatePaginatorAdapter($data));
            $result = $this->fractal->createData($collection)->toArray();
            return [
                "success" => true,
                "data" => $result['data'],
                "pagination" => $result['meta']['pagination']
            ];
        } else {
            $result = $this->fractal->createData($collection)->toArray()['data'];
            return [
                "success" => true,
                "data" => $result
            ];
        }
    }

     /**
     * Transform a collection of data.
     *
     * @param $data
     * @param TransformerAbstract|null $transformer
     * @return array
     * @throws \Exception
     */
    public function collection_sale($data, TransformerAbstract $transformer = null, $total_profit = 0)
    {
        $transformer = $transformer ?: $this->fetchDefaultTransformer($data);

        $collection = new FractalCollection($data, $transformer);

        if ($data instanceof LengthAwarePaginator) {
            $collection->setPaginator(new IlluminatePaginatorAdapter($data));
            $result = $this->fractal->createData($collection)->toArray();
            return [
                "success" => true,
                "data" => $result['data'],
                'total_profit' => $total_profit,
                "pagination" => $result['meta']['pagination']
            ];
        } else {
            return $this->fractal->createData($collection)->toArray()['data'];
        }
    }

    /**
     * Transform a single data.
     *
     * @param $data
     * @param TransformerAbstract|null $transformer
     * @return array
     * @throws \Exception
     */
    public function item($data, TransformerAbstract $transformer = null)
    {
        $transformer = $transformer ?: $this->fetchDefaultTransformer($data);

        return $this->fractal->createData(
            new FractalItem($data, $transformer)
        )->toArray()['data'];
    }

    /**
     * Tries to fetch a default transformer for the given data.
     *
     * @param $data
     *
     * @return EmptyTransformer
     * @throws \Exception
     */
    protected function fetchDefaultTransformer($data)
    {
        if (($data instanceof LengthAwarePaginator || $data instanceof Collection) && $data->isEmpty()) {
            return new EmptyTransformer();
        }

        $className = $this->getClassName($data);

        if ($this->hasDefaultTransformer($className)) {
            $transformer = config('api.transformers.' . $className);
        } else {
            $classBasename = class_basename($className);

            if (!class_exists($transformer = "App\\Transformers\\{$classBasename}Transformer")) {
                throw new \Exception("No transformer for {$className}");
            }
        }

        return new $transformer;
    }

    /**
     * Check if the class has a default transformer.
     *
     * @param $className
     *
     * @return bool
     */
    protected function hasDefaultTransformer($className)
    {
        return !is_null(config('api.transformers.' . $className));
    }

    /**
     * Get the class name from the given object.
     *
     * @param $object
     *
     * @return string
     * @throws \Exception
     */
    protected function getClassName($object)
    {
        if ($object instanceof LengthAwarePaginator || $object instanceof Collection) {
            return get_class(Arr::first($object));
        }

        if (!is_string($object) && !is_object($object)) {
            throw new \Exception("No transformer of \"{$object}\" found.");
        }

        return get_class($object);
    }
}
