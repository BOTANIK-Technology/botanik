<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \Blade;

class IsMobileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $agent = new \Jenssegers\Agent\Agent;

        view()->share('phone', $agent->isPhone());
        view()->share('desktop', $agent->isDesktop());

        Blade::directive('phone', function (){
            return "<?php if (\$phone): ?>";
        });

        Blade::directive('endphone', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('desktop', function (){
            return "<?php if (\$desktop): ?>";
        });

        Blade::directive('enddesktop', function () {
            return "<?php endif; ?>";
        });

//        Blade::directive('phone', function (){
/*            return "<?php \$agent = new \Jenssegers\Agent\Agent; \$agent->setUserAgent('Mozilla/5.0 (Linux; Android 4.0.4; Desire HD Build/IMM76D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19'); if (\$agent->isPhone()): ?>";*/
//        });
//
//        Blade::directive('endphone', function () {
/*            return "<?php endif; ?>";*/
//        });
//
//        Blade::directive('desktop', function (){
/*            return "<?php \$agent = new \Jenssegers\Agent\Agent; if (false): ?>";*/
//        });
//
//        Blade::directive('enddesktop', function () {
/*            return "<?php endif; ?>";*/
//        });
    }
}