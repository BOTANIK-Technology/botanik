<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard == 'web' && $business = $request->route()->parameter('business'))
                    return redirect('/'.$business.RouteServiceProvider::HOME);

                elseif ($guard == 'root' && $request->route()->getPrefix() == '/a-level')
                    return redirect('/a-level'.RouteServiceProvider::ROOT);

//                dd($guard, $request->route()->getPrefix());
                abort(404);
            }
        }

        return $next($request);
    }
}
