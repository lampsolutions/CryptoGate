<?php

namespace App\Lib\Stratum;

use React\Socket\Connection;
use React\Socket\ConnectorInterface;

class Client {
    private $connector;
    private $requestFactory;

    public function __construct(ConnectorInterface $connector, RequestFactory $requestFactory) {
        $this->connector = $connector;
        $this->requestFactory = $requestFactory;
    }

    public function connect($host, $port, $loop) {
        $uri = $host.':'.$port;

        return $this->connector->connect($uri)->then(function (\React\Promise\PromiseInterface $stream) {
            return new \App\Lib\Stratum\Connection($stream, $loop, $this->requestFactory);
        }, function (\Exception $e) {
            throw $e;
        });
    }
}