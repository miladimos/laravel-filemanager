<?php


namespace Miladimos\FileManager\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\File;

class UploadService
{

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
}
