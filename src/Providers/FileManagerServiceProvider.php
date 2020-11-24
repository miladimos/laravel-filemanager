<?php

namespace Miladimos\FileManager\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Miladimos\FileManager\FileManager;


class FileManagerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/config.php", 'file-manager');

        $this->app->bind('file-manager', function($app) {
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
            $this->registerConfig();
            $this->registerPublishesMigrations();
        }

        if(config('file-manager.uses') == 'api') {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }



//        if (config('lfm.use_package_routes')) {
//            Route::group(['prefix' => 'filemanager', 'middleware' => ['web', 'auth']], function () {
//                \UniSharp\LaravelFilemanager\Lfm::routes();
//            });
//        }
    }

    private function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('file_manager.php')
        ], 'file-manager-config');
    }

    private function registerPublishesMigrations() {

        if (! class_exists('CreateFilesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_files_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_files_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
        if (! class_exists('CreateFileGroupsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_file_groups_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_file_groups_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
        if (! class_exists('CreateFileGroupPivotTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_file_group_pivot_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_file_group_pivot_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
        if (! class_exists('CreateDirectoriesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_directories_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_directories_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }
    }
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/filemanager-api.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('file-manager.api_prefix') . config('file-manager.api_version') . config('file-manager.prefix'),
            'middleware' => config('file-manager.middleware'),
        ];
    }
}
