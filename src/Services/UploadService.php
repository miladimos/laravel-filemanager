<?php

namespace Miladimos\FileManager\Services;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Miladimos\FileManager\Models\Directory;
use Miladimos\FileManager\Models\File;


// all of about uploads (upload files, ...)
class UploadService extends Service
{
    // custom File model
    private $model;

    private $fileService;

    private $directoryService;

    // default File model
    private $fileModel;

    private $directoryModel;

    public function __construct(Model $model = null)
    {
        parent::__construct();

        $this->model = $model;

        $this->fileService = new FileService();

        $this->directoryService = new DirectoryService();

        $this->fileModel = new File();

        $this->directoryModel = new Directory();
    }

    public function uploadFile(UploadedFile $uploadedFile, $path = null, $directory = 0)
    {

        //        foreach ($request->file('files') as $key => $file) {
////            $file->storeAs($path, $file->getClientOriginalName());
////        }
////

        $path = $path ?? $this->defaultUploadFolderName;

        if ($uploadedFile->isValid()) {

            $model = $this->getFileModel();

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;

            $originalName = $uploadedFile->getClientOriginalName();
            $fileExt = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getClientMimeType();
            $fileSize = $uploadedFile->getSize(); // in bytes

            $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

            $fullUploadedPath = public_path($uploadPath . $this->ds . $originalName);

            $dirPath = public_path($uploadPath);

            $this->mkdir_directory_if_not_exists($dirPath);

            if (checkPath($fullUploadedPath, $this->disk_name)) {
                $finalFileName = Carbon::now()->timestamp . "-{$originalName}";

                $uploadedFile->move($dirPath, $finalFileName);

                $model->create([
                    'file_name' => $finalFileName,
                    'original_name' => $originalName,
                    'file_path' => url($uploadPath . $this->ds . $finalFileName),
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'file_ext' => $fileExt,
                ]);

                return response()->json([
                    'data' => [
                        'url' => url($uploadPath . $this->ds . $finalFileName)
                    ]
                ]);
            }

            $uploadedFile->move($dirPath, $originalName);

            $model->create([
                'file_name' => $originalName,
                'original_name' => $originalName,
                'file_path' => url($uploadPath . $this->ds . $originalName),
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'file_ext' => $fileExt,
                'is_private' => false,
            ]);

            return response()->json([
                'data' => [
                    'url' => url($uploadPath . $this->ds . $originalName)
                ]
            ]);
        }

        return response()->json([
            'data' => 'File is Broken Or Not Valid!'
        ]);
    }

    function mkdir_directory_if_not_exists($dirPath)
    {
        if (!checkPath($dirPath, $this->disk_name)) {
            $this->directoryService->createDirectory([]);
        }
    }

    private function getFileModel()
    {
        if (!$this->model === null) {
            return resolve($this->model);
        }
        return $this->fileModel;
    }

//    public function uploadOneImage(UploadedFile $uploadedFile, $path = null)
//    {
//        $path = $path ?? $this->defaultUploadFolderName;
//
//        if ($uploadedFile->isValid()) {
//            $model = resolve($this->model);
//
//            $image = Image::make($uploadedFile->getRealPath());
//            $year = Carbon::now()->year;
//            $month = Carbon::now()->month;
//            $day = Carbon::now()->day;
//
//            $fileName = $uploadedFile->getClientOriginalName();
//            $fileExt = $uploadedFile->getClientOriginalExtension();
//            $mimeType = $uploadedFile->getClientMimeType();
//            $fileSize = $uploadedFile->getSize();
//
//            $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";
//
//            $fullUploadedPath = public_path($uploadPath . $this->ds . $fileName);
//
//            $dirPath = public_path($uploadPath);
//
//            $this->mkdir_if_not_exists($dirPath);
//
//            if (file_exists($fullUploadedPath)) {
//                $finalFileName = Carbon::now()->timestamp . "-{$fileName}";
//
//                $image->save($dirPath, $finalFileName);
//
//                $model->create([
//                    'file_name' => $finalFileName,
//                    'original_name' => $fileName,
//                    'file_path' => url($uploadPath . $this->ds . $finalFileName),
//                    'file_size' => $fileSize,
//                    'mime_type' => $mimeType,
//                    'file_ext' => $fileExt,
//                    'width' => $image->width(),
//                    'height' => $image->height(),
//                ]);
//
//                return response()->json([
//                    'data' => [
//                        'url' => url($uploadPath . $this->ds . $finalFileName)
//                    ]
//                ]);
//            }
//
//            $image->save($fullUploadedPath);
//
//            $model->create([
//                'file_name' => $fileName,
//                'original_name' => $fileName,
//                'file_path' => url($uploadPath . $this->ds . $fileName),
//                'file_size' => $fileSize,
//                'mime_type' => $mimeType,
//                'file_ext' => $fileExt,
//                'width' => $image->width(),
//                'height' => $image->height(),
//            ]);
//            // $uploadedFile->move(public_path($uploadPath), $fileName);
//
//            return response()->json([
//                'data' => [
//                    'url' => url($uploadPath . $this->ds . $fileName)
//                ]
//            ]);
//        }
//
//        return response()->json([
//            'data' => 'File is Broken Or Not Valid!'
//        ]);
//    }
//
//    // $path = $request->photo->storeAs('images', 'filename.jpg', 'disk');
//

//    public function saveUploadedFiles(UploadedFile $files, $path = '/')
//    {
//        return $files->getUploadedFiles()->reduce(function ($uploaded, UploadedFile $file) use ($path) {
//            $fileName = $file->getClientOriginalName();
//            if ($this->disk->exists($path . $fileName)) {
////                $this->errors[] = 'File ' . $path . $fileName . ' already exists in this folder.';
//
//                return $uploaded;
//            }
//
//            if (!$file->storeAs($path, $fileName, [
//                'disk' => $this->diskName,
//                'visibility' => $this->access,
//            ])) {
////                $this->errors[] = trans('media-manager::messages.upload_error', ['entity' => $fileName]);
//
//                return $uploaded;
//            }
//            $uploaded++;
//
//            return $uploaded;
//        }, 0);
//    }

    public function uploadFileByUrl(string $url, string $field, $fileName = null)
    {
        $uuid = Str::uuid();
        $storage = \Storage::disk(config('upload.disk'));
        $file = file_get_contents($url);
        $url = strtok($url, '?');
        $config = config('upload.files.' . $field);

        $orignalName = str_replace('_', '-', pathinfo($url, PATHINFO_FILENAME));
        $orignalName = str_replace(' ', '-', pathinfo($url, PATHINFO_FILENAME));
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $extension = ($extension) ? "." . $extension : $extension;
        $storagePath = $storage->getDriver()->getAdapter()->getPathPrefix();

        if ($fileName) {
            $fileNameWithExtension = $fileName . $extension;
            $orignalName = $fileName;
        } else {
            $fileNameWithExtension = $orignalName . $extension;
        }

        $storage->put('/uploads/' . $uuid . '/' . $fileNameWithExtension, $file);

        $mimeType = mime_content_type($storagePath . '/uploads/' . $uuid . '/' . $fileNameWithExtension);
        $mimeFileType = $this->getFileType($mimeType);

        $upload = new Upload();
        $upload->private = array_get($config, 'private', false);
        $upload->title = $orignalName;
        $upload->file_field = $field;
        $upload->file_name = $fileNameWithExtension;
        $upload->mime_type = $mimeType;
        $upload->file_type = $mimeFileType;
        $upload->size = (filesize($storagePath . '/uploads/' . $uuid . '/' . $fileNameWithExtension) / 1024) / 1024;
        $upload->uuid = $uuid;
        $upload->save();

        if ($mimeFileType != 'image' || !array_get($config, 'resize')) {
            return $upload;
        }

        foreach ($config['resize'] as $key => $value) {
            if (array_get($value, 'create_on_upload', false)) {
                $this->resizeImage($upload, $key);
                continue;
            }

            ProcessUpload::dispatch($upload, $key);
        }

        return $upload;
    }

    /**
     * File upload trait used in controllers to upload files
     */
    public function saveFiles(Request $request)
    {
        if (!file_exists(public_path('uploads'))) {
            mkdir(public_path('uploads'), 0777);
            mkdir(public_path('uploads/thumb'), 0777);
        }
        $newRequest = null; // Variable to hold a new request created by above array merging
        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
                if ($request->has($key . '_w') && $request->has($key . '_h')) {
                    // Check file width
                    $filename = time() . '-' . $request->file($key)->getClientOriginalName();
                    $file = $request->file($key);
                    $image = Image::make($file);
                    Image::make($file)->resize(50, 50)->save(public_path('uploads/thumb') . '/' . $filename);
                    $width = $image->width();
                    $height = $image->height();
                    if ($width > $request->{$key . '_w'} && $height > $request->{$key . '_h'}) {
                        $image->resize($request->{$key . '_w'}, $request->{$key . '_h'});
                    } elseif ($width > $request->{$key . '_w'}) {
                        $image->resize($request->{$key . '_w'}, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } elseif ($height > $request->{$key . '_w'}) {
                        $image->resize(null, $request->{$key . '_h'}, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $image->save(public_path('uploads') . '/' . $filename);
                    // Determine which request's data to use further
                    $requestDataToMerge = $newRequest == null ? $request->all() : $newRequest->all();
                    // Create new request without changing the original one (prevents removal of specific metadata which disables parsing of a second file)
                    $newRequest = new Request(array_merge($requestDataToMerge, [$key => $filename]));
                } else {
                    $filename = time() . '-' . $request->file($key)->getClientOriginalName();
                    $request->file($key)->move(public_path('uploads'), $filename);
                    // Determine which request's data to use further
                    $requestDataToMerge = $newRequest == null ? $request->all() : $newRequest->all();
                    // Create new request without changing the original one (prevents removal of specific metadata which disables parsing of a second file)
                    $newRequest = new Request(array_merge($requestDataToMerge, [$key => $filename]));
                }
            }
        }
        return $newRequest == null ? $request : $newRequest;
    }

}
