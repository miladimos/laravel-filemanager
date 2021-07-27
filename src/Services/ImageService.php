<?php

namespace Miladimos\FileManager\Services;

use Intervention\Image\Image;
use Miladimos\FileManager\Models\File;

// all of about images (resize - quality ...)
class ImageService extends Service
{

    private $sizes;

    public function __construct()
    {
        parent::__construct();

        $this->sizes = config('filemanager.images.sizes');
    }

    protected function handleDelete(File $file)
    {
        if (is_null($this->getSizes())) {
            if ($sizes = $this->getConfig("sizes"))
                $this->setSizes($sizes);
            else
                $this->setSizes(["16", "24", "32", "64", "128"]);
        }

        if (is_null($this->getThumbSize())) {
            if (!$thumb = $this->getConfig("thumb"))
                $this->setThumbSize($thumb);
            else
                $this->setThumbSize("128");
        }

        $sizes = $this->getSizes();
        foreach ($sizes as $size) {
            $sizePath = $file->base_path . "{$size}/";
            $sizePath = $sizePath . $file->file_name;
            if ($file->private) {
                $sizePath = storage_path($sizePath);
            } else {
                $sizePath = public_path($sizePath);
            }

            $this->disk->delete($sizePath);
        }

        $thumbSize = $file->base_path . "thumb/" . $file->file_name;
        $originalSize = $file->base_path . "original/" . $file->file_name;

        if ($file->private) {
            $thumbSize = storage_path($thumbSize);
        } else {
            $thumbSize = public_path($thumbSize);
        }

        if ($file->private) {
            $originalSize = storage_path($originalSize);
        } else {
            $originalSize = public_path($originalSize);
        }

        $this->disk->delete($thumbSize);
        $this->disk->delete($originalSize);

        return true;
    }

    /**
     * Crop the image (called via ajax).
     */
    public function getCropimage($overWrite = true)
    {
        $image_name = request('img');
        $image_path = $this->lfm->setName($image_name)->path('absolute');
        $crop_path = $image_path;

        if (!$overWrite) {
            $fileParts = explode('.', $image_name);
            $fileParts[count($fileParts) - 2] = $fileParts[count($fileParts) - 2] . '_cropped_' . time();
            $crop_path = $this->lfm->setName(implode('.', $fileParts))->path('absolute');
        }

        event(new ImageIsCropping($image_path));

        $crop_info = request()->only('dataWidth', 'dataHeight', 'dataX', 'dataY');

        // crop image
        Image::make($image_path)
            ->crop(...array_values($crop_info))
            ->save($crop_path);

        // make new thumbnail
        $this->lfm->makeThumbnail($image_name);

        event(new ImageWasCropped($image_path));
    }

    public function getNewCropimage()
    {
        $this->getCropimage(false);
    }


    /**
     * resize image and return specific array of images
     *
     * @param $filePath
     * @param $uploadPath
     * @param $fileName
     * @return mixed
     */
    protected function resize($filePath, $uploadPath, $fileName)
    {
        if (is_null($this->getSizes())) {
            if ($sizes = $this->getConfig("sizes"))
                $this->setSizes($sizes);
            else
                $this->setSizes(["16", "24", "32", "64", "128"]);
        }

        if (is_null($this->getThumbSize())) {
            if (!$thumb = $this->getConfig("thumb"))
                $this->setThumbSize($thumb);
            else
                $this->setThumbSize("128");
        }

        $sizes = $this->getSizes();
        foreach ($sizes as $size) {
            $sizeUploadPath = $uploadPath . "{$size}/";
            if (!is_dir($sizeUploadPath)) mkdir($sizeUploadPath);
            $sizeName = $sizeUploadPath . $fileName;
            \Intervention\Image\Facades\Image::make($filePath)->fit($size, $size, function ($constraint) {
                $constraint->aspectRatio();
//                $constraint->upsize();
            })->save($sizeName);
        }

        $thumbUploadPath = $uploadPath . "thumb/";
        if (!is_dir($thumbUploadPath)) mkdir($thumbUploadPath);
        $thumbPath = $thumbUploadPath . $fileName;
        copy($uploadPath . "{$this->getThumbSize()}/" . $fileName, $thumbPath);

        return $this;
    }

    public function preview($disk, $path)
    {
        // get image
        $preview = Image::make($this->disk($disk)->get($path));

        return $preview->response();
    }


    public function url($disk, $path)
    {
        return [
            'result' => [
                'status' => 'success',
                'message' => null,
            ],
            'url' => $this->disk->disk($disk)->url($path),
        ];
    }

//
//class ImageRequest extends FormRequest
//{
//    use ConvertsBase64ToFiles;
//
//    protected function base64FileKeys(): array
//    {
//        return [
//            'jpg_image' => 'Logo.jpg',
//        ];
//    }
//
//    public function rules()
//    {
//        return [
//            'jpg_image' => ['required', 'file', 'image'],
//        ];
//    }
//}
//
//
//trait ConvertsBase64ToFiles
//{
//    protected function base64FileKeys(): array
//    {
//        return [];
//    }
//
//    /**
//     * Pulls the Base64 contents for each image key and creates
//     * an UploadedFile instance from it and sets it on the
//     * request.
//     *
//     * @return void
//     */
//    function prepareForValidation()
//    {
//        Collection::make($this->base64FileKeys())->each(function ($filename, $key) {
//            rescue(function () use ($key, $filename) {
//                $base64Contents = $this->input($key);
//
//                if (!$base64Contents) {
//                    return;
//                }
//
//                // Generate a temporary path to store the Base64 contents
//                $tempFilePath = tempnam(sys_get_temp_dir(), $filename);
//
//                // Store the contents using a stream, or by decoding manually
//                if (Str::startsWith($base64Contents, 'data:') && count(explode(',', $base64Contents)) > 1) {
//                    $source = fopen($base64Contents, 'r');
//                    $destination = fopen($tempFilePath, 'w');
//
//                    stream_copy_to_stream($source, $destination);
//
//                    fclose($source);
//                    fclose($destination);
//                } else {
//                    file_put_contents($tempFilePath, base64_decode($base64Contents, true));
//                }
//
//                $uploadedFile = new UploadedFile($tempFilePath, $filename, null, null, true);
//
//                $this->request->remove($key);
//                $this->files->set($key, $uploadedFile);
//            }, null, false);
//        });
//    }
}
