<?php


namespace Miladimos\FileManager\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Miladimos\FileManager\Models\File;

abstract class Service
{
    // public functionalities write here for inherit by other services

    protected $disk;

    protected $base_directory;

    protected $errors = [];

    protected $mimeDetect;

    public function __construct()
    {
        $this->disk = Storage::disk(config('filemanager.disk'));
        $this->base_directory = config('filemanager.base_directory');
        $this->mimeDetect = new FinfoMimeTypeDetector();
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
    protected function generateRandomFileName(int $length = 10): string
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
    protected function generateRandomName(int $length = 10): string
    {
        $chars = range('a', 'z');
        $charsC = range('A', 'Z');
        $nums = range(1, 9) + 1;

        $merged = implode("", array_merge($chars, $charsC, $nums));
        $str = str_shuffle($merged);
        $randomName = Str::random(3) . substr($str, 0, $length);

        return $randomName;
    }

    public function getHumanReadableSize(int $sizeInBytes): string
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

    /**
     * Rename file or folder
     *
     * @param $newName
     * @param $oldName
     *
     * @return bool
     */
    public function rename($oldName, $newName)
    {
        if (!$this->disk->exists($oldName)) return false;

        if ($this->disk->move($oldName, $newName)) return true;
    }


}
