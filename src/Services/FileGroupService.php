<?php

namespace Miladimos\FileManager\Services;

use Miladimos\FileManager\Models\FileGroup;

class FileGroupService extends Service
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

        if (!$fileGroup) return false;

        return $fileGroup;
    }

    public function updateFileGroup(FileGroup $fileGroup, $tile, $description)
    {
        $fileGroup = $fileGroup->update([
            'title' => $tile,
            'description' => $description,
        ]);
        if (!$fileGroup) return false;

        return $fileGroup;
    }

    public function deleteFileGroup(FileGroup $fileGroup)
    {
        if ($fileGroup = $fileGroup->delete())
            return $fileGroup;

        return false;
    }
}
