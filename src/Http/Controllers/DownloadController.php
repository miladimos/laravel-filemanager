<?php


namespace Miladimos\FileManager\Http\Controllers;


class DownloadController
{
    public function download($file)
    {
        /** @var File $file */
        $file = File::query()
            ->where("id", $file)
            ->orWhere("name", $file)
            ->firstOrFail();

        $config = filemanager_config();

        if ($file->isPublic) {
            return $file->download();
        } else {
            $secret = "";
            if ($config['secret']) {
                $secret = $config['secret'];
            }

            $hash = $secret . $file->id . request()->ip() . request('t');

            if ((Carbon::createFromTimestamp(request('t')) > Carbon::now()) &&
                Hash::check($hash, request('mac'))) {
                return $file->download();
            } else {
                throw new InternalErrorException("link not valid");
            }
        }
    }

    public function downloadFile($id)
    {
        $file = File::where('id', $id)->first();

        return response()->download(storage_path("app/uploads/") . $file->file_hash, $file->file_name);
    }
}
