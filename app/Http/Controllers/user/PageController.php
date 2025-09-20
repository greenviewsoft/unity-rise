<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Addresstrx;
use App\Models\Announcement;
use App\Models\Apikey;
use App\Models\Commission;
use App\Models\Deposite;
use App\Models\Energy;
use App\Models\Event;
use App\Models\Grab;
use App\Models\History;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReferralCommission;
use App\Models\Refergift;
use App\Models\Referhis;
use App\Models\Settingtrx;
use App\Models\Sitesetting;

use App\Models\Spinamount;
use App\Models\Unseen;
use App\Models\User;
use App\Models\Vip;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class PageController extends Controller
{
    public function history()
    {
        $histories = History::where('user_id', Auth::user()->id)
            ->where('type', 'grab')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('user.history', compact('histories'));
    }

    public function orderLoad(Request $request)
    {
        $page = $request->input('page', 1); // Get the current page number from the request
        $perPage = 5; // Number of items to load per page

        // Fetch data for the current page, e.g., from a database
        $histories = History::where('user_id', Auth::user()->id)
            ->where('type', 'grab')
            ->orderBy('id', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        if ($histories->count() == '0') {
            return response()->json([
                'error' => 'No more grabs',
            ]);
        }

        return view('user.ajax.grabs', compact('histories'));
    }

    public function waves()
    {
        $vips = Vip::all();
        $currentDate = Carbon::now()->toDateString();

        $todaygrab = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->first();

        if (!isset($todaygrab) && Auth::user()->balance > 0) {
            $commssion = Commission::find(1);

            $numberToCheck = Auth::user()->balance;
            $vips = Vip::all();
            foreach ($vips as $key => $vip) {
                if ($numberToCheck >= $vip->requred_from && $numberToCheck <= $vip->requred_to) {
                    $authbal = $vip->income_from;
                }
            }

            // return $authbal;
            if (!isset($authbal)) {
            } else {
                $devid = rand(9, 12);
                $divisions = [];
                $sum = 0;
                for ($i = 0; $i < $devid; $i++) {
                    $divisionResult = $authbal / $devid;
                    $divisions = number_format($divisionResult, 3);
                    $sum += number_format($divisionResult, 3);

                    $product = Product::inRandomOrder()->first();

                    $order = new Grab();
                    $order->user_id = Auth::user()->id;
                    $order->product_id = $product->id;
                    $order->amount = $divisions;
                    $order->save();
                }
            }
        }

        $todaygrab = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->where('status', '1')
            ->count();
        $grabtotal = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->count();
        $todaygrabs = History::where('user_id', Auth::user()->id)
            ->where('type', 'grab')
            ->sum('amount');

        return view('user.waves', compact('vips', 'todaygrab', 'grabtotal', 'todaygrabs'));
    }

    public function announcements()
    {
        $unseenck = Unseen::where('user_id', Auth::user()->id)
            ->where('type', 'notification')
            ->first();
        if (!isset($unseenck)) {
            $unseen = new Unseen();
            $unseen->user_id = Auth::user()->id;
            $unseen->type = 'notification';
            $unseen->save();
        }

        $user = User::find(1);
        return view('user.announcements', compact('user'));
    }

    public function orderGrav()
    {
        $currentDate = Carbon::now()->toDateString();
        $todaygrab = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->first();

        if (!isset($todaygrab) && Auth::user()->balance > 0) {
            $commssion = Commission::find(1);

            $numberToCheck = Auth::user()->balance;
            $vips = Vip::all();
            foreach ($vips as $key => $vip) {
                if ($numberToCheck >= $vip->requred_from && $numberToCheck <= $vip->requred_to) {
                    $authbal = $vip->income_from;
                }
            }
            // return $authbal;
            if (!isset($authbal)) {
                return redirect()
                    ->back()
                    ->with('error', 'Not have energy');
            }

            $devid = rand(3, 10);
            $divisions = [];
            $sum = 0;
            for ($i = 0; $i < $devid; $i++) {
                $divisionResult = $authbal / $devid;
                $divisions = number_format($divisionResult, 3);
                $sum += number_format($divisionResult, 3);

                $product = Product::inRandomOrder()->first();

                $order = new Grab();
                $order->user_id = Auth::user()->id;
                $order->product_id = $product->id;
                $order->amount = $divisions;
                $order->save();
            }
        }

        if (!isset($todaygrab) && Auth::user()->balance == 0) {
            $threeDaysAgo = Carbon::now()->subDays(3);
            $oldgrab = Grab::where('user_id', Auth::user()->id)
                ->where('created_at', '<=', $threeDaysAgo)
                ->first();

            if (!isset($oldgrab)) {
                $divide = 0.3 / 3;

                for ($i = 0; $i < 3; $i++) {
                    $product = Product::inRandomOrder()->first();

                    $order = new Grab();
                    $order->user_id = Auth::user()->id;
                    $order->product_id = $product->id;
                    $order->amount = $divide;
                    $order->save();
                }
            }
        }

        $grabcheck = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->where('status', 0)
            ->first();

        $today = now()->toDateString();
        $todaygrabs = History::whereDate('created_at', $today)
            ->where('user_id', Auth::user()->id)
            ->where('type', 'grab')
            ->sum('amount');

        return view('user.order-grav-ruls', compact('grabcheck', 'todaygrabs'));
    }

    public function orderInfo()
    {
        $currentDate = Carbon::now()->toDateString();

        $grab = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->where('status', 0)
            ->first();

        if (!isset($grab)) {
            return response()->json([
                'error' => 'No order available',
            ]);
        }

        $grab2 = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->where('id', '!=', $grab->id)
            ->where('status', 0)
            ->first();

        if (isset($grab2)) {
            $rand = rand(1, 2);
            if ($rand == '1') {
                $percentage = $grab->amount / rand(3, 6);

                $grab->amount = $grab->amount - $percentage;
                $grab->save();

                $grab2->amount = $grab2->amount + $percentage;
                $grab2->save();
            }
            if ($rand == '2') {
                $percentage = $grab->amount / rand(3, 6);

                $grab->amount = $grab->amount + $percentage;
                $grab->save();

                $grab2->amount = $grab2->amount - $percentage;
                $grab2->save();
            }
        }

        $grab = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->where('status', 0)
            ->first();

        $product = Product::find($grab->product_id);

        return response()->json([
            'grab' => $grab,
            'product' => $product,
            'image' => Sitesetting::find(1)->app_url . '/' . $product->image,
            'time' => Carbon::now()->toDateString(),
            'total' => Auth::user()->balance + $grab->amount,
        ]);
    }

    public function grabSubmit()
    {
        $currentDate = Carbon::now()->toDateString();
        $grab = Grab::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $currentDate)
            ->where('status', 0)
            ->first();

        $user = User::find(Auth::user()->id);
        $user->balance = $user->balance + $grab->amount;
        $user->save();

        $grab->status = 1;
        $grab->save();

        //save history
        $history = new History();
        $history->user_id = $user->id;
        $history->product_id = $grab->product_id;
        $history->grab_id = $grab->id;
        $history->amount = $grab->amount;
        $history->type = 'grab';
        $history->save();

        return redirect()
            ->back()
            ->with('success', 'Order grabbed successfully');
    }

    public function withdraw()
    {
        $settingtrx = Settingtrx::find(1);
        $commssion = Commission::find(1);

        //check total deposit
        $toaldeposit = Deposite::where('user_id', Auth::user()->id)->sum('amount');

        //check minimu withdraw
        $settingtrx = Settingtrx::find(1);
        $balance = Auth::user()->balance;
        $pattern = '/\.\d+/';
        $authbal = preg_replace($pattern, '', $balance) - $toaldeposit - $commssion->bonus;

        return view('user.withdraw', compact('settingtrx', 'authbal'));
    }

    public function deposit()
    {
        return view('user.deposit');
    }

    public function depositDetails()
    {
        $order = Order::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->first();
        $settingtrx = Settingtrx::find(1);

        return view('user.deposit-details', compact('order', 'settingtrx'));
    }

    public function invite()
    {
        return view('user.invite');
    }

    public function event()
    {
        $events = Event::orderBy('id', 'asc')->get();
        
        return view('user.event', compact('events'));
    }



    public function about()
    {
        $sitesetting = Sitesetting::find(1);

        return view('user.about', compact('sitesetting'));
    }

    public function rule()
    {
        return view('user.rule');
    }

    public function promotion()
    {
        return view('user.promotion');
    }

    public function news()
    {
        $announcements = Announcement::orderBy('id', 'desc')->get();

        return view('user.news', compact('announcements'));
    }

    public function lobby()
    {
        return view('user.lobby');
    }

    public function amazon()
    {
        $vips = Vip::all();

        return view('user.amazon', compact('vips'));
    }

    public function userinfo()
    {
        return view('user.userinfo');
    }

    public function vip()
    {
        $numberToCheck = Auth::user()->balance;
        $vips = Vip::all();
        $meyvip = 0;
        $level = 0;
        foreach ($vips as $key => $vip) {
            if ($numberToCheck >= $vip->requred_from && $numberToCheck <= $vip->requred_to) {
                $meyvip = $vip->id;
                $level = $key + 1;
            }
        }
        $vipf = Vip::find($meyvip);

        return view('user.vip', compact('vipf', 'level'));
    }

    public function team()
    {
        $data['total'] = Referhis::where('user_id', Auth::user()->id)->count();

        $today = Carbon::today();
        $data['todayUsers'] = Referhis::where('user_id', Auth::user()->id)
            ->whereDate('created_at', $today)
            ->count();

        $totalusers = Referhis::where('user_id', Auth::user()->id)->get();
        $totalDepositSum = 0;
        $refcom = 0;
        foreach ($totalusers as $totaluser) {
            $deposites = Deposite::where('user_id', $totaluser->refer_id)->sum('amount');

            // Add the deposit sum for the current user to the total
            $totalDepositSum += $deposites;
            $refcom = History::where('user_id', $totaluser->user_id)
                ->where('type', 'B-receive')
                ->sum('amount');
        }
        $data['totaldeposite'] = $totalDepositSum;
        $data['refcom'] = $refcom;

        $data['totalcommission'] = ReferralCommission::where('referrer_id', Auth::user()->id)->sum('commission_amount');
        $data['total_deposite'] = Deposite::where('user_id', Auth::user()->id)->sum('amount');

        $data['total_withdraw'] = Withdraw::where('user_id', Auth::user()->id)->sum('amount');

        $data['allgrabs'] = History::where('user_id', Auth::user()->id)
            ->where('type', 'grab')
            ->sum('amount');
        $data['refercom'] = History::where('user_id', Auth::user()->id)
            ->where('type', 'B-receive')
            ->sum('amount');

        // Enhanced team members data with detailed information
        $refersusers = User::where('parent_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();
        
        // Add detailed information for each referred user
        foreach ($refersusers as $user) {
            // Calculate total deposit amount for this user
            $user->total_deposit = Deposite::where('user_id', $user->id)->sum('amount');
            
            // Calculate total profit (commission + grab earnings)
            $user->total_commission = ReferralCommission::where('referred_id', $user->id)->sum('commission_amount');
            $user->total_grabs = History::where('user_id', $user->id)
                ->where('type', 'grab')
                ->sum('amount');
            $user->total_profit = $user->total_commission + $user->total_grabs;
            
            // Calculate referral commission earned from this user
            $user->earned_commission = ReferralCommission::where('referrer_id', Auth::user()->id)
                ->where('referred_id', $user->id)
                ->sum('commission_amount');
        }
        
        $data['refersusers'] = $refersusers;

        return view('user.team', $data);
    }

    public function account()
    {
        return view('user.account');
    }

    public function record(Request $request)
    {
        $type = $request->type;

        if ($request->type == 'deposit') {
            $deposites = Deposite::where('user_id', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->get();

            return view('user.record', compact('type', 'deposites'));
        }

        if ($request->type == 'withdraw') {
            $withdraws = Withdraw::where('user_id', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->get();

            return view('user.record', compact('type', 'withdraws'));
        }
    }

    public function wallet()
    {
        return view('user.wallet');
    }

    public function newsDetails($id)
    {
        $announcement = Announcement::find($id);

        return view('user.news-details', compact('announcement'));
    }

    public function changePassword()
    {
        return view('user.change-password');
    }

    public function walletGuide()
    {
        return view('user.wallet-guide');
    }

    public function paymentPage()
    {
        $txid = Session::get('txid');
        if (isset($txid)) {
            $addresstrx = Addresstrx::find($txid);
        } else {
            return redirect('user/deposit');
        }

        $currentTime = Carbon::now();
        $extime = Session::get('expiration_time');
        $timeLeftInSeconds = $extime->diffInSeconds($currentTime);

        return view('user.payment_page', compact('addresstrx', 'timeLeftInSeconds'));
    }

    public function sessionex()
    {
        Session::flash('error', 'Address submit session expired');
        return redirect('user/deposit');
    }

    public function addressSetup()
    {
        return view('user.address-setup');
    }

    public function checkDepo(Request $request)
    {
        if (!$request->ajax()) {
            return 'Something wrong';
        }

        if (Auth::user()->withdraw_check == '0') {
            return 'submit please';
        }

        $currentTime = Carbon::now();
        $fiveMinutesAgo = $currentTime->subMinutes(5);
        $deposite = Deposite::where('created_at', '>=', $fiveMinutesAgo)
            ->where('user_id', Auth::user()->id)
            ->first();
        if (isset($deposite)) {
            return response()->json([
                'error' => 'Please wait 5 minute',
            ]);
        }


        $settingtrx = Settingtrx::find(1);
        $addresstrx = Addresstrx::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->first();
        $apikey = Apikey::find(1);

        $client = new Client();
        $response = $client->post($apikey->base_url.'api/usdt-balance?address=' . $addresstrx->address_base58 . '&apikey=' . $apikey->apikey);
        $responseBody = $response->getBody()->getContents();
        $status = $response->getStatusCode();

        $setting = Sitesetting::find(1);

        try {
            $now = Carbon::now();
            $thirtyMinutesAgo = $now->subMinutes(50);

            $order = Order::where('user_id', Auth::user()->id)
                ->where('created_at', '>=', $thirtyMinutesAgo)
                ->where('status', '0')
                ->orderBy('id', 'desc')
                ->first();
            if (isset($order)) {
                if ($setting->development == 'true') {
                    $balance = $order->amount;
                } else {
                    $balance = json_decode($responseBody, true);
                }


                if ($balance > 0) {
                    //now save the balance on trx address
                    $addresstrx->amount = $balance;
                    $addresstrx->save();

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
                        "Deposit via {$order->currency} - Order: {$order->order_number}",
                        [
                            'order_number' => $order->order_number,
                            'currency' => $order->currency,
                            'txid' => 'USDT-trc20',
                            'payment_method' => 'crypto'
                        ]
                    );

                    //now update the order as success
                    $order->status = '1';
                    $order->save();

                    //generate new trx address

                    $client = new Client();
                    $response = $client->get($apikey->base_url.'api/usdt-adress?apikey=' . $apikey->apikey);
                    $responseBody = $response->getBody()->getContents();
                    $status = $response->getStatusCode();
                    $data = json_decode($responseBody, true);

                    $newtrdadd = new Addresstrx();
                    $newtrdadd->user_id = Auth::user()->id;
                    $newtrdadd->address_hex = $data['address_hex'];
                    $newtrdadd->address_base58 = $data['address_base58'];
                    $newtrdadd->private_key = $data['private_key'];
                    $newtrdadd->public_key = $data['public_key'];
                    $newtrdadd->is_validate = $data['is_validate'];
                    $newtrdadd->save();

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
                        $previousBalance = $user->balance;
                        $rewardAmount = ($balance / 100) * $refergift->a_reward;
                        $user->balance = $user->balance + $rewardAmount;
                        $user->save();
                        
                        // Create transaction log
                        $user->createTransactionLog([
                            'type' => 'referral_gift_reward',
                            'amount' => $rewardAmount,
                            'previous_balance' => $previousBalance,
                            'new_balance' => $user->balance,
                            'metadata' => [
                                'recharge_amount' => $balance,
                                'reward_percentage' => $refergift->a_reward,
                                'gift_type' => 'a_reward'
                            ]
                        ]);

                        //save history
                        $history = new History();
                        $history->user_id = $user->id;
                        $history->amount = ($balance / 100) * $refergift->a_reward;
                        $history->type = 'Reward';
                        $history->save();

                        $firstuser = User::find($user->refer_id);
                        if (isset($firstuser)) {
                            $previousBalance = $firstuser->balance;
                            $firstuser->balance = $firstuser->balance + $refergift->b_receive;
                            $firstuser->save();
                            
                            // Create transaction log
                            $firstuser->createTransactionLog([
                                'type' => 'referral_gift_bonus',
                                'amount' => $refergift->b_receive,
                                'previous_balance' => $previousBalance,
                                'new_balance' => $firstuser->balance,
                                'metadata' => [
                                    'recharge_amount' => $balance,
                                    'gift_type' => 'b_receive',
                                    'referred_user_id' => $user->id
                                ]
                            ]);

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
                        $refercommission = new ReferralCommission();
                    $refercommission->referrer_id = $firstuser->id;
                    $refercommission->referred_id = $user->id;
                    $refercommission->level = 1;
                    $refercommission->rank = $firstuser->rank ?? 1;
                    $refercommission->investment_amount = $balance;
                    $refercommission->commission_amount = ($balance / 100) * $commission->refer_com1;
                    $refercommission->commission_date = now();
                    $refercommission->save();

                        $previousBalance = $firstuser->balance;
                        $previousReferCommission = $firstuser->refer_commission;
                        $firstuser->refer_commission = $firstuser->refer_commission + $refercommission->commission_amount;
                    $firstuser->balance = $firstuser->balance + $refercommission->commission_amount;
                        $firstuser->save();
                        
                        // Create transaction log
                        $firstuser->createTransactionLog([
                            'type' => 'deposit_referral_commission',
                            'amount' => $refercommission->commission_amount,
                            'previous_balance' => $previousBalance,
                            'new_balance' => $firstuser->balance,
                            'metadata' => [
                                'deposit_amount' => $balance,
                                'commission_rate' => $commission->refer_com1,
                                'commission_level' => 1,
                                'referred_user_id' => $user->id,
                                'previous_refer_commission' => $previousReferCommission
                            ]
                        ]);

                        //save history
                        $history = new History();
                        $history->user_id = $firstuser->id;
                        $history->amount = $refercommission->commission_amount;
                        $history->type = 'Refer commmission';
                        $history->save();

                        $seconduser = User::find($firstuser->refer_id);

                        if (isset($seconduser)) {
                            $refercommission2 = new ReferralCommission();
                        $refercommission2->referrer_id = $seconduser->id;
                        $refercommission2->referred_id = $user->id;
                        $refercommission2->level = 2;
                        $refercommission2->rank = $seconduser->rank ?? 1;
                        $refercommission2->investment_amount = $balance;
                        $refercommission2->commission_amount = ($balance / 100) * $commission->refer_com2;
                        $refercommission2->commission_date = now();
                        $refercommission2->save();

                            $seconduser->refer_commission = $seconduser->refer_commission + $refercommission2->commission_amount;
                        $seconduser->balance = $seconduser->balance + $refercommission2->commission_amount;
                            $seconduser->save();

                            //save history
                            $history = new History();
                            $history->user_id = $seconduser->id;
                            $history->amount = $refercommission2->commission_amount;
                            $history->type = 'Refer commmission';
                            $history->save();

                            $thirduser = User::find($seconduser->refer_id);

                            if (isset($thirduser)) {
                                $refercommission3 = new ReferralCommission();
                            $refercommission3->referrer_id = $thirduser->id;
                            $refercommission3->referred_id = $user->id;
                            $refercommission3->level = 3;
                            $refercommission3->rank = $thirduser->rank ?? 1;
                            $refercommission3->investment_amount = $balance;
                            $refercommission3->commission_amount = ($balance / 100) * $commission->refer_com3;
                            $refercommission3->commission_date = now();
                            $refercommission3->save();

                                $thirduser->refer_commission = $thirduser->refer_commission + $refercommission3->commission_amount;
                            $thirduser->balance = $thirduser->balance + $refercommission3->commission_amount;
                                $thirduser->save();

                                //save history
                                $history = new History();
                                $history->user_id = $thirduser->id;
                                $history->amount = $refercommission3->commission_amount;
                                $history->type = 'Refer commmission';
                                $history->save();

                                $fourthuser = User::find($thirduser->refer_id);

                                if (isset($fourthuser)) {
                                    $refercommission4 = new ReferralCommission();
                                    $refercommission4->referrer_id = $fourthuser->id;
                                    $refercommission4->referred_id = $user->id;
                                    $refercommission4->level = 4;
                                    $refercommission4->rank = $fourthuser->rank ?? 1;
                                    $refercommission4->investment_amount = $balance;
                                    $refercommission4->commission_amount = ($balance / 100) * $commission->refer_com4;
                                    $refercommission4->commission_date = now();
                                    $refercommission4->save();

                                    $fourthuser->refer_commission = $fourthuser->refer_commission + $refercommission4->commission_amount;
                                    $fourthuser->balance = $fourthuser->balance + $refercommission4->commission_amount;
                                    $fourthuser->save();

                                    //save history
                                    $history = new History();
                                    $history->user_id = $fourthuser->id;
                                    $history->amount = $refercommission4->commission_amount;
                                    $history->type = 'Refer commmission';
                                    $history->save();
                                }
                            }
                        }
                    }

                    $user = User::find(Auth::user()->id);
                    $user->withdraw_check = '0';
                    $user->save();

                    return 'reacharge successfully';
                } else {
                    return 'balance is zero';
                }
            } else {
                return 'no order';
            }
        } catch (Exception $e) {
            return $e->getMessage();
            Log::channel('single')->info($e->getMessage());
        }
    }

    public function bep20Payment()
    {
        $user = Auth::user();
        $depositAmount = Session::get('bep20_amount');
        $walletAddress = Session::get('bep20_wallet_address');
        
        if (!$depositAmount || !$walletAddress) {
            return redirect()->route('user.deposit')->with('error', 'Invalid deposit session');
        }
        
        return view('user.bep20-payment', compact('user', 'depositAmount', 'walletAddress'));
    }
}
