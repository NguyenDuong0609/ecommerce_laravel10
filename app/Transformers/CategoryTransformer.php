<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            "id" => $data->id,
            "name" => $data->name,
            "parent_id" => $data->parent_id,
            "slug" => $data->slug,
        ];
    }
}
