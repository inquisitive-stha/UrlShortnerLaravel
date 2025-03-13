<?php

namespace Modules\Url\Providers;

use Carbon\Laravel\ServiceProvider;
class UrlServiceProvider extends ServiceProvider
{
    public function register()
    {

    }
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/route.php');
    }
}
