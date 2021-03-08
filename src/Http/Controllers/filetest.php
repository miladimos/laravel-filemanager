<?php

namespace Miladimos\FileManager\Http\Controllers;

class FileController extends Controller
{
    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {

            $path = $request->file('file')->store('uploads');

            $folder = $request->input('folder');
            //$folder = 0;
            $path = str_replace("uploads/", "", $path);

            $size = $request->file('file')->getClientSize();
            $fileName = $request->file('file')->getClientOriginalName();
            $fileExt = $request->file('file')->getClientOriginalExtension();

            // first check that the user has got some quota space
            $userQuota = QuotaHelper::getUserQuotaUsed(Auth::id());

            $userQuota = json_decode($userQuota);

            $diskQuota = $userQuota->{'disk_quota'};
            $diskUsed  = $userQuota->{'disk_usage'};

            if (($size / 1024 / 1024) + $diskUsed > $diskQuota) {
                return response()->json(['msg' => 'You do not have enough of you quota left to upload this file.', 'status' => '500'], 500);
            }

            $file = new File();
            $file->user_id = Auth::id();
            $file->folder_id = $folder;
            $file->file_name = $fileName;
            $file->file_extension = $fileExt;
            $file->file_size = $size;
            $file->file_hash = $path;
            $file->save();

            return response()->json(['msg' => 'File uploaded.', 'status' => '200'], 200);
        } else {
            return response()->json(['msg' => 'No file was specified.', 'status' => '500'], 500);
        }
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

    public function downloadFile($id)
    {
        $file = File::where('id', $id)->first();

        return response()->download(storage_path("app/uploads/") . $file->file_hash, $file->file_name);
    }

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
}
