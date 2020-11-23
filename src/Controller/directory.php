<?php


class FolderController extends Controller
{
    public function createFolder(Request $request) {
        $name = $request->input('name');
        $desc = $request->input('description');
        $parent = $request->input('parent');


        $folder = new Folder();
        $folder->folder_name = $name;
        $folder->folder_desc = $desc;
        $folder->category = 0;
        $folder->user_id = Auth::id();
        $folder->parent_folder = $parent;
        $folder->save();

        return response()->json(['msg' => 'Folder created.', 'status' => '200'], 200);
    }

    public function getUserFolders(Request $request) {
        $folder = $request->input('folder');

        if ($folder == 0) {
            // get the parent folders
            $folders = Folder::where('parent_folder', '0')->where('user_id', Auth::id())->orderBy('folder_name', 'asc')->get();
        } else {
            $folders = Folder::where('parent_folder', $folder)->where('user_id', Auth::id())->orderBy('folder_name', 'asc')->get();
        }

        return $folders->toJson();
    }

    public function getParentFolderId(Request $request) {
        $folder = $request->input('folder');

        return Folder::where('id', $folder)->select('parent_folder')->firstOrFail();
    }

    public function renameFolder(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');

        $folder = Folder::where('id', $id)->first();

        $folder->folder_name = $name;
        $folder->save();

        return response()->json(['msg' => 'Folder renamed.', 'status' => '200'], 200);
    }

    public function getFolderBreadcrumb(Request $request)
    {
        // This probably could be better.
        $id = $request->input('folder');

        if ($id != 0) {
            // Get the current folder
            $folders = Folder::where('id', $id)->get();

            // See if it has a parent
            $parentId = $folders[0]["parent_folder"];

            if ($parentId != 0) {
                $looping = true;

                while ($looping) {
                    // Get the parent details.
                    $nextFolder = Folder::where('id', $parentId)->get();

                    $parentId = $nextFolder[0]["parent_folder"];

                    $folders = $folders->merge($nextFolder);

                    $looping = $parentId != 0;
                }
            }

            return $folders->toJson();
        }

        return null;
    }
}
