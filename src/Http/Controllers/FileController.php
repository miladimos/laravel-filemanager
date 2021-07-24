<?php


namespace Miladimos\FileManager\Http\Controllers;


class FileController extends Controller
{
    public function deleteFile($id)
    {
        try {
            $file = File::where('id', $id)->first();

            Storage::delete("uploads/" . $file->file_hash);

            $file->forceDelete();

            return response()->json(['msg' => 'File deleted.', 'status' => '200'], 200);
        } catch (\Exception $ex) {
            return response()->json(['msg' => $ex->getMessage(), 'status' => '500'], 500);
        }
    }

    public function renameFile(Request $request)
    {
        $id = $request->input('fileId');
        $name = $request->input('fileName');

        $ext = pathinfo($name, PATHINFO_EXTENSION);

        $file = File::where('id', $id)->first();

        $file->file_name = $name;
        $file->file_extension = $ext;
        $file->save();

        return response()->json(['msg' => 'File renamed.', 'status' => '200'], 200);
    }

    public function moveFile(Request $request)
    {
        $folderId = $request->input('folderId');
        $fileId = $request->input('fileId');

        $file = File::where('id', $fileId)->first();

        $file->folder_id = $folderId;
        $file->save();

        return response()->json(['msg' => 'File moved.', 'status' => '200'], 200);
    }

    public function getUserFiles(Request $request)
    {
        $folder = $request->input('folder');

        // un-foldered files
        if ($folder == 0) {
            $files = File::where('folder_id', '0')->where('user_id', Auth::id())->orderBy('file_name', 'asc')->get();
        } else {
            $files = File::where('folder_id', $folder)->where('user_id', Auth::id())->orderBy('file_name', 'asc')->get();
        }

        return $files->toJson();
    }

    public function deleteFiles(Request $request)
    {
        foreach ($request->input('files', []) as $key => $file) {
            Storage::delete($file);
        }

        return json_encode(['result' => true]);
    }

    public function listAllFiles(Request $request)
    {
        $path = $request->input('path', '');
        $directoriesList = Storage::directories($path);
        $filesList = Storage::files($path);

        $directories = [];
        $files = [];

        foreach ($directoriesList as $key => $directory) {
            $directories[] = [
                'name' => last(explode("/", $directory)),
                'path' => $directory,
                'public_path' => Storage::url($directory),
                'size' => Storage::size($directory),
                'type' => 'directory',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($directory))->diffForHumans()
            ];
        }

        foreach ($filesList as $key => $file) {
            $files[] = [
                'name' => last(explode("/", $file)),
                'path' => $file,
                'public_path' => Storage::url($file),
                'size' => Storage::size($file),
                'type' => 'file',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->diffForHumans()
            ];
        }

        return [
            'path' => $path,
            'directoriesAndFiles' => array_merge($directories, $files)
        ];
    }
}
