<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Addresstrx;
use App\Models\Apikey;
use App\Models\Cronhit;
use App\Models\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Settingtrx;

class Autoreceive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:autoreceive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $apikey = Apikey::find(1);
        $settingtrx = Settingtrx::find(1);

        if ($settingtrx->receiver_status == '1') {
            //first check if have any order then on the trx address add trx
            $order = Order::where('status', '1')
                ->where('autoreceive', '0')
                ->first();
            if (isset($order)) {
                $addresstrx = Addresstrx::find($order->txid);

                try {
                    $client = new Client();
                    $response = $client->post($apikey->base_url.'api/usdt-balance?address=' . $addresstrx->address_base58 . '&apikey=' . $apikey->apikey);
                    $responseBody = $response->getBody()->getContents();
                    $status = $response->getStatusCode();
                    $balance = json_decode($responseBody, true);

                    if ($balance > 1) {
                        $apikey = $apikey->apikey;
                        $sender_address = $settingtrx->receiver_address;
                        $sender_privatekey = $settingtrx->receiver_privatekey;
                        $receiver_address = $addresstrx->address_base58;
                        $energy = $settingtrx->energy;

                        $client = new Client();
                        $response = $client->post($apikey->base_url.'api/trx-transfer?apikey=' . $apikey . '&sender_address=' . $sender_address . '&sender_privatekey=' . $sender_privatekey . '&receiver_address=' . $receiver_address . '&amount=' . $energy);
                        $responseBody = $response->getBody()->getContents();
                        $status = $response->getStatusCode();
                        $data = json_decode($responseBody);
                    }
                    $order->amount = $balance;
                    $order->autoreceive = '1';
                    $order->save();
                } catch (Exception $e) {
                    $order->autoreceive = '3';
                    $order->save();

                    Log::channel('single')->info($e->getMessage());
                }
            }

            //now after 5 minute transfer the usdt
            $currentDateTime = now();
            $fiveMinutesAgo = $currentDateTime->subMinutes(2);
            $orderusdt = Order::where('status', '1')
                ->where('autoreceive', '1')
                ->where('updated_at', '<', $fiveMinutesAgo)
                ->first();

            if (isset($orderusdt)) {
                try {
                    $addresstrx = Addresstrx::find($orderusdt->txid);

                    $apikey = $apikey->apikey;
                    $sender_privatekey = $addresstrx->private_key;
                    $receiver_address = $settingtrx->receiver_address;
                    $amount = $orderusdt->amount;

                    $client = new Client();

                    $response = $client->post($apikey->base_url.'/api/usdt-transfer?address=' . $receiver_address . '&amount=' . $amount . '&apikey=' . $apikey . '&sender_privatekey=' . $sender_privatekey);

                    $responseBody = $response->getBody()->getContents();
                    $status = $response->getStatusCode();
                    $data = json_decode($responseBody);

                    $orderusdt->autoreceive = '2';
                    $orderusdt->save();
                } catch (Exception $e) {
                    $orderusdt->autoreceive = '3';
                    $orderusdt->save();

                    Log::channel('single')->info($e->getMessage());
                }
            }
        }
    }


}
