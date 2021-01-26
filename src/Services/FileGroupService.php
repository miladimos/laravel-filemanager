<?php


namespace Miladimos\FileManager\Services;


use App\Models\FileGroup;
use Illuminate\Support\Facades\Storage;

class FileGroupService
{


    protected $disk;


    protected $access;


    protected $mimeDetect;


    private $errors = [];

    private $diskName;


    public function __construct()
    {
        $this->diskName = config('media-manager.disk');
        $this->access = config('media-manager.access');
        $this->breadcrumbRootLabel = config('media-manager.breadcrumb.root');
        $this->disk = Storage::disk($this->diskName);
    }


    public function allFileGroups()
    {
        return FileGroup::all();
    }

    public function createFileGroup($tile, $description)
    {
        $fileGroup = FileGroup::create([
            'title' => $tile,
            'description' => $description,
        ]);

        if (!$fileGroup)
            return false;
        return $fileGroup;
    }

    // if (! file_exists(public_path().'/uploads')) { File::makeDirectory(public_path().'/uploads',0777, true);}


    public function updateFileGroup(FileGroup $fileGroup, $tile, $description)
    {
        $fileGroup = $fileGroup->update([
            'title' => $tile,
            'description' => $description,
        ]);
        if (!$fileGroup)
            return false;

        return $fileGroup;
    }

    public function deleteFileGroup(FileGroup $fileGroup)
    {
        if ($fileGroup = $fileGroup->delete())
            return $fileGroup;

        return false;
    }
}
