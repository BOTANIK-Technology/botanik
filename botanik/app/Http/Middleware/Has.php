<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Has
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param mixed $params
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ... $params)
    {
        foreach ($params as $param) {
            if ($request->has($param) && $request->$param == true) {
                return $next($request);
            }
        }

        abort(404);
    }
}
