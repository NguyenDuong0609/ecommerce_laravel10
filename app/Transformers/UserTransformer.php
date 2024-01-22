<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * Transform the User entity.
     *
     * @param \App\Entities\User $model
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
        return [
            "id" => $data->id,
            "name" => $data->name,
            "email" => $data->email,
        ];
    }
}
