<?php

namespace App\Lib;

class ElectrumXClient {
private $socket;
private $host;
private $port;
private $timeout;
private $errno;
private $errstr;

    public function __construct($host, $port, $errno = null, $errstr = null, $timeout = 3)
    {
        $this->timeout = $timeout;
        $this->host = gethostbyname($host);
        $this->port = $port;

        $this->errno = &$errno;
        $this->errstr = &$errstr;
    }

    public function send($method, $params = []) {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        $json_params = \json_encode($params);
        fwrite($this->socket, '{"id": '.rand(100000,1000000000).', "method": "'.$method.'", "params": '.$json_params.'}');
        fwrite($this->socket, "\n");
        fflush($this->socket);
        $response = fgets($this->socket);

        $response_decoded = @\json_decode($response);
        if($response_decoded && @$response_decoded->result) return $response_decoded->result;

        return false;
    }
}