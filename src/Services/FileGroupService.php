<?php


namespace Miladimos\FileManager\Services;


use App\Models\FileGroup;

class FileGroupService
{
    public function createFileGroup($tile, $description)
    {
        $fileGroup = FileGroup::create([
            'title' => $tile,
            'description' => $description,
        ]);

        return true;
    }
}
