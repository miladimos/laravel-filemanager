<?php


namespace Miladimos\FileManager\Http\Controllers;


use Miladimos\FileManager\Services\FileGroupService;

class FileGroupController extends Controller
{
    protected $fileGroupService = null;
    public function __construct(FileGroupService $fileGroupService)
    {
        $this->fileGroupService = $fileGroupService;
    }


//"php": ">=7.2",
//        "illuminate/filesystem": "5.0.*",
//        "league/flysystem": "^1.0",
//        "league/flysystem-aws-s3-v3": "^1.0",
//        "intervention/image": "^2.5"
}
