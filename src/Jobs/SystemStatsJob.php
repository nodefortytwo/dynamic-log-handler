<?php

namespace Nodefortytwo\DynamicLogHandler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Log;
//System stats
use Nodefortytwo\DynamicLogHandler\System\Io;
use Nodefortytwo\DynamicLogHandler\System\Load;
use Nodefortytwo\DynamicLogHandler\System\Network;

class SystemStatsJob
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "queueOn" and "delay" queue helper methods.
    |
     */

    use InteractsWithQueue, Queueable;

    public $stats = [Load::class, Io::class, Network::class];

    public function handle()
    {
        $stats = [];

        foreach ($this->stats as $class) {
            $stats += $class::collect();
        }

        Log::info('system_info', $stats);
    }
}
