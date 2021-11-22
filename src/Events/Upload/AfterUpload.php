<?php

namespace Miladimos\FileManager\Events\Upload;

use Miladimos\FileManager\Events\Event;

class AfterUpload extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
}
