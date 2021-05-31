<?php

namespace App\Lib\Electrum;

use App\Lib\Electrum\Notification\AddressNotification;
use App\Lib\Electrum\Notification\HeadersNotification;
use App\Lib\ScriptHash;
use Illuminate\Support\Facades\Log;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

class Client {
    protected $connector;
    protected $loop;
    protected $requestFactory;
    protected $deferred = [];

    protected $streamBuffer = '';
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function countPromises() {
        return count($this->deferred);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function __construct(Connector $connector, LoopInterface $loop, RequestFactory $requestFactory) {
        $this->connector = $connector;
        $this->loop = $loop;
        $this->requestFactory = $requestFactory;
    }

    public function connect($uri) {
        $promise = $this->connector->connect($uri);
        $promise->then(function(ConnectionInterface $connection) {
            $this->connection = $connection;
            $connection->on('data', [$this, 'onData']);
        });

        return $promise;

    }

    public function ping() {
        return $this->request('server.ping');
    }

    public function get_balance($script_hash) {
        return $this->request('blockchain.scripthash.get_balance', [$script_hash]);
    }

    public function subscribe_address($script_hash) {
        return $this->request('blockchain.scripthash.subscribe', [$script_hash]);
    }

    public function subscribe_headers() {
        return $this->request('blockchain.headers.subscribe', []);
    }

    public function getmempool($script_hash) {
        return $this->request('blockchain.scripthash.get_mempool', [$script_hash]);
    }

    public function get_history($script_hash) {
        return $this->request('blockchain.scripthash.get_history', [$script_hash]);
    }

    /**
     * @param Request $request
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    private function sendRequest(Request $request) {
        $result = new Deferred();
        $this->deferred[$request->getId()] = $result;
        $this->sendData($request->write());

        return $result->promise();
    }

    /**
     * @param string $method
     * @param array $params
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    private function request($method, array $params = []) {
        $request = $this->requestFactory->create($method, $params);
        return $this->sendRequest($request);
    }

    public function onData($data) {
        $buffer = $this->streamBuffer . $data;

        $buffer = ltrim($buffer, "\n");

        while (($nextPos = strpos($buffer, "\n"))) {
            $msg = substr($buffer, 0, $nextPos);
            $buffer = substr($buffer, $nextPos);
            if (substr($buffer, -1) == "\n") {
                $buffer = substr($buffer, 1);
            }
            $this->onMessage($msg);
        }

        if (!$buffer) {
            $this->streamBuffer = '';
        } else {
            $this->streamBuffer = $buffer;
        }
    }

    private function sendData($data) {
        return $this->connection->write($data);
    }

    private function onMessage($data) {

        try {
            $response = $this->requestFactory->response($data);
        } catch (\Exception $e) {
            Log::info('Received invalid data from electrum server, closing connection', ['data' => $data]);
            $this->connection->close();
            return false;
        }

        if (isset($this->deferred[$response->getId()])) {
            $this->deferred[$response->getId()]->resolve($response);
            unset($this->deferred[$response->getId()]); // Cleanup resolved promises
        } else {
            $this->connection->emit('message', [$response]);

            if ($response instanceof Request) {
                $params = $response->getParams();

                switch ($response->getMethod()) {
                    case 'blockchain.scripthash.subscribe':
                        if (!isset($params[0]) || !isset($params[1])) {
                            throw new \RuntimeException('Address notification missing address/txid');
                        }

                        $this->getConnection()->emit('blockchain.scripthash.subscribe', [new AddressNotification($params[0], $params[1])]);
                        break;
                    case 'blockchain.headers.subscribe':
                        if (!isset($params[0]["hex"]) || !isset($params[0]["height"])) {
                            throw new \RuntimeException('Headers notification missing height/hex');
                        }

                        $this->getConnection()->emit('blockchain.headers.subscribe', [new HeadersNotification($params[0]["height"], $params[0]["hex"])]);
                        break;
                }
            }

            if($response instanceof ErrorResponse) {
                $this->connection->close();
                Log::info('Closing connection due error on server side', ['code' => $response->getCode(), 'message' => $response->getMessage() ]);
            }
        }
    }

}
