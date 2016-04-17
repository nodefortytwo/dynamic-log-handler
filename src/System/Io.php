<?php

namespace Nodefortytwo\DynamicLogHandler\System;

class Io
{
    public static function collect()
    {
        $contents = explode(PHP_EOL, trim(file_get_contents('/proc/diskstats')));

        $contents = array_map(function ($line) {
            return preg_split('/\s+/', $line);
        }, $contents);

        $contents = array_filter($contents, function ($line) {
            if ($line[4] == 0 && $line[8] == 0 && $line[12] == 0 && $line[13] == 0) {
                return false;
            }
            return true;
        });

        $devices = [];
        foreach ($contents as $line) {

            $devices[$line[3]] = [
                'reads'       => $line[4],
                'writes'      => $line[8],
                'in_progress' => $line[12],
                'time_in_io'  => $line[13],
            ];
        }

        return ['io' => $devices];
    }
}
