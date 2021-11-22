<?php

namespace Miladimos\FileManager\Events\File;

use Miladimos\FileManager\Events\Event;

class AfterRename extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
}
