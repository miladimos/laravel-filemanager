<?php

namespace Miladimos\FileManager\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\Directory;
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

    public function rename(File $file, $newFileName)
    {

        if (!checkPath($file->path, $this->disk_name)) {
            return false;
        }

        if ($this->disk->move(($file->path . $this->ds . $file->name), $file->path . $this->ds . $newFileName)) {

            DB::transaction(function () use ($file, $newFileName) {
                $this->model->where('name', $file->name)->update([
                    'name' => $newFileName,
                ]);
            });

            return true;
        }

        return false;
    }

    public function moveFile(File $file, Directory $newdir)
    {

        if (!checkPath($file->path, $this->disk_name)) {
            return false;
        }

        if ($this->disk->move($file->path, $newdir->path)) {
            DB::transaction(function () use ($file, $newdir) {
                $this->model->where('id', $file)->update([
                    'directory_id' => $newdir->id,
                    'path' => $newdir->path
                ]);
            });
        }

        return false;
    }

    public function copyFile(File $file, Directory $new_dir)
    {

        if (!checkPath($file->path, $this->disk_name)) {
            return false;
        }

        if ($this->disk->copy($file->path, $new_dir->path)) {
            DB::transaction(function () use ($file, $new_dir) {
                $this->model->where('id', $file)->update([
                    'directory_id' => $new_dir,
                    'path' => $new_dir->path
                ]);
            });

            return true;
        }

        return false;
    }

    public function deleteFile(File $file)
    {
        if (!checkPath($file->path, $this->disk_name)) {
            return false;
        }

        if ($this->disk->delete($file->path)) {
            $file->forceDelete();

            return true;
        }

        return false;
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
