<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($business = $request->route()->parameter('business'))
                    return redirect('/'.$business.RouteServiceProvider::HOME);

                elseif (($request->route()->getPrefix()) == '/a-level')
                    return redirect('/'.'a-level'.RouteServiceProvider::ROOT);

                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}