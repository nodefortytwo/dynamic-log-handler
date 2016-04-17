<?php

namespace Nodefortytwo\DynamicLogHandler\System;

class Load
{
    public static function collect()
    {

        $processors = count(CpuInfo::get());

        $loadavg = explode(' ', file_get_contents('/proc/loadavg'));
        $loadavg = array_slice($loadavg, 0, 3);

        $loadavg = array_map(function ($item) use ($processors) {
            return $item * 100 / $processors;
        }, $loadavg);

        return ['loadavg' => $loadavg];
    }
}
