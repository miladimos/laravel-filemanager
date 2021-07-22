<?php


namespace Miladimos\FileManager\Services;


class DirectoryService extends Service
{
    public function listDirectories($path)
    {
        $dirs = $this->disk->directories($path);

        return $dirs;
    }

    public function listDirectoriesRecursive($directory)
    {
        $dirs = $this->disk->allDirectories($directory);

        return $dirs;
    }

    public function createDirectory($directory)
    {
//        $dir = Directory::create([
//            'user_id' => user()->id,
//
//        ]);

        $this->disk->makeDirectory($directory);
        return true;
    }

    public function deleteDirectory($directory)
    {
        try {
            if ($this->disk->deleteDirectory($directory))
                return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
