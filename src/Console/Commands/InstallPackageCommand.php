<?php


namespace Miladimos\FileManager\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class InstallPackageCommand extends Command
{
    protected $signature = 'filemanager:install';

    protected $description = 'Install the FileManager Package';

    public function handle()
    {
        $this->line("\t... Welcome To File Manager Package Installer ...");

        $uses = $this->choice('What type do you use this package?', ['api', 'web']);

        if ($uses == 'web') {
            $this->info('web');
            Config::set('file_manager.uses', 'web');
        } else if ($uses == 'api') {
            $this->info('api');
            Config::set('file_manager.uses', 'api');
        }


        $this->warn(Config::get('file_manager.uses'));

        //        $this->info('Installing FileManager Package Started...');

        //        $this->info('Publishing configuration...');


        $this->info("FileManager Package Successfully Installed.\n");
        $this->info("\t\tGood Luck.");
    }

    //       //config
    //       if (File::exists(config_path('filemanager.php'))) {
    //         $confirm = $this->confirm("filemanager.php already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishConfig();
    //             $this->info("config overwrite finished");
    //         } else {
    //             $this->info("skipped config publish");
    //         }
    //     } else {
    //         $this->publishConfig();
    //         $this->info("config published");
    //     }

    //     //assets
    //     if (File::exists(public_path('filemanager'))) {
    //         $confirm = $this->confirm("filemanager directory already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishAssets();
    //             $this->info("assets overwrite finished");
    //         } else {
    //             $this->info("skipped assets publish");
    //         }
    //     } else {
    //         $this->publishAssets();
    //         $this->info("assets published");
    //     }

    //     //migration
    //     if (File::exists(database_path("migrations/$migrationFile"))) {
    //         $confirm = $this->confirm("migration file already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishMigration();
    //             $this->info("migration overwrite finished");
    //         } else {
    //             $this->info("skipped migration publish");
    //         }
    //     } else {
    //         $this->publishMigration();
    //         $this->info("migration published");
    //     }

    //     $this->call('migrate');
    // }

    // private function publishConfig()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
    //         '--tag'      => 'config',
    //         '--force'    => true
    //     ]);
    // }

    // private function publishMigration()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
    //         '--tag'      => 'migrations',
    //         '--force'    => true
    //     ]);
    // }

    // private function publishAssets()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
    //         '--tag'      => 'assets',
    //         '--force'    => true
    //     ]);
    // }

}
