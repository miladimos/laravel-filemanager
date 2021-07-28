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
        $file = $request->file;
        $new_name = $request->new_name;

        if ($this->fileService->rename($file, $new_name)) {
            return $this->responseSuccess("File Renamed");
        }

        return $this->responseError("Error in Rename File");
    }

    public function moveFile(Request $request)
    {
        $new_dir = $request->input('new_dir'); // id
        $file = $request->input('file');

        if ($this->fileService->moveFile($file, $new_dir)) {
            return $this->responseSuccess("File Moved");
        }

        return $this->responseError("Error in Move File");
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
        $user = $request->user_id;
        $directory = $request->has('directory') ? $request->directory : 0;

        if ($files = $this->fileService->getUserFiles($user, $directory)) {
            return $this->responseError($files);
        }

        return $this->responseError("Error in get user files.");
    }

    public function listAllFiles(Request $request)
    {
        //
    }
}
