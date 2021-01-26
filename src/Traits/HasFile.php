<?php

namespace Miladimos\FileManager\Traits;

use Miladimos\FileManager\Models\File;

trait HasFile
{
    public function files()
    {
        return $this->morphMany(File::class);
    }

    public static function getHumanReadableSize(int $sizeInBytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes == 0) {
            return '0 ' . $units[1];
        }

        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2) . ' ' . $units[$i];
    }
}
