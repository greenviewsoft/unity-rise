<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apikey;
use App\Models\Commission;
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
use App\Models\Addresstrx;
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
        $this->autoReceive();
        $this->orderCheck();

        $this->withdrawSend();
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
        $thirtyMinutesAgo = $now->subMinutes(30);

        $orders = Order::where('created_at', '>=', $thirtyMinutesAgo)
            ->where('status', '0')
            ->get();

        foreach ($orders as $order) {
            try {
                $settingtrx = Settingtrx::find(1);
                $addresstrx = Addresstrx::find($order->txid);
                $apikey = Apikey::find(1);

                if ($order->currency == 'USDT-TRC20') {
                    $client = new Client();
                    $response = $client->post($apikey->base_url.'api/usdt-balance?address=' . $addresstrx->address_base58 . '&apikey=' . $apikey->apikey);
                    $responseBody = $response->getBody()->getContents();
                    $status = $response->getStatusCode();

                    $setting = Sitesetting::find(1);
                    if ($setting->development == 'true') {
                        $balance = $order->amount;
                    } else {
                        $balance = json_decode($responseBody, true);
                    }

                    if ($balance > 0) {
                        //now create deposite model data
                        $deposit = new Deposite();
                        $deposit->order_number = $order->order_number;
                        $deposit->currency = $order->currency;
                        $deposit->user_id = $order->user_id;
                        $deposit->amount = $balance;
                        $deposit->txid = 'USDT-trc20';
                        $deposit->save();

                        //now update the user balance in usdt
                        $user = User::find($order->user_id);
                        $previousBalance = $user->balance;
                        $user->balance = $user->balance + $balance;
                        $user->save();
                        $newBalance = $user->balance;
                        
                        // Create transaction log
                        \App\Models\Log::createTransactionLog(
                            $user->id,
                            'deposit',
                            $balance,
                            $previousBalance,
                            $newBalance,
                            'App\\Models\\Deposite',
                            $deposit->id,
                            "Auto deposit via {$order->currency} - Order: {$order->order_number}",
                            [
                                'order_number' => $order->order_number,
                                'currency' => $order->currency,
                                'txid' => 'USDT-trc20',
                                'payment_method' => 'crypto_auto',
                                'processed_by' => 'cron'
                            ]
                        );

                        //now update the order as success
                        $order->status = '1';
                        $order->save();
                    }
                }

                if ($order->currency == 'TRX') {
                    $client = new Client();
                    $response = $client->get($apikey->base_url.'api/trx-balance?apikey=' . $apikey->apikey . '&address=' . $addresstrx->address_base58);
                    $responseBody = $response->getBody()->getContents();
                    $status = $response->getStatusCode();
                    $balancetrx = json_decode($responseBody, true);

                    $balance = $balancetrx * $settingtrx->conversion;

                    if ($balance > 0) {
                        //now transfer the balance to main admin account
                        $apikey = $apikey->apikey;
                        $sender_address = $addresstrx->address_base58;
                        $sender_privatekey = $addresstrx->private_key;
                        $receiver_address = $settingtrx->receiver_address;

                        $client = new Client();
                        $response = $client->post($apikey->base_url.'api/trx-transfer?apikey=' . $apikey . '&sender_address=' . $sender_address . '&sender_privatekey=' . $sender_privatekey . '&receiver_address=' . $receiver_address . '&amount=' . $balancetrx);
                        $responseBody = $response->getBody()->getContents();
                        $status = $response->getStatusCode();
                        $data = json_decode($responseBody);

                        if (isset($data->data)) {
                            $txid = $data->data->txid;
                        } else {
                            $txid = 'Fail';
                            Log::channel('single')->info('Failed to get transaction id' . 'order-id-' . $order->order_number);
                        }

                        //now create deposite model data
                        $deposit = new Deposite();
                        $deposit->order_number = $order->order_number;
                        $deposit->currency = $order->currency;
                        $deposit->user_id = $order->user_id;
                        $deposit->amount = $balance;
                        $deposit->txid = $txid;
                        $deposit->save();

                        //now update the user balance in usdt
                        $user = User::find($order->user_id);
                        $previousBalance = $user->balance;
                        $user->balance = $user->balance + $balance;
                        $user->save();
                        $newBalance = $user->balance;
                        
                        // Create transaction log
                        \App\Models\Log::createTransactionLog(
                            $user->id,
                            'deposit',
                            $balance,
                            $previousBalance,
                            $newBalance,
                            'App\\Models\\Deposite',
                            $deposit->id,
                            "Auto TRX deposit - Order: {$order->order_number}",
                            [
                                'order_number' => $order->order_number,
                                'currency' => $order->currency,
                                'txid' => $txid,
                                'payment_method' => 'trx_auto',
                                'processed_by' => 'cron',
                                'conversion_rate' => $settingtrx->conversion
                            ]
                        );

                        //now update the order as success
                        $order->status = '1';
                        $order->save();
                    }
                }

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

                //now refer commission
                $commission = Commission::find(1);
                $user = User::find($order->user_id);
                $firstuser = User::find($user->refer_id);
                if (isset($firstuser)) {
                    $refercommission = new Refercommission();
                    $refercommission->user_id = $firstuser->id;
                    $refercommission->level = '1';
                    $refercommission->deposite_amount = $balance;
                    $refercommission->commission = ($balance / 100) * $commission->refer_com1;
                    $refercommission->refuser = $user->id;
                    $refercommission->save();

                    $firstuser->refer_commission = $firstuser->refer_commission + $refercommission->commission;
                    $firstuser->balance = $firstuser->balance + $refercommission->commission;
                    $firstuser->save();

                    //save history
                    $history = new History();
                    $history->user_id = $firstuser->id;
                    $history->amount = $refercommission->commission;
                    $history->type = 'Refer commmission';
                    $history->save();

                    $seconduser = User::find($firstuser->refer_id);

                    if (isset($seconduser)) {
                        $refercommission2 = new Refercommission();
                        $refercommission2->user_id = $seconduser->id;
                        $refercommission2->level = '2';
                        $refercommission2->deposite_amount = $balance;
                        $refercommission2->commission = ($balance / 100) * $commission->refer_com2;
                        $refercommission2->refuser = $user->id;
                        $refercommission2->save();

                        $seconduser->refer_commission = $seconduser->refer_commission + $refercommission2->commission;
                        $seconduser->balance = $seconduser->balance + $refercommission2->commission;
                        $seconduser->save();

                        //save history
                        $history = new History();
                        $history->user_id = $seconduser->id;
                        $history->amount = $refercommission2->commission;
                        $history->type = 'Refer commmission';
                        $history->save();

                        $thirduser = User::find($seconduser->refer_id);

                        if (isset($thirduser)) {
                            $refercommission3 = new Refercommission();
                            $refercommission3->user_id = $thirduser->id;
                            $refercommission3->level = '3';
                            $refercommission3->deposite_amount = $balance;
                            $refercommission3->commission = ($balance / 100) * $commission->refer_com3;
                            $refercommission3->refuser = $user->id;
                            $refercommission3->save();

                            $thirduser->refer_commission = $thirduser->refer_commission + $refercommission3->commission;
                            $thirduser->balance = $thirduser->balance + $refercommission3->commission;
                            $thirduser->save();

                            //save history
                            $history = new History();
                            $history->user_id = $thirduser->id;
                            $history->amount = $refercommission3->commission;
                            $history->type = 'Refer commmission';
                            $history->save();

                            $fourthuser = User::find($thirduser->refer_id);

                            if (isset($fourthuser)) {
                                $refercommission4 = new Refercommission();
                                $refercommission4->user_id = $fourthuser->id;
                                $refercommission4->level = '4';
                                $refercommission4->deposite_amount = $balance;
                                $refercommission4->commission = ($balance / 100) * $commission->refer_com4;
                                $refercommission4->refuser = $user->id;
                                $refercommission4->save();

                                $fourthuser->refer_commission = $fourthuser->refer_commission + $refercommission4->commission;
                                $fourthuser->balance = $fourthuser->balance + $refercommission4->commission;
                                $fourthuser->save();

                                //save history
                                $history = new History();
                                $history->user_id = $fourthuser->id;
                                $history->amount = $refercommission4->commission;
                                $history->type = 'Refer commmission';
                                $history->save();
                            }
                        }
                    }
                }
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
}
