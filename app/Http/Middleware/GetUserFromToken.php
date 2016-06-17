<?php

namespace App\Http\Middleware;

use Closure;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Middleware\BaseMiddleWare;

class GetUserFromToken extends BaseMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            // If token doesn't exist in HTTP Header or URL parameter
            throw new JWTException('token_not_provided', 400);
        }

        if (! $user = $this->auth->authenticate($token)) {
            // Login with token and if user doesn't exist
            throw new JWTException('user_not_found', 404);

            
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
