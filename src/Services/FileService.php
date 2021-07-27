<?php

namespace Miladimos\FileManager\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function deleteFile(File $file)
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

    public function deleteFiles(array $files)
    {
        foreach ($files as $key => $file) {
            Storage::delete($file);
        }

        return true;
    }

//////////////////////////////////////////////////////////


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


    public function getUserFiles(Request $request)
    {
        $folder = $request->input('folder');

        // un-foldered files
        if ($folder == 0) {
            $files = File::where('folder_id', '0')->where('user_id', Auth::id())->orderBy('file_name', 'asc')->get();
        } else {
            $files = File::where('folder_id', $folder)->where('user_id', Auth::id())->orderBy('file_name', 'asc')->get();
        }

        return $files->toJson();
    }


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
