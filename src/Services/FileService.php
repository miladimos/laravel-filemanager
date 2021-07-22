<?php

namespace Miladimos\FileManager\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class FileService extends Service
{

    protected $access;

    public function __construct()
    {
        $this->access = config('media-manager.access');
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
        $folder = $this->cleanFolder($folder);
        $breadCrumbs = $this->breadcrumbs($folder);
        $folderName = $breadCrumbs->pop();

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

        return compact('folder', 'folderName', 'breadCrumbs', 'subFolders', 'files', 'itemsCount');
    }

    /**
     * Return breadcrumbs to current folder.
     *
     * @param $folder
     *
     * @return Collection
     */
    protected function breadcrumbs($folder)
    {
        $folder = trim($folder, '/');
        $folders = collect(explode('/', $folder));
        $path = '';

        return $folders->reduce(function ($crumbs, $folder) use (&$path) {
            $path .= '/' . $folder;
            $crumbs[$path] = $folder;

            return $crumbs;
        }, collect())->prepend($this->breadcrumbRootLabel, '/');
    }

    /**
     * Return an array of folder details for a given folder.
     *
     * @param $path
     *
     * @return array
     */
    protected function folderDetails($path)
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
    protected function fileDetails($path)
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

    /**
     * Return the mime type.
     *
     * @param $path
     *
     * @return string
     */
    public function fileMimeType($path)
    {
        $type = $this->mimeDetect->findType(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
        if (!empty($type)) {
            return $type;
        }

        return 'unknown/type';
    }

    /**
     * Return the file size.
     *
     * @param $path
     *
     * @return int
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * Return the last modified time. If a timestamp can not be found fall back
     * to today's date and time...
     *
     * @param $path
     *
     * @return Carbon
     */
    public function fileModified($path)
    {
        try {
            return Carbon::createFromTimestamp($this->disk->lastModified($path));
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    /**
     * Create a new directory.
     *
     * @param $folder
     *
     * @return bool
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);
        if ($this->disk->exists($folder)) {
            $this->errors[] = 'Folder "' . $folder . '" already exists.';

            return false;
        }

        return $this->disk->makeDirectory($folder);
    }

    /**
     * Delete a directory.
     *
     * @param $folder
     *
     * @return bool
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);
        $filesFolders = array_merge($this->disk->directories($folder), $this->disk->files($folder));
        if (!empty($filesFolders)) {
            $this->errors[] = 'The directory must be empty to delete it.';

            return false;
        }

        return $this->disk->deleteDirectory($folder);
    }

    /**
     * Delete a file.
     *
     * @param $path
     *
     * @return bool
     */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);
        if (!$this->disk->exists($path)) {
            $this->errors[] = 'File does not exist.';

            return false;
        }

        return $this->disk->delete($path);
    }

    /**
     * @param $path
     * @param $originalFileName
     * @param $newFileName
     *
     * @return bool
     */
    public function rename($path, $originalFileName, $newFileName)
    {
        $path = $this->cleanFolder($path);
        $nameName = $path . DIRECTORY_SEPARATOR . $newFileName;
        if ($this->disk->exists($nameName)) {
            $this->errors[] = 'The file "' . $newFileName . '" already exists in this folder.';

            return false;
        }

        return $this->disk->getDriver()->rename(($path . DIRECTORY_SEPARATOR . $originalFileName), $nameName);
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

    /**
     * @param $currentFile
     * @param $newFile
     *
     * @return bool
     */
    public function moveFile($currentFile, $newFile)
    {
        if ($this->disk->exists($newFile)) {
            $this->errors[] = 'File already exists.';

            return false;
        }

        return $this->disk->getDriver()->rename($currentFile, $newFile);
    }

    /**
     * @param $currentFolder
     * @param $newFolder
     *
     * @return bool
     */
    public function moveFolder($currentFolder, $newFolder)
    {
        if ($newFolder == $currentFolder) {
            $this->errors[] = 'Please select another folder to move this folder into.';

            return false;
        }

        if (Str::startsWith($newFolder, $currentFolder)) {
            $this->errors[] = 'You can not move this folder inside of itself.';

            return false;
        }

        return $this->disk->getDriver()->rename($currentFolder, $newFolder);
    }

    /**
     * Return the full web path to a file.
     *
     * @param $path
     *
     * @return string
     */
    public function fileWebpath($path)
    {
        $path = $this->disk->url($path);
        // Remove extra slashes from URL without removing first two slashes after http/https:...
        $path = preg_replace('/([^:])(\/{2,})/', '$1/', $path);

        return $path;
    }

    /**
     * @param $path
     *
     * @return string
     */
    private function fileRelativePath($path)
    {
        $path = $this->fileWebpath($path);
        // @todo This wont work for files not located on the current server...
        $path = Str::replaceFirst(env('APP_URL'), '', $path);
        $path = str_replace(' ', '%20', $path);

        return $path;
    }

    /**
     * This method will take a collection of files that have been
     * uploaded during a request and then save those files to
     * the given path.
     *
     * @param UploadedFilesInterface $files
     * @param string $path
     *
     * @return int
     */
    public function saveUploadedFiles(UploadedFilesInterface $files, $path = '/')
    {
        return $files->getUploadedFiles()->reduce(function ($uploaded, UploadedFile $file) use ($path) {
            $fileName = $file->getClientOriginalName();
            if ($this->disk->exists($path . $fileName)) {
                $this->errors[] = 'File ' . $path . $fileName . ' already exists in this folder.';

                return $uploaded;
            }

            if (!$file->storeAs($path, $fileName, [
                'disk' => $this->diskName,
                'visibility' => $this->access,
            ])) {
                $this->errors[] = trans('media-manager::messages.upload_error', ['entity' => $fileName]);

                return $uploaded;
            }
            $uploaded++;

            return $uploaded;
        }, 0);
    }

    /**
     * Work out if an item (file or folder) is hidden (begins with a ".").
     *
     * @param $item
     *
     * @return bool
     */
    private function isItemHidden($item)
    {
        return Str::startsWith(last(explode(DIRECTORY_SEPARATOR, $item)), '.');
    }


    /**
     * first get passed file and fetch name and format from original name
     * and if not set these when set
     * then return handle method
     *
     * @param $file
     * @return mixed
     */
    public function upload($file)
    {
        $nameSplit = explode('.', $file->getClientOriginalName());
        $fileName = $nameSplit[0];
        $format = $nameSplit[1];
        if (!$this->getName()) {
            if ($this->useFileNameToUpload) {
                $this->setName($fileName);
            } else {
                $this->setName($this->generateRandomName());
            }
        }

        if (is_null($this->getFormat())) $this->setFormat($format);

        return $this->handle($file);
    }


    /**
     * create a new file row in db
     *
     * @param null $name
     * @return mixed
     */
    protected function createFileRow($name = null)
    {
        $file = File::create([
            "name" => $name ?? $this->getName() ?? $this->generateRandomName(),
            "file_name" => $this->getFileName(),
            "type" => $this->getType(),
            "base_path" => $this->getUploadPath(),
            "format" => $this->getFormat(),
            "private" => $this->public ? false : true,
        ]);
        $this->setFile($file);
        return $file;
    }


    /**
     * if has $this->file and is null name return this
     * else get file by name
     *
     * @param null $name
     * @return Builder|Model|\Illuminate\Http\File|File
     * @throws InternalErrorException
     */
    public function getFile($name = null)
    {
        if (is_null($name) && !is_null($this->file) && $this->file instanceof File) return $this->file;

        if (is_null($name)) throw new InternalErrorException("file name not valid!");

        return File::query()
            ->where("name", $name)
            ->first();
    }


    /**
     * set $this->file
     *
     * @param File $file
     * @return $this
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        return $this;
    }


    public function delete($filename = null)
    {
        /** @var File $file */
        $file = $this->getFile($filename);

        if ($file) {
            $flag = $this->handleDelete($file);
            $file->delete();
        }

        return $flag ?? true;
    }

    /**********     Getters & Setters    **********/

    /**
     * set config
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * get config
     * if pass name:
     *  check exists that
     *  if exists then return the config
     *  else if not exists name return false
     *
     * else or not pass name:
     *  return the all configs
     *
     * @param null $name
     * @return array|bool|mixed
     */
    public function getConfig($name = null)
    {
        $config = $this->config;
        if (is_null($name)) return $config;
        $find = $config[$name];
        if (!isset($find)) return false;
        return $find;
    }


    /**
     * set type
     * this type is one of types in filemanager.php (module config)
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    /**
     * get the current type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * set upload path
     *
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


    /**
     * get upload path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * get full path
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->getStorageFolder($this->getUploadPath());
    }


    /**
     * get upload location path
     * we append the prefix
     * if the dateTimePrefix property is true also append this {$year}/{$month}/{$day}/
     *
     * @return string
     */
    public function getUploadPath()
    {
        $path = $this->getPath();
        $prefix = $this->getPrefix() ?? "";

        /** Check and set dateTimePrefix */
        if ($this->dateTimePrefix) {
            $now = Carbon::now();
            $year = $now->year;
            $month = $now->month;
            $day = $now->day;
            $prefix .= "{$year}/{$month}/{$day}/";
        }

        return $path . $prefix;
    }


    /**
     * set dateTimePrefix the value
     * and default is true
     * if this is true so append datetime prefix to upload path
     * else dont do
     *
     * @param bool $value
     * @return $this
     */
    public function dateTimePrefix($value = true)
    {
        $this->dateTimePrefix = $value;
        return $this;
    }


    /**
     * get set prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }


    /**
     * get set prefix
     *
     * @param $prefix
     * @return string
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }


    /**
     * check storage path
     *
     * @param $src
     *
     * @return string
     */
    protected function getStorageFolder($src)
    {
        if ($this->storageFolder == "storage")
            return storage_path($src);
        if ($this->storageFolder == "public")
            return public_path($src);
        return public_path($src);
    }


    /**
     * you can set file name for upload
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * get the current name for upload file
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get file path like: /path/prefix/filename.format
     *
     * @param array $parameters
     * @return string
     */
    public function getFilePath()
    {
        return $this->getUploadPath() . $this->getFileName();
    }


    /**
     * get file name like: current name.format
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getName() . '.' . $this->getFormat();
    }

    /**
     * @inheritDoc
     */
    protected function handle($file)
    {
        $path = $this->getFullPath();
        $originalPath = $path . "original/";

        if ($file->move($originalPath, $this->getFileName())) {
            $this->createFileRow();
        }

        $this->resize($originalPath . $this->getFileName(), $path, $this->getFileName());

        return $this;
    }

}
