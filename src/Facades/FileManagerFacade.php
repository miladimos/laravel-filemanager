<?php

namespace Miladimos\Social\Facades;

use Illuminate\Support\Facades\Facade;

class FileManagerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filemanager';
    }
}
