<?php

namespace Miladimos\FileManager\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\File;
use Symfony\Component\CssSelector\Exception\InternalErrorException;


// all of about files
class FileService extends Service
{

    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new File();
    }

    public function rename($path, $originalFileName, $newFileName)
    {
        $nameName = $path . DIRECTORY_SEPARATOR . $newFileName;

        if (!checkPath($newFileName, $this->disk_name)) {
            return false;
        }

        if ($this->disk->rename(($path . DIRECTORY_SEPARATOR . $originalFileName), $nameName)) {

            DB::transaction(function () use ($originalFileName, $newFileName) {
                $this->model->where('name', $originalFileName)->update([
                    'name' => $newFileName
                ]);
            });

            return true;
        }

        return false;
    }

    public function moveFile($file, $newdir)
    {

        if ($this->disk->exists($newFile)) {
//            $this->errors[] = 'File already exists.';

            return false;
        }

        DB::transaction(function () use ($file, $newdir) {
            $this->model->where('id', $file)->update([
                'directory_id' => $newdir
            ]);
        });

        return true;
    }

    public function deleteFile(File $file)
    {
        if (!$this->disk->exists($file->path)) {

        }

        try {
            $file = $this->model->where('id', $file)->first();

            Storage::delete("uploads/" . $file->file_hash);

            $file->forceDelete();

            return response()->json(['msg' => 'File deleted.', 'status' => '200'], 200);
        } catch (\Exception $ex) {
            return response()->json(['msg' => $ex->getMessage(), 'status' => '500'], 500);
        }
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $key => $file) {
            Storage::delete($file);
        }

        return true;
    }

    public function getUserFiles($user, $directory)
    {
        // all files in everywhere
        if ($directory == 0) {
            $files = $this->model->where('user_id', $user)->latest()->get();
        } else {
            $files = $this->model->where('directory_id', $directory)->where('user_id', $user)->latest()->get();
        }

        return $files;
    }


    /**
     * Return the mime type.
     *
     * @param $path
     *
     * @return string
     */
    public function getMimeType($path)
    {
        $type = $this->mimeDetect->detectMimeTypeFromPath(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
        if (!empty($type)) {
            return $type;
        }

        return 'unknown/type';
    }

    /**
     * Return the mime type.
     *
     * @param $path
     *
     * @return string
     */
    public function getExtention($path)
    {
        $type = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!empty($type)) {
            return $type;
        }

        return false;
    }

    /**
     * generate the link for download file
     * this link has expire time
     *
     * @return string
     */
    public function generateLink($uuid)
    {
        $file = File::where('uuid', $uuid)->first();

        $secret = env('APP_KEY');

        $expireTime = (int)config('filemanager.download_link_expire');

        $timestamp = Carbon::now()->addMinutes($expireTime)->timestamp;
        $hash = Hash::make($secret . $file->uuid . getUserIP() . $timestamp);

//        return "/api/filemanager/download/$file->uuid?mac=$hash&t=$timestamp";
        return route('filemanager.download', [$file, $hash, $timestamp]);
    }

    ////////////////////////////////

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

    public function getFile($name = null)
    {
        if (is_null($name) && $this->file instanceof File) return $this->file;

        if (is_null($name)) throw new InternalErrorException("file name not valid!");

        return File::query()
            ->where("name", $name)
            ->first();
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
}
