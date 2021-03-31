<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param string $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $guard = 'web')
    {

        if (! $request->expectsJson()) {

            if ( !Auth::guard($guard)->check() ) {

                if ($guard == 'web' && $business = $request->route()->parameter('business'))
                    return redirect()->route('login', $business);

                elseif ($guard == 'root' && $request->route()->getPrefix() == '/a-level')
                    return redirect()->route('root.login');

                abort(404);
            }
        }

        return $next($request);
    }
}
