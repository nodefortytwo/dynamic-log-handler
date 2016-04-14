<?php

use Monolog\Logger;
use Nodefortytwo\DynamicLogHandler\Handler as DynamicHandler;

class TestHandler extends \PHPUnit_Framework_TestCase
{

    public function setupLoggers()
    {
        $udp = new Logger('test');
        $udp->pushHandler(new DynamicHandler('127.0.0.1', '50000'));

        $http = new Logger('test');
        $http->pushHandler(new DynamicHandler('127.0.0.1', '80', 'someproxy'));

        $https = new Logger('test');
        $https->pushHandler(new DynamicHandler('127.0.0.1', '443', 'someproxy'));

        return [
            [$udp],
            [$http],
            [$https],
        ];
    }

    /**
     * @dataProvider setupLoggers
     */
    public function testInfo($logger)
    {
        //im not sure what to actually test
    }

}
