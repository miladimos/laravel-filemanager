<?php


namespace Miladimos\FileManager\Services;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Miladimos\FileManager\Models\File;
use Miladimos\FileManager\Traits\ErrorHandler;

// public functionalities write here for inherit by other services
abstract class Service
{
    use ErrorHandler;

    protected $disk;

    protected $base_directory;


    protected $mimeDetect;

    public function __construct()
    {
        $this->disk = Storage::disk(config('filemanager.disk'));
        $this->base_directory = config('filemanager.base_directory');
        $this->mimeDetect = new FinfoMimeTypeDetector();
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
     * Rename file or Directory
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


    /**
     * Get content for the selected disk and path
     *
     * @param       $disk
     * @param null $path
     *
     * @return array
     */
    public function getContent($disk, $path = null)
    {
        $content = Storage::disk($disk)->listContents($path);

        // get a list of directories
        $directories = $this->filterDir($disk, $content);

        // get a list of files
        $files = $this->filterFile($disk, $content);

        return compact('directories', 'files');
    }

    /**
     * Get only directories
     *
     * @param $content
     *
     * @return array
     */
    protected function filterDir($disk, $content)
    {
        // select only dir
        $dirsList = Arr::where($content, function ($item) {
            return $item['type'] === 'dir';
        });

        // remove 'filename' param
        $dirs = array_map(function ($item) {
            return Arr::except($item, ['filename']);
        }, $dirsList);

        return array_values($dirs);
    }

    /**
     * Get only files
     *
     * @param $disk
     * @param $content
     *
     * @return array
     */
    protected function filterFile($disk, $content)
    {
        // select only files
        $files = Arr::where($content, function ($item) {
            return $item['type'] === 'file';
        });

        return array_values($files);
    }

    /**
     * Get directories for tree module
     *
     * @param $disk
     * @param $path
     *
     * @return array
     */
    public function getDirectoriesTree($disk, $path = null)
    {
        $directories = $this->directoriesWithProperties($disk, $path);

        foreach ($directories as $index => $dir) {
            $directories[$index]['props'] = [
                'hasSubdirectories' => Storage::disk($disk)
                    ->directories($dir['path']) ? true : false,
            ];
        }

        return $directories;
    }

    /**
     * File properties
     *
     * @param       $disk
     * @param null $path
     *
     * @return mixed
     */
    public function fileProperties($disk, $path = null)
    {
        $file = Storage::disk($disk)->getMetadata($path);

        $pathInfo = pathinfo($path);

        $file['basename'] = $pathInfo['basename'];
        $file['dirname'] = $pathInfo['dirname'] === '.' ? ''
            : $pathInfo['dirname'];
        $file['extension'] = isset($pathInfo['extension'])
            ? $pathInfo['extension'] : '';
        $file['filename'] = $pathInfo['filename'];

        return $file;
    }

}
