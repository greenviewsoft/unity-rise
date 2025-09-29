<?php

namespace App\Console\Commands;

use App\Models\Apikey;
use Illuminate\Console\Command;
use App\Models\ReferralCommissionLevel;
use App\Models\Deposite;
use App\Models\Info;
use App\Models\Settingtrx;
use App\Models\Sitesetting;
use App\Models\Spinamount;
use App\Models\User;

use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

class WithdrawSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:withdraw';

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
        $settingtrx = Settingtrx::find(1);
        if ($settingtrx->sender_status == '1') {
            $withdraw = Withdraw::where('status', '0')
                ->where('amount', '<', '130')
                // ->where('type', 'basic')
                ->first();

            if (isset($withdraw)) {
                $user = User::find($withdraw->user_id);

                //do ban
                $refers = User::where('refer_code', $user->invitation_code)->count();
                $deposite = Deposite::where('user_id', $user->id)->first();
                if (!isset($deposite) && $refers == 0) {
                    $srcip = Info::where('user_id', $user->id)->first();
                    // $srcip->status = 'off';
                    $srcip->save();

                    // $withdraw->status = '2';
                    $withdraw->save();
                }
            }

            if (isset($withdraw) && $withdraw->status == '0') {
                $sitesetting = Sitesetting::find(1);
                $settingtrx = Settingtrx::find(1);
                $apikey = Apikey::find(1);

                try {
                    $sender_privatekey = $settingtrx->sender_privatekey;
                    $receiver_address = $withdraw->txaddress;
                    $amount = $withdraw->amount;
                    // //transfer balance to user if live
                    if ($sitesetting->development == 'false') {
                        $client = new Client();

                        $response = $client->post($apikey->base_url.'api/usdt-transfer?address=' . $receiver_address . '&amount=' . $amount . '&apikey=' . $apikey->apikey . '&sender_privatekey=' . $sender_privatekey);

                        $responseBody = $response->getBody()->getContents();
                        $status = $response->getStatusCode();
                        $data = json_decode($responseBody);
                        $txid = $data->data->txid;
                    } else {
                        $txid = 'manual';
                    }
                    $withdraw->txid = isset($txid) == true ? $txid : 'demo';
                    $withdraw->status = '1';
                    $withdraw->save();

                    $withdrawalls = Withdraw::where('user_id', $withdraw->user_id)
                        ->where('status', '0')
                        ->get();
                    foreach ($withdrawalls as $withdrawall) {
                        $withdrawall->delete();
                    }
                } catch (Exception $e) {
                    $withdraw->status = '2';
                    $withdraw->save();

                    Log::channel('single')->info($e->getMessage());
                }
            }
        }


    }



}
