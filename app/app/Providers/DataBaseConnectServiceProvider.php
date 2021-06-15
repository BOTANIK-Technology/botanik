<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DataBaseConnectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('dbConnect','App\Services\DataBaseConnect');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
