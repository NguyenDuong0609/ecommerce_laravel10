<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\ErrorHandlerTrait;
use Illuminate\Session\TokenMismatchException as SessionTokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    use ErrorHandlerTrait;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->errorResponse(config('messages.LOGIN.WRONG_PASSWORD_OR_USERNAME'), "", Response::HTTP_NOT_FOUND);
        }
        // error input token miss
        if($e instanceof SessionTokenMismatchException)
        {
            return $this->errorResponse(config('messages.EXCEPTION.TOKEN_MIS_MATCH'), "", Response::HTTP_UNAUTHORIZED);
        }

        return parent::render($request, $e);
    }
    /**
     * [unauthenticated description]
     * token invalid
     *
     * @param   [type]                   $request    [$request description]
     * @param   AuthenticationException  $exception  [$exception description]
     *
     * @return  [type]                               [return description]
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse(config('messages.EXCEPTION.TOKEN_IN_VALID'), "", Response::HTTP_UNAUTHORIZED);
    }
}
