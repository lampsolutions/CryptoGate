<?php

namespace App\Console\Commands;

use App\Lib\StratumServer;
use App\Services\HierarchicalDeterministicWalletService;
use App\Services\PaymentRequestService;
use App\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use React\EventLoop\Factory;
use React\EventLoop\TimerInterface;

class Daemon extends Command {

    protected $signature = 'cryptogate:daemon';
    protected $description = 'Wallet Manager Daemon';
    protected $loop;
    protected $wallets;

    public function __construct() {
        parent::__construct();
        $this->wallets = new \SplObjectStorage();
        $this->loop = $loop = Factory::create();
    }

    protected function loadWalletService(Wallet $wallet) {
        Log::info('Loading Wallet Service for Wallet', ['walletid' => $wallet->id]);

        $requestFactory = new \App\Lib\Electrum\RequestFactory();
        $connector = new \React\Socket\Connector($this->loop, array(
            'timeout' => 300,
            'tls' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            ),
        ));
        $client = new \App\Lib\Electrum\Client($connector, $this->loop, $requestFactory);

        $client->connect($wallet->server_dsn)->then(

            function() use ($client, $wallet) {
                Log::info('Connected to Electrum Service for Wallet', ['walletid' => $wallet->id]);
                try {
                    $hdWalletService = new HierarchicalDeterministicWalletService($wallet, $client);
                    $prs = new PaymentRequestService($hdWalletService, $wallet);
                    $hdWalletService->synchronize()->then(function() use($hdWalletService, $wallet, $prs) {
                        $prs->synchronize();
                    });
                } catch (\Exception $exception) {
                    Log::info('Exception in Wallet init', ['exceptionTrace' => $exception->getTraceAsString()]);
                }

                // Check Synchronization State after 600 seconds
                $this->loop->addTimer(600, function() use($hdWalletService, $prs, $wallet, $client) {
                    if(!$hdWalletService->synchronized) {
                        Log::info('Wallet Service not synchronized for Wallet, closing connection', ['walletid' => $wallet->id]);
                        // Close connection and wait for Timeout Timer to cleanup
                        $client->getConnection()->close();
                    }

                    if(!$prs->synchronized) {
                        Log::info('Payment Service not synchronized for Wallet, closing connection', ['walletid' => $wallet->id]);
                        // Close connection and wait for Timeout Timer to cleanup
                        $client->getConnection()->close();
                    }
                });

                // Handle Timeouts
                $this->loop->addPeriodicTimer(5, function(TimerInterface $timer) use($client, $wallet) {
                    if(!$client->getConnection()->isWritable() && !$client->getConnection()->isReadable() ) {
                        Log::info('Closing Connection to Electrum Server for Wallet, restarting for Wallet', ['walletid' => $wallet->id]);
                        $this->loop->cancelTimer($timer);

                        // Delay reconnect for failover
                        $delay = rand(10, 300);
                        Log::info("Planing reconnect to Electrum Serrver for Wallet in ${delay} seconds, restarting for Wallet", ['walletid' => $wallet->id]);
                        $this->loop->addTimer($delay, function() use($wallet) {
                            Log::info('Reconnecting now ...', ['walletid' => $wallet->id]);
                            $this->loadWalletService($wallet);
                        });

                        // Cleanup
                        try {
                            $client->getConnection()->close();
                        } catch (\Exception $e) {
                            var_dump($e->getTraceAsString());
                        }
                    }
                });

                $this->loop->addPeriodicTimer(150, function(TimerInterface $timer) use($client, $wallet) {
                    if(!$client->getConnection()->isWritable() && !$client->getConnection()->isReadable() ) {
                        Log::info('Pinging Electrum Server for Wallet failed, removing timer', ['walletid' => $wallet->id]);
                        $this->loop->cancelTimer($timer);
                    } else {
                        Log::info('Pinging Electrum Server for Wallet', ['walletid' => $wallet->id]);
                        $client->ping();
                    }
                });

                $server = new StratumServer($hdWalletService, $this->loop);
            },
            function(\Exception $exception) use($client, $wallet) { // Handle Connection reset
                $delay = rand(10, 300);
                Log::info("Failed Connecting to Electrum Server for Wallet. Delaying Connect ${delay} Seconds...", ['walletid' => $wallet->id]);
                $this->loop->addTimer($delay, function() use($wallet) {
                    $this->loadWalletService($wallet);
                });
            });
    }

    public function handleIntegration() {
        $coins = [
            1 => 'BTC',
            2 => 'LTC',
            3 => 'DASH',
            4 => 'BCH'
        ];

        foreach($coins as $id => $coin) {
            if(!empty(env($coin.'_PUBKEY'))) {
                $wallet = Wallet::updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                        'coin' => $coin,
                        'gap_limit' => 100,
                        'public_key' => env($coin.'_PUBKEY'),
                        'network' => env($coin.'_MODE'),
                        'height' => 0,
                        'server_dsn' => env($coin.'_DSN'),
                    ]
                );

            }
        }

    }

    public function handle() {

        $this->handleIntegration();

        Log::info('Loading CryptoGate2 Daemon');

        try {
            foreach(Wallet::all() as $wallet) {
                $this->loadWalletService($wallet);
            }
        } catch (\Exception $exception) {
            exit();
        }

        // Add restart Timer for whole daemon
        $restartTime = rand(48, 96) * 60 * 60;
        $this->loop->addTimer( $restartTime , function() use($wallet) {
            Log::info('Restarting CryptoGate2 Daemon');
            exit();
        });

        try {
            $this->loop->run();
        } catch (\Exception $exception) {
            exit();
        }

        return 0;
    }
}
