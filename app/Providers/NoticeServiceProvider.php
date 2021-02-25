<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \Blade;

class NoticeServiceProvider extends ServiceProvider
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
        Blade::directive('notice', function (){
            return "
<?php
    \$result = false;
    foreach (auth()->user()->notices as \$notice):
        if (!\$notice->seen):
            \$result = true;
            break;
        endif;
        if (\$result) break;
    endforeach;
    if (!\$result):
        if (auth()->user()->hasRole('admin')):
            foreach (auth()->user()->addresses as \$address):
                foreach (\$address->notices as \$notice):
                    if (!\$notice->seen):
                        \$result = true;
                        break;
                    endif;
                endforeach;
                if (\$result) break;
            endforeach;
        elseif (auth()->user()->hasRole('owner')):
            foreach (auth()->user()->roles as \$role):
                foreach (\$role->notices as \$notice):
                    if (!\$notice->seen):
                        \$result = true;
                        break;
                    endif;
                endforeach;
                if (\$result) break;
            endforeach;
        endif; 
    endif;
    if (\$result) echo 'true';
?>
            ";
        });
    }
}
