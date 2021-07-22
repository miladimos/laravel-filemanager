<?php


namespace Miladimos\FileManager\Http\Controllers;


class BrowseController
{
    public function index()
    {
        return [
            'result'      => [
                'status'  => 'success',
                'message' => null,
            ],
//            'directories' => $content['directories'],
//            'files'       => $content['files'],
        ];
    }
}
