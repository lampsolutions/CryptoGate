<?php

namespace App\Lib;

use App\Services\HierarchicalDeterministicWalletService;
use App\Services\PaymentRequestService;
use App\Wallet;
use function Denpa\Bitcoin\to_fixed;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use React\Socket\TcpServer;

class StratumServer {

    protected $walletService;

    protected function to_satoshi($bitcoin) : string {
        return bcmul($this->to_fixed((float) $bitcoin, 8), (string) 1e8);
    }

    protected function to_fixed(float $number, int $precision = 8) : string {
        $number = $number * pow(10, $precision);

        return bcdiv((string) $number, (string) pow(10, $precision), $precision);
    }

    protected function to_bitcoin(int $satoshi) : string {
        return bcdiv((string) $satoshi, (string) 1e8, 8);
    }

    public function addrequest($params) {
        $prs = new PaymentRequestService($this->walletService, $this->walletService->getWallet());
        $response = $prs->addRequest($this->to_satoshi($params->amount), $params->memo, $params->expiration);
        if(!$response) return false;

        $address = $response->address->address;

        return [
            'id' => $response->id,
            'address' => $address,
            'amount' => $response->amount,
            'expiration' => strtotime($response->expires_at),
            'URI' => $response->buildPaymentUri()
        ];

    }

    public function handleHttpRequest(\Psr\Http\Message\ServerRequestInterface $request) {
        $payload = [];

        try {
            $query = json_decode($request->getBody());

            switch($query->method) {
                case 'addrequest':
                    $payload = $this->addrequest($query->params);
                    break;
                case 'check':
                    $payload = [
                        'id' => '1',
                        'status' => 'OK'
                    ];
                    break;
            }


            $response = [
                'jsonrpc' => $query->jsonrpc,
                'id' => $query->id,
                'result' => $payload
            ];

            return new \React\Http\Message\Response(
                200,
                array(
                    'Content-Type' => 'text/plain'
                ),
                json_encode($response)."\n"
            );

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }



        return new \React\Http\Message\Response(
            200,
            array(
                'Content-Type' => 'text/plain'
            ),
            "\n"
        );
    }


    public function __construct(HierarchicalDeterministicWalletService $walletService, LoopInterface $loop) {
        $this->walletService = $walletService;

        $server = new \React\Http\Server($loop, function (\Psr\Http\Message\ServerRequestInterface $request) {
            return $this->handleHttpRequest($request);
        });

        $port = 7770;
        $port = $walletService->getWallet()->coin == 'BTC' ? 7771 : $port;
        $port = $walletService->getWallet()->coin == 'DASH' ? 7772 : $port;
        $port = $walletService->getWallet()->coin == 'LTC' ? 7773 : $port;
        $port = $walletService->getWallet()->coin == 'BCH' ? 7774 : $port;

        $socket = new Server($port, $loop);
        $server->listen($socket);
    }
}
