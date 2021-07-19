<?php


namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\Storage;
use Miladimos\FileManager\Models\Directory;

class DirectoryService extends Service
{
    public function listDirectories($path)
    {
        //
    }

    public function listDirectoriesInDirectory($directory)
    {
        //
    }

    public function createDirectory($directory)
    {
        $dir = Directory::create([
            'user_id' => user()->id,

        ]);
        $this->disk->makeDirectory($directory);
        return true;
    }

    public function deleteDirectory($directory)
    {
        try {
            if ($this->disk->deleteDirectory($directory))
                return "true";
        } catch (\Exception $exception) {
            return "false";
        }
    }

    public function renameDirectory($name, $newName)
    {
        if (!$this->disk->exists($name)) return "false";

        if ($this->disk->move($name, $newName)) return "true";

    }
}
