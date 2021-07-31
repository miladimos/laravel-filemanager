<?php

namespace Miladimos\FileManager\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AfterResize
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
