<?php

namespace Miladimos\FileManager\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Uploading extends Event
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        parent::__construct();
    }
}
