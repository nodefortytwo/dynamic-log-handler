<?php

namespace Nodefortytwo\DynamicLogHandler;

use GuzzleHttp\Client as GuzzleClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class Handler extends AbstractProcessingHandler
{

    protected $endpoint = '/api/event';
    public $guzzle;

    public function __construct(string $uri, string $proxy = null, $level = Logger::DEBUG, $bubble = true)
    {
        $this->uri   = $uri;
        $this->proxy = $proxy;

        $this->guzzle = $this->initGuzzle($uri, $proxy);

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        unset($record['formatted']);
        $this->send($record);
    }

    protected function initGuzzle($uri, $proxy): GuzzleClient
    {
        $client = new GuzzleClient(['base_uri' => $uri]);
        return $client;
    }
    //TODO: look at UDP or something...
    protected function send(array $record)
    {
        $this->guzzle->request('POST', $this->endpoint, [
            'body' => json_encode($record),
        ]);
    }
}
