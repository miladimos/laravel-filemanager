<?php

namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\Storage;

class UploadService
{
    protected $disk;

    protected $access;

    protected $mimeDetect;

    private $errors = [];

    public function __construct()
    {
        $this->access = config('filemanager.access');
        $this->disk = Storage::disk(config('filemanager.disk'));
    }

    public function errors()
    {
        return $this->errors;
    }
}
