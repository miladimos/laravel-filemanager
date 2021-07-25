<?php


namespace Miladimos\FileManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InitializePackageCommand extends Command
{
    protected $signature = 'filemanager:init';

    protected $description = 'initialize the FileManager package';

    private $disk;

    private $base_directory;

    public function __construct()
    {
        parent::__construct();

        $this->disk = Storage::disk(config('filemanager.disk'));
        $this->base_directory = config('filemanager.base_directory');

    }

    public function handle()
    {
        $this->line("\n... Initialize package ...\n\n");

        if (!$this->disk->exists($this->base_directory)) {
            $this->disk->makeDirectory($this->base_directory);
        }

        $this->line("Initialized ... \n");

        $this->warn("enjoy it, star me of github :) \n");
        $this->info("\t\tGood Luck.");
    }

}
