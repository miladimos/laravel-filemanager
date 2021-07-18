<?php


namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\Storage;

class DirectoryService extends Service
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(config('filemanager.disk'));
    }

    public function createDirectory($data)
    {
        $this->disk->makeDirectory('');
        return true;
    }

    public function deleteDirectory($data)
    {
        $this->disk->deleteDirectory('');
        return true;
    }

    public function renameDirectory($data)
    {
        $this->disk->renameDirectory('');
        return true;
    }
}
