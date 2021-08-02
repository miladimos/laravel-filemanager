<?php


namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function listDirectories(Directory $directory, $recursive = false)
    {
        if ($recursive) {
            $dirs = collect($this->disk->allDirectories($directory->path));
        }

        $dirs = collect($this->disk->directories($directory->path));

        return $dirs;
    }

    public function listFiles(Directory $directory, $recursive = false)
    {
        if ($recursive) {
            $dirs = collect($this->disk->allFiles($directory->path));
        }

        $dirs = collect($this->disk->files($directory->path));

        return $dirs;
    }

    public function createDirectory(array $data)
    {
        $path = $this->base_directory . $this->ds . $data['name'];

        if (!checkPath($path, $this->disk_name)) {
            if ($this->disk->makeDirectory($path)) {
                DB::transaction(function () use ($data, $path) {
                    $this->model->create([
//                'user_id' => user()->id,
                        'name' => $data['name'],
                        'description' => $data['description'] ?? '',
                        'path' => $path,
                        'parent_id' => $data['parent_id'] ?? 0,
                        'disk' => $this->disk_name,
                    ]);
                });
                return true;
            } else {

//                $this->error('Directory "' . $directory . '" already exists.');
//                $this->error('Can not create directory.');
                return false;
            }
        }

        return false;
    }

    public function renameDirectory(Directory $directory, $newName)
    {
        if ($directory->name == $newName) return false;


        $path = $this->base_directory . $this->ds . $directory->name;

        if ($this->disk->exists($path)) {
            DB::transaction(function () use ($directory, $newName) {
                $directory->update([
                    'name' => $newName
                ]);
            });

            if ($this->disk->move($directory->name, $newName)) return true;
        };
        return false;
    }

    public function deleteDirectory(Directory $directory)
    {
        $path = $directory->path;

        if (!checkPath($path, $this->disk_name)) {
            return false; // directory does not exists
        }

        $directoryFiles = array_merge($this->disk->directories($path), $this->disk->files($path));
        if ($directoryFiles)
            return false; // directory is not empty

        if ($this->disk->deleteDirectory($path)) {
            DB::transaction(function () use ($directory) {
                $directory->forceDelete();
            });
            return true;
        }

        return false;
    }

    /**
     * Return files and directories within a directory.
     *
     * @param string $directory
     *
     * @return array
     */
    public function directoryInfo(Directory $directory)
    {
        // Get the names of the sub directorys within this directory
        $subFolders = collect($this->disk->directories($directory->path))->reduce(function ($subFolders, $subFolder) {
            if (!$this->isItemHidden($subFolder)) {
                $subFolders[] = $this->directoryDetails($subFolder);
            }

            return $subFolders;
        }, collect([]));

        // Get all files within this directory
        $files = collect($this->disk->files($directory->path))->reduce(function ($files, $path) {
            if (!$this->isItemHidden($path)) {
                $files[] = $this->fileDetails($path);
            }

            return $files;
        }, collect([]));

        $itemsCount = $subFolders->count() + $files->count();

        return compact('directory', 'subFolders', 'files', 'itemsCount');
    }

    /**
     * Return an array of directory details for a given directory.
     *
     * @param $path
     *
     * @return array
     */
    public function directoryDetails($path)
    {
        $path = '/' . ltrim($path, '/');

        return [
            'name' => basename($path),
            'mime_type' => 'directory',
            'disk_path' => $path,
            'modified' => $this->lastModified($path),
        ];
    }

    /**
     * Return an array of file details for a given file.
     *
     * @param $path
     *
     * @return array
     */
    public function fileDetails($path)
    {
        $path = '/' . ltrim($path, '/');

        return [
            'original_name' => $this->getOriginalNameFromPath($path),
            'name' => basename($path),
            'disk_path' => $path,
            'url_path' => $this->disk->url($path),
            'extension' => $this->getExtention($path),
            'mime_type' => $this->getMime($path),
            'size' => $this->disk->size($path),
            'human_size' => $this->getHumanReadableSize($this->disk->size($path)),
            'modified' => $this->lastModified($path),
        ];
    }
}
