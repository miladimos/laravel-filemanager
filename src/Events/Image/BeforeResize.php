<?php

namespace Miladimos\FileManager\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BeforeResize
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
