<?php

namespace Tequilarapido\Consolify;

use Illuminate\Support\ServiceProvider;

class ConsolifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/consolify.php' => config_path('consolify.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/consolify.php', 'consolify');

        // ...
    }
}
