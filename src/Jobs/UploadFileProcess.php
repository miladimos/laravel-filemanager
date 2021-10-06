<?php

namespace Miladimos\FileManager\Jobs;

use Miladimos\FileManager\Services\UploadService;
use Illuminate\Http\UploadedFile;

class UploadFileProcess extends Job
{

    public $file;

    private $uploadService;

    public function __construct(UploadedFile $file)
    {
        parent::__construct();

        $this->file = $file;
        $this->uploadService = new UploadService();
    }

    public function handle()
    {
        $this->uploadService->uploadFile($this->file);
    }
}
