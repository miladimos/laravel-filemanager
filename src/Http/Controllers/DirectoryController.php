<?php

namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Miladimos\FileManager\Models\Directory;
use Miladimos\FileManager\Services\DirectoryService;

class DirectoryController extends Controller
{

    private $directoryService;

    public function __construct(DirectoryService $directoryService)
    {
        $this->directoryService = $directoryService;
    }

    public function createDirectory(Request $request)
    {
        $data = [
            'name' => $request->get('name'),
            'description' => $request->has('description') ? $request->input('description') : null,
            'parent_id' => $request->has('parent_id') ? $request->input('parent_id') : 0,
        ];

        if ($this->directoryService->createDirectory($data)) {
            $msg = trans('filemanager::messages.directory_created');
            return $this->responseSuccess($msg, 201, "Created");
        }

        return $this->responseError("Error in Directory create", 500);
    }

    public function deleteDirectories(Request $request)
    {
        if (is_array($request->get('directories'))) {
            foreach ($request->get('directories', []) as $key => $directory) {
                if (!$this->directoryService->deleteDirectory($directory)) {
                    return $this->responseError("Error in Directory Delete", 500);
                }
            }
        }

        if (!$this->directoryService->deleteDirectory($request->get('directories'))) {
            return $this->responseError("Error in Directory Delete", 500);
        }

        return $this->responseSuccess("Directories Deleted");
    }

    public function renameDirectory(Directory $directory, Request $request)
    {
        $name = $request->input('new_name');

        if (checkInstanceOf($directory, Directory::class)) {
        }

        return response()->json(['msg' => 'Directory renamed.', 'status' => '200'], 200);
    }

    public function getUserDirectorys(Request $request)
    {
        $folder = $request->input('folder');

        if ($folder == 0) {
            // get the parent folders
            $folders = Directory::where('parent_folder', '0')->where('user_id', Auth::id())->orderBy('folder_name', 'asc')->get();
        } else {
            $folders = Directory::where('parent_folder', $folder)->where('user_id', Auth::id())->orderBy('folder_name', 'asc')->get();
        }

        return $folders->toJson();
    }

    public function getParentDirectory(Directory $directory)
    {
        return $directory->parent();
    }
}
