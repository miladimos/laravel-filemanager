<?php


namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Models\File;
use Miladimos\FileManager\Services\FileService;

class FileController extends Controller
{
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function renameFile(Request $request)
    {
        //
    }

    public function moveFile(Request $request)
    {
        //
    }

    public function deleteFile(File $file)
    {
        if ($this->fileService->deleteFile($file)) {
            return $this->responseSuccess("File Deleted");
        }

        return $this->responseError("Error in Delete File");

    }

    public function deleteFiles(Request $request)
    {
        if (is_array($request->input('files'))) {
            if ($this->fileService->deleteFiles($request->input('files'))) {
                return $this->responseError("All files Deleted.");
            }
        }

        return $this->responseError("files input is not array.");
    }

    public function getUserFiles(Request $request)
    {
        //
    }

    public function listAllFiles(Request $request)
    {
        //
    }
}
