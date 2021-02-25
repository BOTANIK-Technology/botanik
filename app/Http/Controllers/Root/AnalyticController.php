<?php

namespace App\Http\Controllers\Root;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticController extends Controller
{
    public function index()
    {
        return view('root.analytic');
    }

    public function collect (Request $request)
    {
        if ( !$request->has('gess_key') || ( $request->input('gess_key') !== env('APP_KEY') ) )
            abort('404');

        //$request->route()->getActionName()
    }
}
