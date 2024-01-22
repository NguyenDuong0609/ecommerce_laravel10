<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ErrorHandlerTrait
{
    public function errorResponse(string $message, $data ,int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $result = [
            "message" => $message,
            "success" => false,
            "data" => $data,
            "statusCode" => $statusCode,
        ];
        return response()->json($result,$statusCode);
    }
}