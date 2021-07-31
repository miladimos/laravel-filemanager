<?php


namespace Miladimos\FileManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Miladimos\FileManager\Models\File;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class DownloadController extends Controller
{

    private $fileModel;

    public function __construct(File $file)
    {
        $this->fileModel = $file;
    }

    public function download(File $file)
    {
        $path = $file->path . DIRECTORY_SEPARATOR . $file->name;

        return Storage::disk($file->disk)->download($path, $file->name);

    }

    public function downloadFile($uuid)
    {
        $file = $this->fileModel->where('uuid', $uuid)->first();

        $secret = env('APP_KEY');

        $hash = $secret . $file->uuid . getUserIP() . request('t');

        if ((Carbon::createFromTimestamp(request('t')) > Carbon::now()) &&
            Hash::check($hash, request('mac'))) {
            return response()->download();
        } else {
            throw new InternalErrorException("link not valid");
        }

        return response()->download(storage_path("app/uploads/") . $file->file_hash, $file->file_name);
    }
}
