<?php

namespace Miladimos\Package\Providers;

use Illuminate\Support\ServiceProvider;


class PackageServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/config.php", 'file-manager');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {

            $this->registerPublishes();

        }
    }

    private function registerPublishes()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('file-manager.php')
        ], 'package-config');

    }
}
