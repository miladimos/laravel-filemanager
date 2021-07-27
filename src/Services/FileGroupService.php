<?php

namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\DB;
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

    public function createFileGroup(array $data)
    {
        DB::transaction(function () use ($data) {
            $fileGroup = $this->model->create([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
        });

        return true;
    }

    public function updateFileGroup(FileGroup $fileGroup, $data)
    {
        DB::transaction(function () use ($fileGroup, $data) {
            $fileGroup->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
        });

        return true;
    }

    public function deleteFileGroup(FileGroup $fileGroup)
    {
        if ($fileGroup->delete())
            return true;

        return false;
    }
}
