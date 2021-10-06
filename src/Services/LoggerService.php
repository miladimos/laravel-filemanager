<?php

namespace Miladimos\FileManager\Services;

use Illuminate\Support\Facades\Log;

class LoggerService
{
    private $channel;

    public function __construct()
    {
        $this->channel = config('filemanager.logger.channel');
    }

    public function log($msg, $level = 'debug')
    {
        if (!$this->checkLevel($level)) return false;

        Log::channel($this->channel)->$level($msg);
    }

    private function checkLevel($level)
    {
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        return in_array($level, $levels);
    }
}
