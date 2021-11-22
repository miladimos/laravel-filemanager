<?php

namespace Miladimos\FileManager\Events\Image;

use Miladimos\FileManager\Events\Event;

class Resizing extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
}
