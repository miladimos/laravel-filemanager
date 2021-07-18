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

    }

    public function deleteDirectory($data)
    {
        //
    }

    public function renameDirectory($data)
    {
        //
    }

    public function moveDirectory($data)
    {
        //
    }
}
