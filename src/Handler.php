<?php

namespace Nodefortytwo\DynamicLogHandler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class Handler extends AbstractProcessingHandler
{

    protected $endpoint = '/api/event';
    public $guzzle;
    public $sock;

    public function __construct(string $uri, string $proxy = null, $level = Logger::DEBUG, $bubble = true)
    {

        $this->uri_parts = static::parseUrl($uri);

        //i have no idea if this will work but i'm going
        //to send the request to the "$proxy" with headers
        //matching the uri, hopefully that will allow squid to
        //forward it...
        if (!$proxy) {
            $this->proxy_parts = $this->uri_parts;
        } else {
            $this->proxy_parts = static::parseUrl($proxy);
        }

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        unset($record['formatted']);
        $this->send($record);
    }

    protected function send(array $record)
    {
        $content = json_encode($record);
        $headers = static::getHeaders($this->uri_parts['host'], $this->endpoint, $content);
        $request = $headers . "\r\n" . $content;

        $host = $this->proxy_parts['host'];
        $port = $this->proxy_parts['port'];

        $socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$socket) {
            return;
        }

        fwrite($socket, $request);
        fclose($socket);
    }

    protected static function getHeaders($host, $path, $content)
    {
        $headers = "POST " . $path . " HTTP/1.1\r\n";
        $headers .= "Host: " . $host . "\r\n";
        $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $headers .= "Content-Length: " . strlen($content) . "\r\n";
        $headers .= "Connection: Close\r\n";
        return $headers;
    }

    protected static function parseUrl(string $url): array
    {
        $url = parse_url($url);

        if (!isset($url['scheme'])) {
            if (isset($url['port'])) {
                switch ($url['port']) {
                    case 443:
                        $url['scheme'] = 'ssl';
                        break;
                    case 80:
                    default:
                        $url['scheme'] = 'http';
                }
            } else {
                $url['scheme'] = 'http';
                $url['port']   = 80;
            }
        }

        if (!isset($url['port'])) {
            switch ($url['scheme']) {
                case 'ssl':
                case 'https':
                    $url['port'] = 443;
                    break;
                default:
                    $url['port'] = 80;
            }
        }

        return $url;
    }
}
