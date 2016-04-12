<?php

namespace Nodefortytwo\DynamicLogHandler;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;

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
        $record['context']['hostname'] = gethostname();

        if (isset($_ENV['APP_ENV'])) {
            $record['context']['environment'] = $_ENV['APP_ENV'];
        }

        $this->send($record);

    }

    protected function initGuzzle($uri, $proxy): GuzzleClient
    {
        $client = new GuzzleClient(['base_uri' => $uri]);
        return $client;
    }

    protected function send(array $record)
    {
        //fire and forget
        $promise = $this->guzzle->requestAsync('POST', $this->endpoint, [
            'body' => json_encode($record),
        ]);
        $promise->then(
            function (ResponseInterface $res) {},
            function (RequestException $e) {}
        );

    }
}
