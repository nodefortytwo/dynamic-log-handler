<?php

namespace Nodefortytwo\DynamicLogHandler\System;

class CpuInfo
{
    public static function collect()
    {

    }

    public static function get()
    {
        $contents   = explode(PHP_EOL, trim(file_get_contents('/proc/cpuinfo')));
        $processors = [];

        foreach ($contents as $line) {
            if (empty($line)) {
                continue;
            }
            $line = explode(':', $line);
            $line = array_map('trim', $line);

            if ($line[0] == 'processor') {
                $processor_id = $line[1];
                continue;
            }

            $processors[$processor_id][$line[0]] = $line[1];

        }

        return $processors;
    }
}
