<?php


namespace Miladimos\FileManager\Services;


use Illuminate\Support\Str;
use Miladimos\FileManager\Models\File;

abstract class Service
{
    /**
     * Sanitize the directory name.
     *
     * @param $directory
     *
     * @return mixed
     */
    protected function cleanDirectoryName($directory)
    {
        return DIRECTORY_SEPARATOR . trim(str_replace('..', '', $directory), DIRECTORY_SEPARATOR);
    }

    /**
     * generate and unique & random name
     *
     * @param int $length
     * @return string
     */
    protected function generateRandomFileName(int $length = 10)
    {
        do {
            $randomName = Str::random($length);
            $check = File::query()
                ->where("name", $randomName)
                ->first();
        } while (!empty($check));

        return $randomName;
    }
}
