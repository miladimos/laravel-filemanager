<?php

namespace Miladimos\FileManager\Services;

use Miladimos\FileManager\Models\FileGroup;

class FileGroupService extends Service
{

    // FileGroup Model
    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new FileGroup();
    }

    public function allFileGroups()
    {
        return $this->model->all();
    }

    public function createFileGroup($tile, $description)
    {
        $fileGroup = $this->model->create([
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

        return true;
    }

    public function deleteFileGroup(FileGroup $fileGroup)
    {
        if ($fileGroup = $fileGroup->delete())
            return true;

        return false;
    }
}
