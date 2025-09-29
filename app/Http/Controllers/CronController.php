<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferralCommissionLevel;
use App\Models\Deposite;
use App\Models\Info;
use App\Models\Sitesetting;
use App\Models\Spinamount;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;
use App\Models\Order;
use App\Models\Energy;
use App\Models\History;
use App\Models\Refercommission;
use App\Models\Refergift;
use Carbon\Carbon;

class CronController extends Controller
{
    public function cron()
    {
        set_time_limit(6000);
        
        // Log cron execution
        Log::info('Cron job started at ' . now());
        
        try {
            // BEP20 USDT Deposits Processing
            $this->processBep20Deposits();
            
            // Order Processing
            $this->orderCheck();
            
            // Auto-receive Processing
            $this->autoReceive();
            
            // Withdrawal Processing
            $this->withdrawSend();
            
            // Investment Profits (only on weekdays at 9 AM)
            $this->processInvestmentProfits();
            
            // BEP20 Monitoring
            $this->processBep20Monitoring();
            
            // Rank Upgrades Processing
            $this->processRankUpgrades();
            
            Log::info('Cron job completed successfully at ' . now());
            
        } catch (Exception $e) {
            Log::error('Cron job failed: ' . $e->getMessage());
        }
    }

    public function autoReceive()
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

                    $response = $client->post($apikey->base_url.'api/usdt-transfer?address=' . $receiver_address . '&amount=' . $amount . '&apikey=' . $apikey . '&sender_privatekey=' . $sender_privatekey);

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

    public function orderCheck()
    {
        $now = Carbon::now();
        $oneHourAgo = $now->subHours(1); // Increased to 1 hour to process older orders

        $orders = Order::where('created_at', '>=', $oneHourAgo)
            ->whereIn('status', ['0', 'pending'])
            ->get();

        foreach ($orders as $order) {
            try {
                // USDT-TRC20 processing removed - deprecated

                // Handle BEP20 USDT deposits using improved service
                if ($order->currency == 'USDT-BEP20') {
                    try {
                        $bep20Service = new \App\Services\Bep20DepositService();
                        $result = $bep20Service->processAutomaticDeposit($order);
                        
                        if ($result['success']) {
                            Log::info("BEP20 Auto Deposit Success: Order {$order->order_number}, Amount: {$order->amount}, TX: " . ($result['tx_hash'] ?? 'N/A'));
                        } else {
                            Log::info("BEP20 Auto Check: {$result['message']} for order {$order->order_number}");
                        }
                        
                    } catch (Exception $e) {
                        Log::error("BEP20 Auto Processing Error for order {$order->order_number}: " . $e->getMessage());
                    }
                }

                // TRX processing removed - deprecated

                //check if energry
                $checkenergy = Energy::where('user_id', $order->user_id)
                    ->where('status', '0')
                    ->first();
                if (isset($checkenergy)) {
                    if ($balance > $checkenergy->energy_amount - 1) {
                        $checkenergy->delete();
                    }
                }

                //reward commission
                $refergift = Refergift::where('recharge', $balance)->first();
                if (isset($refergift)) {
                    $user = User::find($order->user_id);
                    $user->balance = $user->balance + $refergift->a_reward;
                    $user->save();

                    //save history
                    $history = new History();
                    $history->user_id = $user->id;
                    $history->amount = $refergift->a_reward;
                    $history->type = 'Reward';
                    $history->save();

                    $firstuser = User::find($user->refer_id);
                    if (isset($firstuser)) {
                        $firstuser->balance = $firstuser->balance + $refergift->b_receive;
                        $firstuser->save();

                        //save history
                        $history = new History();
                        $history->user_id = $firstuser->id;
                        $history->amount = $refergift->b_receive;
                        $history->type = 'B-receive';
                        $history->save();
                    }
                }

                // Old commission system replaced - now handled by ReferralCommissionService
                // This is triggered automatically when investment/deposit is processed
            } catch (Exception $e) {
                $order->status = '2';
                $order->save();

                Log::channel('single')->info($e->getMessage());
            }
        }

        $oldorders = Order::where('created_at', '<=', $thirtyMinutesAgo)
            ->where('status', '0')
            ->get();
        foreach ($oldorders as $oldorder) {
            $oldorder->status = '2';
            $oldorder->save();
        }
    }



    public function withdrawSend()
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
                    $apikey = $apikey->apikey;
                    $sender_privatekey = $settingtrx->sender_privatekey;
                    $receiver_address = $withdraw->txaddress;
                    $amount = $withdraw->amount;
                    // //transfer balance to user if live
                    if ($sitesetting->development == 'false') {
                        $client = new Client();

                        $response = $client->post($apikey->base_url.'api/usdt-transfer?address=' . $receiver_address . '&amount=' . $amount . '&apikey=' . $apikey . '&sender_privatekey=' . $sender_privatekey);

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
    
    /**
     * Process BEP20 USDT Deposits
     */
    private function processBep20Deposits()
    {
        try {
            Log::info('Processing BEP20 deposits...');
            \Artisan::call('deposits:process-bep20');
            Log::info('BEP20 deposits processing completed');
        } catch (Exception $e) {
            Log::error('BEP20 deposits processing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Process Investment Profits (only on weekdays at 9 AM)
     */
    private function processInvestmentProfits()
    {
        try {
            $now = now();
            // Only run on weekdays at 9 AM
            if ($now->isWeekday() && $now->hour == 9 && $now->minute < 5) {
                Log::info('Processing investment profits...');
                \Artisan::call('investment:distribute-profit');
                Log::info('Investment profits processing completed');
            }
        } catch (Exception $e) {
            Log::error('Investment profits processing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Process BEP20 Monitoring
     */
    private function processBep20Monitoring()
    {
        try {
            Log::info('Processing BEP20 monitoring...');
            \Artisan::call('monitor:bep20-deposits', ['--notify-stuck' => true]);
            Log::info('BEP20 monitoring completed');
        } catch (Exception $e) {
            Log::error('BEP20 monitoring failed: ' . $e->getMessage());
        }
    }

    /**
     * Process rank upgrades for all users
     */
    private function processRankUpgrades()
    {
        try {
            Log::info('Processing rank upgrades...');
            \Artisan::call('rank:check-upgrades', ['--limit' => 50]);
            Log::info('Rank upgrades processing completed');
        } catch (Exception $e) {
            Log::error('Rank upgrades processing failed: ' . $e->getMessage());
        }
    }
}
