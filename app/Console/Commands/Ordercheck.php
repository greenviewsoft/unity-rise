<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Addresstrx;
use App\Models\Apikey;
use App\Models\Commission;
use App\Models\Deposite;
use App\Models\Energy;
use App\Models\History;
use App\Models\Order;
use App\Models\Refercommission;
use App\Models\Refergift;
use App\Models\Settingtrx;
use App\Models\Sitesetting;
use App\Models\Spinamount;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class Ordercheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This one will check order';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
                        $user->balance = $user->balance + $balance;
                        $user->save();

                        //now update the order as success
                        $order->status = '1';
                        $order->save();
                    }
                }

                if ($order->currency == 'TRX') {
                    $client = new Client();
                    $response = $client->get($apikey->base_url.'/api/trx-balance?apikey=' . $apikey->apikey . '&address=' . $addresstrx->address_base58);
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
                        $response = $client->post($apikey->base_url.'/api/trx-transfer?apikey=' . $apikey . '&sender_address=' . $sender_address . '&sender_privatekey=' . $sender_privatekey . '&receiver_address=' . $receiver_address . '&amount=' . $balancetrx);
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
                        $user->balance = $user->balance + $balance;
                        $user->save();

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

                    $previousBalance = $firstuser->balance;
                    $previousReferCommission = $firstuser->refer_commission;
                    $firstuser->refer_commission = $firstuser->refer_commission + $refercommission->commission;
                    $firstuser->balance = $firstuser->balance + $refercommission->commission;
                    $firstuser->save();
                    
                    // Create transaction log
                    $firstuser->createTransactionLog([
                        'type' => 'deposit_referral_commission',
                        'amount' => $refercommission->commission,
                        'previous_balance' => $previousBalance,
                        'new_balance' => $firstuser->balance,
                        'metadata' => [
                            'deposit_amount' => $balance,
                            'commission_rate' => $commission->refer_com1,
                            'commission_level' => 1,
                            'referred_user_id' => $user->id,
                            'previous_refer_commission' => $previousReferCommission,
                            'processed_via' => 'ordercheck_command'
                        ]
                    ]);

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



}
