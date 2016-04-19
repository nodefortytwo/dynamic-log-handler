<?php
namespace Nodefortytwo\DynamicLogHandler;

class EnvironmentProcessor
{
    protected $keys = [
        'APP_URL',
        'APP_ENV',
        'APP_NAME',
        'DB_HOST',
        'DB_NAME',
        'PROXY_ADDRESS',
    ];

    public function __construct(array $keys = null)
    {
        if ($keys) {
            $this->keys = $keys;
        }
    }

    public function __invoke(array $record)
    {
        //we are just going to pick a few things out of the .env
        $env = [];
        foreach ($this->keys as $key) {
            $env[$key] = env($key);
        }

        $record['extra']['environment'] = $env;

        return $record;
    }
}
