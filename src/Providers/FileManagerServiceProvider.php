<?php

namespace Miladimos\FileManager\Providers;

use Illuminate\Support\ServiceProvider;
use Miladimos\FileManager;


class FileManagerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/config.php", 'file-manager');

        $this->app->bind('filemanager', function($app) {
            return new FileManager();
        });

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
        ], 'file-manager-config');

    }
}
