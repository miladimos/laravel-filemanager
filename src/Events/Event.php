<?php

namespace Miladimos\FileManager\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Miladimos\FileManager\Services\LoggerService;


abstract class Event implements ShouldQueue
{
    use Dispatchable,
        SerializesModels;

    protected $loggerService;

    public function __construct()
    {
        $this->loggerService = resolve(LoggerService::class);
    }
}
