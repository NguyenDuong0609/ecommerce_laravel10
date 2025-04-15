<?php

namespace App\Exceptions\Api;

class CategoryException extends ApiException
{
    public function __construct($message, $data, $httpStatus)
    {
        parent::__construct($message, $data, $httpStatus);
    }
}
