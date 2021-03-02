<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
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
        $lang=$request->server('HTTP_ACCEPT_LANGUAGE');
        if(strpos($lang,"de")===0){
            app()->setLocale("de");
        }
        else{
            app()->setLocale("en");

        }

        if(isset($_GET['language']) && strpos($_GET['language'],"en")===0) {
            app()->setLocale("en");
        }

        return $next($request);
    }
}
