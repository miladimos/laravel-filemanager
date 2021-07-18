<?php

namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected $disk;

    protected $access;

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
