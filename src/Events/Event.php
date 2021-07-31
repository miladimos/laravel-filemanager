<?php

namespace Miladimos\FileManager\Events;

use Miladimos\FileManager\Services\LoggerService;

abstract class Event
{
    protected $loggerService;

    public function __construct()
    {
        $this->loggerService = new LoggerService();
    }
}
