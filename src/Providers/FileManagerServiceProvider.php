<?php

namespace Miladimos\FileManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Miladimos\FileManager\Console\Commands\InstallPackageCommand;
use Miladimos\FileManager\FileManager;


class FileManagerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/config.php", 'file_manager');

        $this->registerFacades();

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->registerConfig();
            // $this->registerPublishesMigrations();
            $this->registerCommands();
            // $this->registerTranslations();
            $this->registerRoutes();
        }
    }

    private function registerFacades()
    {
        $this->app->bind('file-manager', function ($app) {
            return new FileManager();
        });
    }

    private function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'courier');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/courier'),
        ]);
    }

    private function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('file_manager.php')
        ], 'file-manager-config');
    }

    private function registerCommands()
    {
        $this->commands([
            InstallPackageCommand::class,
        ]);
    }

    private function registerPublishesMigrations()
    {

        if (!class_exists('CreateFilesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_files_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_files_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
        if (!class_exists('CreateFileGroupsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_file_groups_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_file_groups_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
        if (!class_exists('CreateFileGroupPivotTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_file_group_pivot_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_file_group_pivot_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
        if (!class_exists('CreateDirectoriesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_directories_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_directories_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
    }
    private function registerRoutes()
    {
        if (config('file_manager.uses') == 'web') {
            Route::group($this->routeConfiguration('web'), function () {
                $this->loadRoutesFrom(__DIR__ . '\..\..\routes\web.php', 'filemanager-routes');
            });
        } else  if (config('file_manager.uses') == 'api') {
            Route::group($this->routeConfiguration('api'), function () {
                $this->loadRoutesFrom(__DIR__ . '\..\..\routes\filemanger-api.php', 'filemanager-routes');
            });
        }
    }

    private function routeConfiguration($uses = 'api')
    {
        if ($uses == 'api') {
            return [
                'prefix' => config('file-manager.routes.api.api_prefix') . '/' . config('file-manager.routes.api.api_version') . '/' . config('file-manager.routes.prefix'),
                'middleware' => config('file-manager.routes.api.middleware'),
            ];
        } else if ($uses == 'web') {
            return [
                'prefix' => config('file-manager.routes.prefix'),
                'middleware' => config('file-manager.routes.web.middleware'),
            ];
        }

        return [];
    }
}
