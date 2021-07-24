<?php


namespace Miladimos\FileManager\Services;


use Miladimos\FileManager\Models\Directory;

// all of about directories
class DirectoryService extends Service
{

    // Directory Model
    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Directory();
    }

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
//        $dir = $this->model->create([
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
                $this->model->where('id', $directory)->delete();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
