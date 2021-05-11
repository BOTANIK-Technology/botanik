<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
//use http\Env\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if ($business = $request->route()->parameter('business'))
            $this->redirectTo = '/'.$business.$this->redirectTo;

        elseif (($request->route()->getPrefix()) == '/a-level')
            $this->redirectTo = '/'.'a-level'.RouteServiceProvider::ROOT;

        $this->middleware('guest:web,root')->except('logout');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        if (!$business = $request->route()->parameter('business'))
            if ( ($business = $request->route()->getPrefix()) != '/a-level')
                $business = '/';

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect($business);
    }
}
