<?php


namespace Miladimos\FileManager\Services;


use App\Models\FileGroup;

class FileGroupService
{
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

        if(!$fileGroup)
            return false;
        return $fileGroup;
    }


    public function updateFileGroup($id,$tile, $description)
    {
        $fileGroup = FileGroup::findOrFail($id)->update([
            'title' => $tile,
            'description' => $description,
        ]);
        if(!$fileGroup)
            return false;

        return $fileGroup;
    }

    public function deleteFileGroup($id)
    {
        if($fileGroup = FileGroup::findOrFail($id)->delete())
            return $fileGroup;

        return false;
    }
}
