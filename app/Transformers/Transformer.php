<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
// use App\Entities\Transformer;

/**
 * Class TransformerTransformer.
 *
 * @package namespace App\Transformers;
 */
class Transformer extends TransformerAbstract
{
    /**
     * Transform the Transformer entity.
     *
     * @param \App\Entities\Transformer $model
     *
     * @return array
     */
    public function transform($data)
    {
        // return [
        //     'id'         => (int) $model->id,

        //     /* place your other model properties here */

        //     'created_at' => $model->created_at,
        //     'updated_at' => $model->updated_at
        // ];

        return collect($data)->toArray();
    }
}
