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

    /**
     * Show all directories that the selected item can be moved to.
     *
     * @return array
     */
    public function allDirectories()
    {
        $directories = $this->disk->allDirectories('/');

        return collect($directories)->filter(function ($directory) {
            return !(Str::startsWith($directory, '.'));
        })->map(function ($directory) {
            return DIRECTORY_SEPARATOR . $directory;
        })->reduce(function ($allDirectories, $directory) {
            $parts = explode('/', $directory);
            $name = str_repeat('&nbsp;', (count($parts)) * 4) . basename($directory);

            $allDirectories[$directory] = $name;

            return $allDirectories;
        }, collect())->prepend($this->breadcrumbRootLabel, '/');
    }

    public function listDirectories($path)
    {
        $dirs = collect($this->disk->directories($this->base_directory . $this->ds . $path));

        return $dirs;
    }

    public function listDirectoriesRecursive($directory)
    {
        $dirs = $this->disk->allDirectories($directory);

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

    public function renameDirectory($uuid, $newName)
    {
        $directory = $this->model->where('uuid', $uuid)->first();

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

    public function deleteDirectory($uuid)
    {
        $directory = $this->model->where('uuid', $uuid)->first();

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
     * Return files and directories within a folder.
     *
     * @param string $folder
     *
     * @return array of [
     *               'folder' => 'path to current folder',
     *               'folderName' => 'name of just current folder',
     *               'breadCrumbs' => breadcrumb array of [ $path => $foldername ],
     *               'subfolders' => array of [ $path => $foldername] of each subfolder,
     *               'files' => array of file details on each file in folder,
     *               'itemsCount' => a combined count of the files and folders within the current folder
     *               ]
     */
    public function folderInfo($folder = '/')
    {
        // Get the names of the sub folders within this folder
        $subFolders = collect($this->disk->directories($folder))->reduce(function ($subFolders, $subFolder) {
            if (!$this->isItemHidden($subFolder)) {
                $subFolders[] = $this->folderDetails($subFolder);
            }

            return $subFolders;
        }, collect([]));

        // Get all files within this folder
        $files = collect($this->disk->files($folder))->reduce(function ($files, $path) {
            if (!$this->isItemHidden($path)) {
                $files[] = $this->fileDetails($path);
            }

            return $files;
        }, collect([]));

        $itemsCount = $subFolders->count() + $files->count();

        return compact('folder', 'subFolders', 'files', 'itemsCount');
    }

    /**
     * Return an array of folder details for a given folder.
     *
     * @param $path
     *
     * @return array
     */
    public function folderDetails($path)
    {
        $path = '/' . ltrim($path, '/');

        return [
            'name' => basename($path),
            'mimeType' => 'folder',
            'fullPath' => $path,
            'modified' => $this->fileModified($path),
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
            'name' => basename($path),
            'fullPath' => $path,
            'webPath' => $this->fileWebpath($path),
            'mimeType' => $this->fileMimeType($path),
            'size' => $this->fileSize($path),
            'modified' => $this->fileModified($path),
            'relativePath' => $this->fileRelativePath($path),
        ];
    }
}
