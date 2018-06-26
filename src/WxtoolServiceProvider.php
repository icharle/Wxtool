<?php

namespace Icharle\Wxtool;

use Illuminate\Support\ServiceProvider;

class WxtoolServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/wxtool.php' => config_path('wxtool.php')
        ], 'wxtool');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('wxtool', function () {
            return new Wxtool();
        });

        $this->app->alias('wxtool', 'Icharle\Wxtool\Wxtool');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['wxtool'];
    }
}