<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Package
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $package1
     * @param string $package2
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $package1, string $package2 = '')
    {
        if ($request->package == $package1)
            return $next($request);
        elseif ($request->package == $package2)
            return $next($request);
        else
            return abort(404);
    }
}
