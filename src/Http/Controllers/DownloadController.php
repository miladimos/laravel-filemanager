<?php


namespace Miladimos\FileManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\File;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class DownloadController extends Controller
{

    private $fileModel;

    public function __construct(File $file)
    {
        $this->fileModel = $file;
    }

    public function download($uuid)
    {
        $file = $this->fileModel->where("uuid", $uuid)->firstOrFail();

        $secret = env('APP_KEY');

        $hash = $secret . $file->uuid . getUserIP() . request('t');

        if ((Carbon::createFromTimestamp(request('t')) > Carbon::now()) &&
            Hash::check($hash, request('mac'))) {
            return response()->download();
        } else {
            throw new InternalErrorException("link not valid");
        }

    }

    public function downloadFile($uuid)
    {
        $file = $this->fileModel->where('uuid', $uuid)->first();

        return response()->download(storage_path("app/uploads/") . $file->file_hash, $file->file_name);
    }
}
