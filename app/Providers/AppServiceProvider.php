<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * This includes IDE Helper Service provider and Redis Service Provider
     *
     * @return void
     */
    public function register()
    {
        if (class_exists('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider'))
        {
            $this->app->register('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
        }

        if (class_exists('Vluzrmos\Tinker\TinkerServiceProvider'))
        {
            $this->app->register('Vluzrmos\Tinker\TinkerServiceProvider');
        }

        /*
         * Register Redis Service provider
         */

        $this->app->register(\Illuminate\Redis\RedisServiceProvider::class);

        /*
         * Register Laravel Log Viewer
         */
        $this->app->register(\Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class);

    }
}
