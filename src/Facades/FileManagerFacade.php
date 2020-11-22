<?php

namespace Miladimos\Social\Facades;

use Illuminate\Support\Facades\Facade;

class RepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filemanager';
    }
}
