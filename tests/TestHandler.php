<?php

use Monolog\Logger;
use Nodefortytwo\DynamicLogHandler\Handler as DynamicHandler;

class TestHandler extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->log = new Logger('test');
        $this->log->pushHandler(new DynamicHandler('127.0.0.1'));
    }

    public function testInfo()
    {
        $this->log->addInfo('info');
    }

}
