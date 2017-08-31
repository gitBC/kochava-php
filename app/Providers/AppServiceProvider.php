<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This includes IDE Helper Service provider and Redis Service Provider
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        /*
         * Register Redis Service provider
         */

        $this->app->register(\Illuminate\Redis\RedisServiceProvider::class);
    }
}
