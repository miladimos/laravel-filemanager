<?php

namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Models\Directory;

class directoryController extends Controller
{
    public function createDirectory(Request $request)
    {
        $name = $request->input('name');
        $desc = $request->input('description');
        $parent = $request->input('parent');

        Directory::create([
            'user_id' => auth()->id(),
        ]);

        return $this->responseSuccess("Directory created", 201, "Created");
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

    public function getDirectoryBreadcrumb(Request $request)
    {
        // This probably could be better.
        $id = $request->input('folder');

        if ($id != 0) {
            // Get the current folder
            $folders = Directory::where('id', $id)->get();

            // See if it has a parent
            $parentId = $folders[0]["parent_folder"];

            if ($parentId != 0) {
                $looping = true;

                while ($looping) {
                    // Get the parent details.
                    $nextDirectory = Directory::where('id', $parentId)->get();

                    $parentId = $nextDirectory[0]["parent_folder"];

                    $folders = $folders->merge($nextDirectory);

                    $looping = $parentId != 0;
                }
            }

            return $folders->toJson();
        }

        return null;
    }
}
