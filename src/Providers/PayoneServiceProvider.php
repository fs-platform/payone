<?php

namespace Smbear\Payone\Providers;

use Smbear\Payone\Payone;
use Smbear\Payone\Enums\PayoneEnums;
use Illuminate\Support\ServiceProvider;

class PayoneServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/payone.php' => config_path('payone.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PayoneEnums::CONFIG,function (){
            return new Payone();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/payone.php', PayoneEnums::CONFIG
        );
    }
}
