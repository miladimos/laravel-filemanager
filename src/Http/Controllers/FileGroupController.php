<?php


namespace Miladimos\FileManager\Http\Controllers;


use Miladimos\FileManager\Services\FileGroupService;

class FileGroupController extends Controller
{
    protected $fileGroupService;

    public function __construct(FileGroupService $fileGroupService)
    {
        $this->fileGroupService = $fileGroupService;
    }

    public function index()
    {
        $fileGroups = $this->fileGroupService->allFileGroups();
        return $this->responseSuccess($fileGroups);
    }

    public function delete($id)
    {
        $fileGroups = $this->fileGroupService->deleteFileGroup($id);
        return $this->responseSuccess($fileGroups);
    }
}
