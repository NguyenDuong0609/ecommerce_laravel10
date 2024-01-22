<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
// use App\Entities\Transformers\AccessTokenTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class AccessTokenTransformer.
 *
 * @package namespace App\Transformers;
 */
class AccessTokenTransformer extends TransformerAbstract
{
    /**
     * Transform the AccessTokenTransformer entity.
     *
     * @param \App\Entities\Transformers\AccessTokenTransformer $model
     *
     * @return array
     */
    public function transform($authedUser)
    {
        return [
            'user'   => $authedUser['user'],
            'access_token' => $authedUser['token']->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $authedUser['token']->token->expires_at
            )->toDateTimeString()
        ];
    }
}
