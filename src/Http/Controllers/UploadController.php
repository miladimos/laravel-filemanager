<?php


namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Jobs\UploadFileProcess;
use Miladimos\FileManager\Services\UploadService;

class UploadController extends Controller
{
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        //        UploadFileProcess::dispatch($file);

        return $this->uploadService->uploadImage($file, 1);
    }

    public function uploadFiles(Request $request)
    {
        //
    }
}
