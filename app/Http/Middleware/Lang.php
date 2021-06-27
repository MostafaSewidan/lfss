<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Lang
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(in_array($request->header('Accept-Language') , ['en','ar']))
            app()->setLocale($request->header('Accept-Language'));
        else
            app()->setLocale('en');

        return $next($request);

    }
}
