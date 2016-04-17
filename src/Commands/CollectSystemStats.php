<?php
namespace Nodefortytwo\DynamicLogHandler\Commands;

use Illuminate\Console\Command;
use Nodefortytwo\DynamicLogHandler\Jobs\SystemStatsJob;

class CollectSystemStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logger:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'trigger job to colllect system status like load, and send to logger';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new SystemStatsJob);
    }
}
