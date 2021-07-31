<?php

namespace Miladimos\FileManager\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BeforeUpload
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
