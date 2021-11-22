<?php

namespace Miladimos\FileManager\Events\File;

use Miladimos\FileManager\Events\Event;

class Renaming extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
}
