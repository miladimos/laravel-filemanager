<?php


namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Models\FileGroup;
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

    public function store(Request $request)
    {
        $fileGroups = $this->fileGroupService->createFileGroup($request->only(['title', 'description']));
        return $this->responseSuccess("FileGroup Created");
    }

    public function update(FileGroup $fileGroup, Request $request)
    {
        $fileGroups = $this->fileGroupService->updateFileGroup($fileGroup, $request->only(['title', 'description']));
        return $this->responseSuccess("FileGroup Updated");
    }

    public function delete(FileGroup $fileGroup)
    {
        $fileGroups = $this->fileGroupService->deleteFileGroup($fileGroup);
        return $this->responseSuccess("FileGroup Deleted");
    }
}
