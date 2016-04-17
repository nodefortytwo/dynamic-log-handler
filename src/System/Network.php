<?php

namespace Nodefortytwo\DynamicLogHandler\System;

class Network
{
    static $samples_count = 2;

    public static function collect()
    {
        $samples = [];
        while (count($samples) < static::$samples_count) {
            $samples[] = static::get();
            sleep(1);
        }

        $diffs = [];

        for ($i = 0; $i < count($samples) - 1; $i++) {
            $first  = $samples[$i];
            $second = $samples[$i + 1];
            $diff   = [];
            foreach ($first as $interface => $stats) {
                $diff[$interface] = [
                    'rx' => $first[$interface]['rx'] - $second[$interface]['rx'],
                    'tx' => $first[$interface]['tx'] - $second[$interface]['tx'],
                ];
            }

            $diffs[] = $diff;
        }

        if (count($diffs) == 0) {
            $average = $diffs[0];
        } else {
            $total = [];
            foreach ($diffs as $diff) {
                foreach ($diff as $interface => $stats) {
                    if (!isset($total[$interface]['rx'])) {
                        $total[$interface]['rx'] = 0;
                    }
                    if (!isset($total[$interface]['tx'])) {
                        $total[$interface]['tx'] = 0;
                    }
                    $total[$interface]['rx'] += $stats['rx'];
                    $total[$interface]['tx'] += $stats['tx'];
                }
            }

            foreach ($total as $interface => $stats) {
                $total[$interface]['rx'] = $total[$interface]['rx'] / count($diffs);
                $total[$interface]['tx'] = $total[$interface]['tx'] / count($diffs);
            }
            $average = $total;
        }

        return ['network' => $average];
    }

    public static function get()
    {
        $contents = explode(PHP_EOL, trim(file_get_contents('/proc/net/dev')));

        $contents = array_map(function ($line) {
            return preg_split('/\s+/', $line);
        }, $contents);

        $contents = array_slice($contents, 2);

        $interfaces = [];
        foreach ($contents as $line) {

            $interfaces[str_replace(':', '', $line[1])] = [
                'rx' => $line[2],
                'tx' => $line[10],
            ];
        }

        return $interfaces;
    }
}
