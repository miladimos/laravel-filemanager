<?php

namespace Miladimos\FileManager\Events\File;

use Miladimos\FileManager\Events\Event;

class AfterMove extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
}
