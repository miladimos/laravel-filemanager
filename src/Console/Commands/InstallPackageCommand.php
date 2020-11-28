<?php


namespace Miladimos\FileManager\Console\Commands;


use Illuminate\Console\Command;

class InstallPackageCommand extends Command
{
    protected $signature = 'filemanager:install';

    protected $description = 'Install the FileManager Package';

    public function handle()
    {
        $this->info('Installing FileManager Package Started...');

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Miladimos\FileManager\Providers\FileManagerServiceProvider",
            '--tag' => "config"
        ]);

        $this->info('Installed BlogPackage');
    }
}
