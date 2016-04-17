<?php

namespace Nodefortytwo\DynamicLogHandler;

use Log;

class Timer
{
    public $name;

    public function __construct(string $name)
    {
        $this->name  = $name;
        $this->start = microtime(true);
    }

    public function end()
    {
        Log::info($this->name, ['time' => microtime(true) - $this->start]);
    }

    public static function start(string $name)
    {
        return new static($name);
    }
}
