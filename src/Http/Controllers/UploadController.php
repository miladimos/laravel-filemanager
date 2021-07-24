<?php


namespace Miladimos\FileManager\Http\Controllers;


class UploadController extends Controller
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
            $diskUsed = $userQuota->{'disk_usage'};

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



    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files.*' => 'mimetypes:image/png,image/jpeg'
        ]);
        $path = $request->path ? $request->path : '';

        foreach ($request->file('files') as $key => $file) {
            $file->storeAs($path, $file->getClientOriginalName());
        }

        return response()->json([
            'result' => true
        ]);
    }
}
