<?php

namespace App\Exceptions\Api;

class LoginException extends ApiException
{
    public function __construct($message, $data, $httpStatus)
    {
        parent::__construct($message, $data, $httpStatus);
    }
}
