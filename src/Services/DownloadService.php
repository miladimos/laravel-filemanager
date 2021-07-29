<?php


namespace Miladimos\FileManager\Services;


use Miladimos\FileManager\Models\File;

class DownloadService extends Service
{
    public function download(File $file)
    {
        if (!$file->private) {
            $path = public_path($file->path);
        } else {
            $path = storage_path($file->path);
        }
        return response()->download($path);
    }
}
