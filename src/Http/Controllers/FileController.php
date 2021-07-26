<?php


namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Services\FileService;

class FileController extends Controller
{
    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileService();
    }

    public function deleteFile($uuid)
    {
        //
    }

    public function renameFile(Request $request)
    {
        //
    }

    public function moveFile(Request $request)
    {
        //
    }

    public function getUserFiles(Request $request)
    {
        //
    }

    public function deleteFiles(Request $request)
    {
        //
    }

    public function listAllFiles(Request $request)
    {
        //
    }
}
