<?php
namespace Miladimos\FileManager;

use App\Models\FileGroup;

class FileManager
{
    public function createGroup($groupName)
    {
        $groupModel = resolve(FileGroup::class);
        $groupModel->create([]);

        return true;
    }

    public function deleteGroup($groupName)
    {
        //
    }

    public function addFileToGroup($groupName)
    {
        //
    }

    public function createDirectory($groupName)
    {
        //
    }

    public function deleteDirectory($groupName)
    {
        //
    }

}
