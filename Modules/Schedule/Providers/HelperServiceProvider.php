<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 1/22/18
 * Time: 10:50 AM
 */

namespace Modules\Schedule\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path().'Modules/Schedule/Helpers/*.php') as $filename){
            require_once($filename);
        }
    }
}