<?php


namespace Miladimos\FileManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Miladimos\FileManager\Models\File;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class DownloadController extends Controller
{

    public function __construct()
    {
        //
    }

    public function download($file_id)
    {
        /** @var File $file_id */
        $file = File::query()
            ->where("id", $file_id)
            ->orWhere("name", $file_id)
            ->firstOrFail();

        $config = config('filemanager');

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
