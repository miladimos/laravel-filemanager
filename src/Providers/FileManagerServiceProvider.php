<?php

namespace Miladimos\FileManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Miladimos\FileManager\Console\Commands\InitializePackageCommand;
use Miladimos\FileManager\Console\Commands\InstallPackageCommand;
use Miladimos\FileManager\FileManager;

class FileManagerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/filemanager.php", 'filemanager');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->registerFacades();

    }

    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->registerConfig();
//            $this->registerPublishesMigrations();
            $this->registerCommands();
            $this->registerTranslations();
            $this->registerRoutes();
        }
    }

    private function registerFacades()
    {
        $this->app->bind('filemanager', function ($app) {
            return new FileManager();
        });
    }

    private function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/filemanager.php' => config_path('filemanager.php')
        ], 'filemanager_config');
    }

    private function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filemanager');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/miladimos/laravel-filemanager'),
        ]);
    }

    private function registerCommands()
    {
        $this->commands([
            InstallPackageCommand::class,
            InitializePackageCommand::class,
        ]);
    }

    private function registerPublishesMigrations()
    {
//        if (!class_exists('CreateFilemanagerTables')) {
        $this->publishes([
            __DIR__ . '/../../database/migrations/2021_07_25_232905_create_filemanager_tables.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_filemanger_tables.php'),
            // you can add any number of migrations here
        ], 'migrations');
//        }
    }

    private function registerRoutes()
    {
        Route::group($this->routeConfiguration('api'), function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/filemanger-api.php', 'filemanager-routes');
        });
    }

    private function routeConfiguration()
    {
        $filemanager_api_version = 'v1';

        return [
            'prefix' => config('filemanager.routes.api.api_prefix') . '/' . $filemanager_api_version . '/' . config('filemanager.routes.prefix'),
            'middleware' => config('filemanager.routes.api.middleware'),
            'as' => 'filemanager.'
        ];
    }
}
