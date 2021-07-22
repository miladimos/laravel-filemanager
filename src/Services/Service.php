<?php


namespace Miladimos\FileManager\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\File;

abstract class Service
{

    protected $disk;

    protected $base_directory;

    protected $errors = [];

    public function __construct()
    {
        $this->disk = Storage::disk(config('filemanager.disk'));
        $this->base_directory = config('filemanager.base_directory');
    }

    public function errors()
    {
        return $this->errors;
    }

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

    /**
     * generate and unique & random name
     *
     * @param int $length
     * @return string
     */
    protected function generateRandomName(int $length = 10)
    {
        $chars = range('a', 'z');
        $charsC = range('A', 'Z');
        $nums = range(1, 9) + 1;

        $merged = implode("", array_merge($chars, $charsC, $nums));
        $str = str_shuffle($merged);
        $randomName = Str::random(3) . substr($str, 0, $length);

        return $randomName;
    }
}
