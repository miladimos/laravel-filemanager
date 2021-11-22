<?php


namespace Miladimos\FileManager\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallPackageCommand extends Command
{
    protected $signature = 'filemanager:install';

    protected $description = 'Install the FileManager package';

    public function handle()
    {
        $this->line("\t... Welcome To File Manager Package Installer ...");

        //config
        if (File::exists(config_path('filemanager.php'))) {
            $confirm = $this->confirm("filemanager.php already exist. Do you want to overwrite?");
            if ($confirm) {
                $this->publishConfig();
            } else {
                $this->error("you must overwrite config file");
                exit;
            }
        } else {
            $this->publishConfig();
        }

        // publish migration
        $this->publishMigration();



        $this->info("FileManager Package Successfully Installed. Star me on Github :) \n");
        $this->info("\t\tGood Luck.");
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
            '--tag' => 'filemanager_config',
            '--force' => true
        ]);
    }

    private function publishMigration()
    {
        $this->call('vendor:publish', [
            '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
            '--tag' => 'filemanager-migrations',
            '--force' => true
        ]);
    }

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

    // private function publishAssets()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
    //         '--tag'      => 'assets',
    //         '--force'    => true
    //     ]);
    // }

}
