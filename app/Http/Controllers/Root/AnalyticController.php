<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;

class AnalyticController extends Controller
{
    public function index()
    {
        return view('root.analytic');
    }
}
