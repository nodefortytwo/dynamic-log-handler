<?php
namespace Nodefortytwo\DynamicLogHandler;

class AwsProcessor
{
    protected $keys = [

    ];

    public function __construct(array $keys = null)
    {
        if ($keys) {
            $this->keys = $keys;
        }
    }

    public function __invoke(array $record)
    {
        //TODO
        $aws = [];

        $record['extra']['aws'] = $aws;

        return $record;
    }
}
