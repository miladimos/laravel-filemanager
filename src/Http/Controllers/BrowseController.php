<?php


namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'directories' => '',
            'files' => '',
        ];

        return $this->responseSuccess($data);
    }
}
