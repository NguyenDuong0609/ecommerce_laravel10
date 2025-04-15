<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use App\Traits\ErrorHandlerTrait;
use Illuminate\Session\TokenMismatchException as SessionTokenMismatchException;

class CustomToken
{
    /**
     * Handle an incoming request.
     *
     * If haven't bearerToken Request, get token in cookie
     * If haven't token in cookie, return error token mismatch
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cookie_name = 'token';
        if ($request->bearerToken() == null) {
            if ($request->hasCookie($cookie_name)) {
                $token = $request->cookie($cookie_name);
                $request->headers->add([
                    'Authorization' => 'Bearer ' . $token
                ]);
            } else {
                throw new SessionTokenMismatchException;
            }
        }
        return $next($request);
    }
}
