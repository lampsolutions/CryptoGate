<?php

namespace App\Console\Commands;

use App\Facades\Electrum;
use App\Lib\BIP32PublicKey;
use App\Wallet;
use Illuminate\Console\Command;

class CheckSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptogate:checkSystem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Daemon Service Uptime';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {

        try {
            // Iterate configured wallets and check for connected service
            foreach(Wallet::all() as $wallet) {
                $client = Electrum::client($wallet->coin);
                $response = $client->send($client->request(sha1(microtime()), 'check', []));
                $error_code = $response->getRpcErrorCode();

                if(!is_null($error_code)) {
                    exit(1);
                }
            }
        } catch (\Exception $e) {
            exit(1);
        }

        exit(0);

    }
}
