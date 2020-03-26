<?php

namespace App\Modules\Event;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class EventServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(GateContract $gate) {

        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'event');

        //$gate->policy(Video::class, VideoPolicy::class);

        $this->mergeConfigFrom(
            __DIR__.'/module.php', 'event'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}
}