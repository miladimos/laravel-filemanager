<?php

namespace Miladimos\FileManager\Traits;
use Miladimos\FileManager\Models\File;

trait HasFile
{
    public function files()
    {
        return $this->morphMany(File::class);
    }

}
