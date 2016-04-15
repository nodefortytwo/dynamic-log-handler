<?php

namespace Nodefortytwo\DynamicLogHandler;

use GuzzleHttp\Client as GuzzleClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class Handler extends AbstractProcessingHandler
{

    protected $endpoint = '/api/event';
    public $guzzle;

    public function __construct(string $uri, string $port = "80", string $proxy = null, $level = Logger::DEBUG, $bubble = true)
    {
        $this->uri   = $uri;
        $this->port  = $port;
        $this->proxy = $proxy;

        //use udp if we aren't targeting 80 or 443, and if we don't have a proxy
        if (!$proxy && $port != "80" && $port != "443") {
            if (!($this->sock = socket_create(AF_INET, SOCK_DGRAM, 0))) {
                $errorcode = socket_last_error();
                $errormsg  = socket_strerror($errorcode);
            }
        }

        //if we don't have a socket, use http
        if (!$this->sock) {
            $proto     = ($port == 443) ? 'https://' : 'http://';
            $this->uri = $proto . $this->uri;

            $this->guzzle = $this->initGuzzle($this->uri, $this->proxy);
        }

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

    protected function send(array $record)
    {
        //if guzzle is setup it means we can't use UDP :(
        if ($this->guzzle) {
            $this->guzzle->request('POST', $this->endpoint, [
                'body' => json_encode($record),
            ]);
        } else {
            $msg = json_encode($record);
            //Send the message to the server
            if (!socket_sendto($this->sock, $msg, strlen($msg), 0, $this->uri, $this->port)) {
                $errorcode = socket_last_error();
                $errormsg  = socket_strerror($errorcode);
            }
        }
    }
}
