<?php

namespace App\Http\Middleware;

use App\Facades\ConnectService;
use Closure;
use Illuminate\Http\Request;


class SetBusiness
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

        $business = ConnectService::isBusiness($request->route()->parameter('business'));

        if (!$business || !$business->status)
            abort(404);

        $request->merge(
            [
                'token'         => $business->token,
                'business_db'   => $business->db_name,
                'business_name' => $business->name,
                'business_id'   => $business->id,
                'package'       => $business->package->slug,
                'catalog'       => $business->catalog
            ]
        );

        if (!ConnectService::dbConnect($business->db_name))
        {
            abort(501);
        }

        view()->share('logotype', !is_null($business->img) ? url('public/storage/'.$business->img) : '/images/botanik-head.png');
        view()->share('package', $request->package);
        view()->share('slug', $request->route()->parameter('business'));
        view()->share('buss_name', $business->name);
        view()->share('catalog', $business->catalog);

        return $next($request);
    }
}
