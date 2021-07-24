<?php

namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Models\Directory;
use Miladimos\FileManager\Services\DirectoryService;

class directoryController extends Controller
{

    private $directoryService;

    public function __construct()
    {
        $this->directoryService = new DirectoryService();
    }

    public function createDirectory(Request $request)
    {

        $data = [
            'directoryName' => $request->input('name'),
            'directoryDescription' => $request->input('description'),
            'directoryParent' => $request->input('parent_id'),
        ];

        if ($this->directoryService->createDirectory($data))
            return $this->responseSuccess("Directory created", 201, "Created");

        return $this->responseError("Error in Directory create", 500);

    }

    public function deleteDirectories(Request $request)
    {
        foreach ($request->input('directories', []) as $key => $directory) {
            if ($this->directoryService->deleteDirectory($directory))
                continue;
        }

        return $this->responseSuccess("Directories Deleted");
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

    public function getParentDirectoryId(Request $request)
    {
        $folder = $request->input('folder');

        return Directory::where('id', $folder)->select('parent_folder')->firstOrFail();
    }

    public function renameDirectory(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');

        $folder = Directory::where('id', $id)->first();

        $folder->folder_name = $name;
        $folder->save();

        return response()->json(['msg' => 'Directory renamed.', 'status' => '200'], 200);
    }

}
