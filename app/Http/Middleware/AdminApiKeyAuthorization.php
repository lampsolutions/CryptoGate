<?php

namespace App\Http\Middleware;

use Closure;

class AdminApiKeyAuthorization
{
    const AUTH_PARAM = 'api_key';

    /**
     * Authorize incoming api request
     * @param  \Closure  $next
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key = $request->get(self::AUTH_PARAM);
        $token_merchant = env('API_TOKEN_ADMIN');

        if(empty($token_merchant) || $key !== $token_merchant) {
            return response('Unauthorized.', 401);
        }



        return $next($request);
    }
}
