<?php

namespace Miladimos\FileManager\Services;


use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Miladimos\FileManager\Events\AfterUpload;
use Miladimos\FileManager\Events\BeforeUpload;
use Miladimos\FileManager\Models\Directory;
use Miladimos\FileManager\Models\File;


// all of about uploads (upload files, ...)
class UploadService extends Service
{
    private $directoryService;

    // default File model
    private $fileModel;

    private $directoryModel;

    public function __construct()
    {
        parent::__construct();

        $this->directoryService = new DirectoryService();

        $this->fileModel = new File();

        $this->directoryModel = new Directory();
    }

    public function uploadFile(UploadedFile $uploadedFile, $directory_id = 0)
    {
///             ProcessUpload::dispatch($upload, $key);

        $path = $this->directoryModel->find($directory_id)->path;

        if ($uploadedFile->isValid() && $this->fileExtIsAllowed($uploadedFile->getExtension())) {

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;

            $originalName = $uploadedFile->getClientOriginalName();
            $fileExt = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getClientMimeType();
            $fileSize = $uploadedFile->getSize(); // in bytes

//            $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

//            $this->mkdir_directory_if_not_exists($uploadPath);

            $finalFileName = Carbon::now()->timestamp . "-{$originalName}";

            $fullUploadedPath = $path . $this->ds . $finalFileName;

            if ($this->disk->put($fullUploadedPath, $uploadedFile->getContent())) {
                DB::transaction(function () use ($originalName, $finalFileName, $directory_id, $path, $fullUploadedPath, $fileSize, $mimeType, $fileExt) {
                    $this->fileModel->create([
                        'original_name' => $originalName,
                        'name' => $finalFileName,
                        'disk' => $this->disk_name,
                        'directory_id' => $directory_id,
//                'user_id' => user()->id,
                        'path' => $path,
                        'url' => url('storage/' . $fullUploadedPath),
                        'size' => $fileSize,
                        'mime_type' => $mimeType,
                        'extension' => $fileExt,
                    ]);
                });

                return true;
            } else
                return false;
        }

        return false;
    }

    public function uploadImage(UploadedFile $uploadedFile, $directory_id = 0)
    {
        $path = $this->directoryModel->find($directory_id)->path;

        if ($uploadedFile->isValid() && $this->fileExtIsAllowed($uploadedFile->getClientOriginalExtension())) {

            $image = Image::make($uploadedFile->getRealPath());
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;

            $originalName = $uploadedFile->getClientOriginalName();
            $fileExt = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getClientMimeType();
            $fileSize = $uploadedFile->getSize();

            $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

//            $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

//            $this->mkdir_directory_if_not_exists($uploadPath);

            $finalFileName = Carbon::now()->timestamp . "-{$originalName}";

            $fullUploadedPath = $path . $this->ds . $finalFileName;

            event(new BeforeUpload());

            if ($this->disk->put($fullUploadedPath, $image->encode())) {
                DB::transaction(function () use ($originalName, $finalFileName, $directory_id, $path, $fullUploadedPath, $fileSize, $mimeType, $fileExt, $image) {
                    $this->fileModel->create([
                        'original_name' => $originalName,
                        'name' => $finalFileName,
                        'disk' => $this->disk_name,
                        'directory_id' => $directory_id,
//                'user_id' => user()->id,
                        'path' => $path,
                        'url' => url('storage/' . $fullUploadedPath),
                        'size' => $fileSize,
                        'mime_type' => $mimeType,
                        'extension' => $fileExt,
                        'width' => $image->width(),
                        'height' => $image->height(),
                    ]);
                });

                event(new AfterUpload());

                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    function mkdir_directory_if_not_exists($dirPath)
    {
        dd(explode("/", $dirPath));
        if (!checkPath($dirPath, $this->disk_name)) {
            $this->directoryService->createDirectory([]);
        }
    }

    // file extension is allowed from config
    public function fileExtIsAllowed($ext)
    {
        $exts = config('filemanager.allowed_extensions');
        return in_array($ext, $exts);
    }

    // file mime is allowed from config
    public function fileMimeIsAllowed($mime)
    {
        $mimes = config('filemanager.allowed_mimes');
        return in_array($mime, $mimes);
    }

    public function saveUploadedFiles(UploadedFile $files, $directory_id = 0)
    {
        $path = $this->directoryModel->find($directory_id)->path;

        return $files->getUploadedFiles()->reduce(function ($uploaded, UploadedFile $file) use ($path) {
            $fileName = $file->getClientOriginalName();
            if ($this->disk->exists($path . $fileName)) {
//                $this->errors[] = 'File ' . $path . $fileName . ' already exists in this folder.';

                return $uploaded;
            }

            if (!$file->storeAs($path, $fileName, [
                'disk' => $this->diskName,
                'visibility' => $this->access,
            ])) {
//                $this->errors[] = trans('media-manager::messages.upload_error', ['entity' => $fileName]);

                return $uploaded;
            }
            $uploaded++;

            return $uploaded;
        }, 0);
    }

//    public function uploadFileByUrl(string $url, string $field, $fileName = null)
//    {
//        $uuid = Str::uuid();
//        $file = file_get_contents($url);
//        $url = strtok($url, '?');
//        $config = config('upload.files.' . $field);
//
//        $orignalName = str_replace('_', '-', pathinfo($url, PATHINFO_FILENAME));
//        $orignalName = str_replace(' ', '-', pathinfo($url, PATHINFO_FILENAME));
//        $extension = pathinfo($url, PATHINFO_EXTENSION);
//        $extension = ($extension) ? "." . $extension : $extension;
//        $storagePath = $this->disk->getDriver()->getAdapter()->getPathPrefix();
//
//        if ($fileName) {
//            $fileNameWithExtension = $fileName . $extension;
//            $orignalName = $fileName;
//        } else {
//            $fileNameWithExtension = $orignalName . $extension;
//        }
//
//        $this->disk->put('/uploads/' . $uuid . '/' . $fileNameWithExtension, $file);
//
//        $mimeType = mime_content_type($storagePath . '/uploads/' . $uuid . '/' . $fileNameWithExtension);
//        $mimeFileType = $this->getFileType($mimeType);
//
//        $file = $this->fileModel->create([
//            'private' => array_get($config, 'private', false),
//            'title' => $orignalName,
//            'file_field' => $field,
//            'file_name' => $fileNameWithExtension,
//            'mime_type' => $mimeType,
//            'file_type' => $mimeFileType,
//            'size' => (filesize($storagePath . '/uploads/' . $uuid . '/' . $fileNameWithExtension) / 1024) / 1024,
//            'uuid' => $uuid,
//        ]);
//
//
//        if ($mimeFileType != 'image' || !array_get($config, 'resize')) {
//            return true;
//        }
//
//        foreach ($config['resize'] as $key => $value) {
//            if (array_get($value, 'create_on_upload', false)) {
//                $this->resizeImage($file, $key);
//                continue;
//            }
//
//            ProcessUpload::dispatch($file, $key);
//        }
//
//        return $file;
//    }
}
