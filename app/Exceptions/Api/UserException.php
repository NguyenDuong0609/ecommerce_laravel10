<?php

namespace App\Exceptions\Api;

class UserException extends ApiException
{
    public function __construct($message, $data, $httpStatus)
    {
        parent::__construct($message, $data, $httpStatus);
    }
}
