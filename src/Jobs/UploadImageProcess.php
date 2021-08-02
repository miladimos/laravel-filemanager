<?php

namespace Miladimos\FileManager\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Miladimos\FileManager\Services\UploadService;
use Illuminate\Http\UploadedFile;

class UploadImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

    private $uploadService;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
        $this->uploadService = new UploadService();
    }

    public function handle()
    {
        $this->uploadService->uploadImage($this->file);
    }
}
