<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role_1
     * @param null $role_2
     * @param null $role_3
     * @param string $operator
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role_1, $role_2 = null, $role_3 = null, $operator = 'OR')
    {
        if ($operator == 'OR') {
            if (auth()->user()->hasRole($role_1))
                return $next($request);
            if (!is_null($role_2) && auth()->user()->hasRole($role_2))
                return $next($request);
            if (!is_null($role_3) && auth()->user()->hasRole($role_3))
                return $next($request);
        } else {
            if (
                !is_null($role_2) &&
                !is_null($role_3) &&
                auth()->user()->hasRole($role_1, $role_2, $role_3)
            )
                return $next($request);
            if (
                !is_null($role_2) &&
                 is_null($role_3) &&
                auth()->user()->hasRole($role_1, $role_2)
            )
                return $next($request);
            if (
                 is_null($role_2) &&
                !is_null($role_3) &&
                auth()->user()->hasRole($role_1, $role_3)
            )
                return $next($request);

        }
        return abort(404);
    }
}
