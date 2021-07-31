<?php

namespace Miladimos\FileManager\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Moving
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
