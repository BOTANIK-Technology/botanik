<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\ConnectService;
use Illuminate\Support\Facades\Log;

class TelegramCheckReq
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

        $business = ConnectService::isBusiness($request->route()->parameter('slug'));

        if (!$business || !$business->status)
            abort(404);

        $request->merge(
            [
                'token'       => $business->token,
                'bot_name'    => $business->bot_name,
                'pay_token'   => $business->pay_token,
                'business_db' => $business->db_name,
                'package'     => $business->package->slug,
                'catalog'     => $business->catalog
            ]
        );

        if (!ConnectService::dbConnect($business->db_name))
            abort(501);

        return $next($request);
    }
}
