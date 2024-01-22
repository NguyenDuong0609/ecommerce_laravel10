<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ErrorHandlerTrait;

abstract class ApiException extends Exception
{
    use ErrorHandlerTrait;
    protected $httpStatus;
    protected $data;

    public function __construct($message, $data, $httpStatus = Response::HTTP_BAD_REQUEST)
    {
        $this->data = $data ?? "";
        $this->message = $message;
        $this->httpStatus = $httpStatus;
        parent::__construct($this->message, $this->httpStatus);
    }

    public function getResponse()
    {
        Log::error(
            request()->path()
            . ': '
            . $this->data
            . ': '
            . $this->httpStatus
            . ' -> '
            . $this->message
        );

        return $this->errorResponse($this->message, $this->data, $this->httpStatus);
    }
}
