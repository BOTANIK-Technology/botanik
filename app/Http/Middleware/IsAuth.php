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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (! $request->expectsJson()) {

            if (!Auth::check()) {

                if ($business = $request->route()->parameter('business'))
                    return redirect()->route('login', $business);

                elseif ($request->route()->getPrefix() == '/a-level')
                    return redirect()->route('login', 'a-level');

                return redirect()->route('login');

            }

        }

        return $next($request);
    }
}
