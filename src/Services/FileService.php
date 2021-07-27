<?php

namespace Miladimos\FileManager\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\File;


// all of about files
class FileService extends Service
{

    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new File();
    }


    public function deleteFile($id)
    {
        if (!$this->disk->exists($id)) {

        }

        try {
            $file = $this->model->where('id', $id)->first();

            Storage::delete("uploads/" . $file->file_hash);

            $file->forceDelete();

            return response()->json(['msg' => 'File deleted.', 'status' => '200'], 200);
        } catch (\Exception $ex) {
            return response()->json(['msg' => $ex->getMessage(), 'status' => '500'], 500);
        }
    }

    public function deleteFiles(Request $request)
    {
        foreach ($request->input('files', []) as $key => $file) {
            Storage::delete($file);
        }

        return true;
    }

    public function rename($path, $originalFileName, $newFileName)
    {
        $nameName = $path . DIRECTORY_SEPARATOR . $newFileName;
        if ($this->disk->exists($nameName)) {
//            $this->errors[] = 'The file "' . $newFileName . '" already exists in this folder.';

            return false;
        }

        return $this->disk->rename(($path . DIRECTORY_SEPARATOR . $originalFileName), $nameName);
    }


    public function moveFile(Request $request)
    {
        $folderId = $request->input('folderId');
        $fileId = $request->input('fileId');

        if ($this->disk->exists($newFile)) {
//            $this->errors[] = 'File already exists.';

            return false;
        }
        $file = File::where('id', $fileId)->first();

        $file->folder_id = $folderId;
        $file->save();

        return response()->json(['msg' => 'File moved.', 'status' => '200'], 200);
    }




//////////////////////////////////////////////////////////

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


//
//    public function getUserFiles(Request $request)
//    {
//        $folder = $request->input('folder');
//
//        // un-foldered files
//        if ($folder == 0) {
//            $files = File::where('folder_id', '0')->where('user_id', Auth::id())->orderBy('file_name', 'asc')->get();
//        } else {
//            $files = File::where('folder_id', $folder)->where('user_id', Auth::id())->orderBy('file_name', 'asc')->get();
//        }
//
//        return $files->toJson();
//    }
//

//
//    public function listAllFiles(Request $request)
//    {
//        $path = $request->input('path', '');
//        $directoriesList = Storage::directories($path);
//        $filesList = Storage::files($path);
//
//        $directories = [];
//        $files = [];
//
//        foreach ($directoriesList as $key => $directory) {
//            $directories[] = [
//                'name' => last(explode("/", $directory)),
//                'path' => $directory,
//                'public_path' => Storage::url($directory),
//                'size' => Storage::size($directory),
//                'type' => 'directory',
//                'last_modified' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($directory))->diffForHumans()
//            ];
//        }
//
//        foreach ($filesList as $key => $file) {
//            $files[] = [
//                'name' => last(explode("/", $file)),
//                'path' => $file,
//                'public_path' => Storage::url($file),
//                'size' => Storage::size($file),
//                'type' => 'file',
//                'last_modified' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->diffForHumans()
//            ];
//        }
//
//        return [
//            'path' => $path,
//            'directoriesAndFiles' => array_merge($directories, $files)
//        ];
//    }


    public function listAllFiles(Request $request)
    {
        $path = $request->input('path', '');
        $directoriesList = Storage::directories($path);
        $filesList = Storage::files($path);

        $directories = [];
        $files = [];

        foreach ($directoriesList as $key => $directory) {
            $directories[] = [
                'name' => last(explode("/", $directory)),
                'path' => $directory,
                'public_path' => Storage::url($directory),
                'size' => Storage::size($directory),
                'type' => 'directory',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($directory))->diffForHumans()
            ];
        }

        foreach ($filesList as $key => $file) {
            $files[] = [
                'name' => last(explode("/", $file)),
                'path' => $file,
                'public_path' => Storage::url($file),
                'size' => Storage::size($file),
                'type' => 'file',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->diffForHumans()
            ];
        }

        return [
            'path' => $path,
            'directoriesAndFiles' => array_merge($directories, $files)
        ];
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
     * create a new file row in db
     *
     * @param null $name
     * @return mixed
     */
    protected function createFileRow($name = null)
    {
        $file = $this->model->create([
            "name" => $name ?? $this->generateRandomName(),
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
     * generate the link for download file
     * this link has expire time
     *
     * @return string
     */
    public function generateLink(File $file)
    {

        if (isset($config['secret'])) {
            $secret = $config['secret'];
        }

        if (isset($config['download_link_expire'])) {
            $expireTime = (int)$config['download_link_expire'];
        }

        /** @var int $expireTime */
        $timestamp = Carbon::now()->addMinutes($expireTime)->timestamp;
        $hash = Hash::make($secret . $file->id . request()->ip() . $timestamp);

        return "/api/filemanager/download/$file->id?mac=$hash&t=$timestamp";
    }

}
