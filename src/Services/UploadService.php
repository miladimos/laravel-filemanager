<?php

namespace Miladimos\FileManager\Services;


use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


// all of about uploads (upload file, files, ...)

class UploadService extends Service
{
    protected $access;

    public function __construct()
    {
        parent::__construct();

        $this->access = config('filemanager.access');
    }


    //public function resizeImagePost(Request $request)
    //{
    //    $this->validate($request, [
    //        'title' => 'required',
    //        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //    ]);
    //
    //    $image = $request->file('image');
    //    $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
    //
    //    $destinationPath = public_path('/thumbnail');
    //    $img = Image::make($image->getRealPath());
    //    $img->resize(100, 100, function ($constraint) {
    //        $constraint->aspectRatio();
    //    })->save($destinationPath.'/'.$input['imagename']);
    //
    //    $destinationPath = public_path('/images');
    //    $image->move($destinationPath, $input['imagename']);
    //
    //    $this->postImage->add($input);
    //
    //    return back()
    //        ->with('success','Image Upload successful')
    //        ->with('imageName',$input['imagename']);
    //}

    public function uploadFile(UploadedFile $uploadedFile, string $file)
    {
        $storage = Storage::disk(config('upload.disk'));
        $config = config('upload.files.' . $file);

        $originalName = str_replace('_', '-', $uploadedFile->getClientOriginalName());
        $originalName = str_replace(' ', '-', $originalName);
        $mimeType = $this->getFileType($uploadedFile->getClientMimeType());
        $uuid = Str::uuid();

        $upload = new File();
        $upload->private = array_get($config, 'private', false);
        $upload->title = str_replace('.' . $uploadedFile->getClientOriginalExtension(), '', $uploadedFile->getClientOriginalName());
        $upload->file_field = $file;
        $upload->file_name = $originalName;
        $upload->mime_type = $uploadedFile->getClientMimeType();
        $upload->file_type = $mimeType;
        $upload->size = ($uploadedFile->getSize() / 1024) / 1024;
        $upload->uuid = $uuid;
        $upload->save();

        if ($mimeType === 'image' && array_get($config, 'optimize')) {
            //keep the original file
            if (array_get($config, 'keep_original_file')) {
                //TODO: think about this because it might take more time if uploaded to cloud
                $storage->put('original-uploads/' . $uuid . '/' . $originalName, file_get_contents($uploadedFile));
            }

            //Optimize image from temp path and replace just over there
            ImageOptimizer::optimize($uploadedFile->getRealPath());
        }

        //move uploaded file to storage
        $storage->put('uploads/' . $uuid . '/' . $originalName, file_get_contents($uploadedFile));

        if ($mimeType != 'image' || !array_get($config, 'resize')) {
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
     * Upload files
     *
     * @param void
     * @return string
     */
    public function upload()
    {
        $uploaded_files = request()->file('upload');
        $error_bag = [];
        $new_filename = null;

        foreach (is_array($uploaded_files) ? $uploaded_files : [$uploaded_files] as $file) {
            try {
                $new_filename = $this->lfm->upload($file);
            } catch (\Exception $e) {
                Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                array_push($error_bag, $e->getMessage());
            }
        }

        if (is_array($uploaded_files)) {
            $response = count($error_bag) > 0 ? $error_bag : parent::$success_response;
        } else { // upload via ckeditor5 expects json responses
            if (is_null($new_filename)) {
                $response = [
                    'error' => [ 'message' =>  $error_bag[0] ]
                ];
            } else {
                $url = $this->lfm->setName($new_filename)->url();

                $response = [
                    'url' => $url,
                    'uploaded' => $url
                ];
            }
        }

        return response()->json($response);
    }

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

    public function getFileType(string $mime)
    {
        if (in_array($mime, $this->imageMimes)) {
            return 'image';
        }

        return 'document';
    }

    public function makeImage(Image $image, array $sizeOption)
    {
        $sizeOption += [
            'width' => null,
            'height' => null,
            'fit' => null,
        ];

        if (!$sizeOption['fit']) {
            return $image->resize($sizeOption['width'], $sizeOption['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        if ($sizeOption['fit'] == 'crop') {
            $cropX = isset($sizeOption['x']) ? $sizeOption['x'] : null;
            $cropY = isset($sizeOption['y']) ? $sizeOption['y'] : null;

            $image->crop($sizeOption['width'], $sizeOption['height'], $cropX, $cropY, function ($constraint) {
                $constraint->upsize();
            });
        } elseif ($sizeOption['fit'] == 'max') {
            $image->resize($sizeOption['width'], $sizeOption['height'], function ($constraint) {
                $constraint->upsize();
            });
        } elseif ($sizeOption['fit'] == 'contain') {
            $image->resizeCanvas($sizeOption['width'], $sizeOption['height']);
        } elseif ($sizeOption['fit'] == 'stretch') {
            $image->resize($sizeOption['width'], $sizeOption['height'], function ($constraint) {
                $constraint->upsize();
            });
        } elseif ($sizeOption['fit'] == 'pad') {
            $width = $image->width();
            $height = $image->height();

            $color = isset($sizeOption['color']) ? $sizeOption['color'] : '#fff';
            if ($width < $height) {
                $newHeight = $sizeOption['height'];
                $newWidth = ($width * 100) / $sizeOption['width'];

                $image->resize($newWidth, $newHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->resizeCanvas($sizeOption['width'], $sizeOption['height'], 'center', false, $color);
            } elseif ($width > $height) {
                $newWidth = $sizeOption['width'];
                $newHeight = ($height * 100) / $sizeOption['height'];

                $image->resize($newWidth, $newHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->resizeCanvas($sizeOption['width'], $sizeOption['height'], 'center', false, $color);
            } elseif ($width == $height) {
                $image->resize($sizeOption['width'], $sizeOption['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->resizeCanvas($sizeOption['width'], $sizeOption['height'], 'center', false, $color);
            }
        } else {
            $image->fit($sizeOption['width'], $sizeOption['height'], function ($constraint) {
                $constraint->upsize();
            });
        }

        return $image;
    }

    public function resizeImage(Upload $upload, string $size)
    {
        $storage = \Storage::disk(config('upload.disk'));
        $config = config('upload.files.' . $upload->file_field . '.resize.' . $size);
        $path = $upload->uuid . '/';
        $file = $storage->get('uploads/' . $path . $upload->file_name);

        $extension = pathinfo($upload->file_name, PATHINFO_EXTENSION);
        $name = str_replace('.' . $extension, '', $upload->file_name);

        $image = \Image::make($file);
        $image = $this->makeImage($image, $config);
        $image->encode($extension);
        $storage->put('cache/' . $path . $name . '-' . $size . '.' . $extension, $image->__toString());
    }

    public function deleteFiles()
    {
        $date = now()->subHour(24);
        $uploadIds = [];
        $storage = \Storage::disk(config('upload.disk'));
        $uploads = Upload::query()
            ->where('created_at', '<=', $date)
            ->where('has_reference', false)
            ->get();

        foreach ($uploads as $upload) {
            $uploadIds[] = $upload->id;
            $storage->deleteDirectory('uploads/' . $upload->uuid);
            $storage->deleteDirectory('cache/' . $upload->uuid);
        }

        Upload::destroy($uploadIds);
    }

    public function generateImages($fileField = null)
    {
        $uploads = Upload::query()
            ->where('has_reference', true)
            ->where('file_type', 'image')
            ->where(function ($query) use ($fileField) {
                if ($fileField) {
                    $query->where('file_field', $fileField);
                }
            })
            ->get();

        foreach ($uploads as $upload) {
            foreach (config('upload.files.' . $upload->file_field . '.resize') as $size => $options) {
                $this->resizeImage($upload, $size);
            }
        }
    }

    public function optimizeUploadedImages($fileField = null)
    {
        $storage = \Storage::disk(config('upload.disk'));

        $uploads = Upload::query()
            ->where('file_type', 'image')
            ->where(function ($query) use ($fileField) {
                if ($fileField) {
                    $query->where('file_field', $fileField);
                }
            })
            ->get();

        foreach ($uploads as $upload) {
            foreach ($storage->allFiles('uploads/' . $upload->uuid) as $file) {
                ImageOptimizer::optimize($storage->path($file));
            }

            foreach ($storage->allFiles('cache/' . $upload->uuid) as $file) {
                ImageOptimizer::optimize($storage->path($file));
            }
        }
    }

    public function optimizeImages($path)
    {
        $images = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $path = base_path($path);

        if (!is_dir($path)) {
            throw new \Exception('Directory Not found.');
        }

        $files = \File::allfiles($path);

        foreach ($files as $file) {
            //optimize images only
            if (!in_array(strtolower($file->getExtension()), $images)) {
                continue;
            }

            ImageOptimizer::optimize($file->getRealPath());
        }
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
