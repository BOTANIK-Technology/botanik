<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ConnectService extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'dbConnect';
    }

}