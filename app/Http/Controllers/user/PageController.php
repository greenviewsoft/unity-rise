<?php

namespace App\Http\Controllers\user;

use Exception;
use Carbon\Carbon;
use App\Models\Vip;
use App\Models\Grab;
use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\Apikey;
use App\Models\Energy;
use App\Models\Unseen;
use GuzzleHttp\Client;
use App\Models\History;
use App\Models\Product;
use App\Models\Deposite;
use App\Models\Referhis;
use App\Models\Withdraw;
use App\Models\Refergift;
use App\Models\Addresstrx;

use App\Models\Investment;
use App\Models\SettingBep20;
use App\Models\Spinamount;
use App\Models\Sitesetting;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\TradingHistory;
use App\Models\ReferralCommission;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ReferralCommissionLevel;
use Illuminate\Support\Facades\Session;

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
            $commssion = // Commission::find - deprecated, now use ReferralCommissionLevel(1);

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
            $commssion = // Commission::find - deprecated, now use ReferralCommissionLevel(1);

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
        $settingbep20 = SettingBep20::find(1);
        
        // If no settings found, create default values
        if (!$settingbep20) {
            $settingbep20 = (object) [
                'withdraw_fee' => 2,
                'min_withdraw' => 10,
                'usdt_to_usd_rate' => 1.0,
                'network_name' => 'BSC',
                'contract_address' => '0x55d398326f99059fF775485246999027B3197955'
            ];
        }
    
        // Available balance for withdraw should reflect the user's net wallet balance
        // Do not subtract deposits; deposits and withdrawals already affect balance.
        $balance = Auth::user()->balance;
        $authbal = (int) floor($balance);

        return view('user.withdraw', compact('settingbep20', 'authbal'));
    }

    public function deposit()
    {
        return view('user.deposit');
    }

    public function manualDeposit()
    {
        // Get BEP20 settings for the fixed address
        $bep20Setting = SettingBep20::first();
        
        return view('user.manual-deposit', compact('bep20Setting'));
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
        $sitesetting = Sitesetting::find(1);
        return view('user.rule', compact('sitesetting'));
    }

    public function promotion()
    {
        $sitesetting = Sitesetting::find(1);
        return view('user.promotion', compact('sitesetting'));
    }

    public function news()
    {
        $announcements = Announcement::orderBy('id', 'desc')->get();

        return view('user.news', compact('announcements'));
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
    $currentUser = Auth::user();
    $maxLevelsForRank = $this->getMaxLevelsForRank($currentUser->rank);

    // Treat these as "active" investment statuses everywhere
    $activeStatuses = ['active', 'Active', 'ACTIVE'];

    // General team info
    $data['total'] = Referhis::where('user_id', $currentUser->id)->count();
    $today = Carbon::today();
    $data['todayUsers'] = Referhis::where('user_id', $currentUser->id)
        ->whereDate('created_at', $today)
        ->count();

    // Total deposit and B-receive for direct referrals
    $totalusers = Referhis::where('user_id', $currentUser->id)->get();
    $totalDepositSum = 0;
    $refcomSum = 0;
    foreach ($totalusers as $totaluser) {
        $deposites = Deposite::where('user_id', $totaluser->refer_id)->sum('amount');
        $totalDepositSum += $deposites;

        $refcomSum += History::where('user_id', $totaluser->user_id)
            ->where('type', 'B-receive')
            ->sum('amount');
    }
    $data['totaldeposite'] = $totalDepositSum;
    $data['refcom'] = $refcomSum;

    $data['totalcommission'] = ReferralCommission::where('referrer_id', $currentUser->id)->sum('commission_amount');
    $data['total_deposite'] = Deposite::where('user_id', $currentUser->id)->sum('amount');
    $data['total_withdraw'] = Withdraw::where('user_id', $currentUser->id)->sum('amount');

    $data['allgrabs'] = History::where('user_id', $currentUser->id)
        ->where('type', 'grab')
        ->sum('amount');
    $data['refercom'] = History::where('user_id', $currentUser->id)
        ->where('type', 'B-receive')
        ->sum('amount');

    /* ---- Direct Members & Business (investment-based activity) ---- */
    $directReferrals = $currentUser->referrals()->get();
    $data['direct_member_count'] = $directReferrals->count();

    $directUserIds = $directReferrals->pluck('id')->toArray();

    // Direct business total uses active investments only
    $data['direct_business_total'] = empty($directUserIds) ? 0 :
        Investment::whereIn('user_id', $directUserIds)
            ->whereIn('status', $activeStatuses)
            ->sum('amount');

    // Active/Inactive = has at least one active investment
    $directActiveIds = empty($directUserIds) ? collect() :
        Investment::whereIn('user_id', $directUserIds)
            ->whereIn('status', $activeStatuses)
            ->distinct()
            ->pluck('user_id');

    $data['direct_active_members']   = $directActiveIds->count();
    $data['direct_inactive_members'] = $data['direct_member_count'] - $data['direct_active_members'];

    /* ---- Team (filtered by max levels) ---- */
    $allDownlineUserIds = $currentUser->getDownlineUserIds();

    $filteredDownlineIds = [];
    foreach ($allDownlineUserIds as $userId) {
        $downlineUser = User::find($userId);
        if ($downlineUser) {
            $level = $this->calculateUserLevel($currentUser, $downlineUser);
            if ($level <= $maxLevelsForRank) {
                $filteredDownlineIds[] = $userId;
            }
        }
    }

    $data['total_team_members'] = count($filteredDownlineIds);

    if (!empty($filteredDownlineIds)) {
        // Active/Inactive = has at least one active investment
        $teamActiveIds = Investment::whereIn('user_id', $filteredDownlineIds)
            ->whereIn('status', $activeStatuses)
            ->distinct()
            ->pluck('user_id');

        $data['total_active_team_members']   = $teamActiveIds->count();
        $data['total_inactive_team_members'] = count($filteredDownlineIds) - $data['total_active_team_members'];
    } else {
        $data['total_active_team_members'] = 0;
        $data['total_inactive_team_members'] = 0;
    }

    // Team business uses active investments only
    $data['total_downline_business'] = empty($filteredDownlineIds) ? 0 :
        Investment::whereIn('user_id', $filteredDownlineIds)
            ->whereIn('status', $activeStatuses)
            ->sum('amount');

    // Total Referral Income (All Commission Types)
    $data['total_referral_income'] =
        ReferralCommission::where('referrer_id', $currentUser->id)->sum('commission_amount')
        + History::where('user_id', $currentUser->id)->where('type', 'B-receive')->sum('amount')
        + History::where('user_id', $currentUser->id)->where('type', 'Refer commmission')->sum('amount');

    /* ---- Multi-level downline users (pre-aggregated) ---- */
    if (!empty($filteredDownlineIds)) {
        $allDownlineUsers = User::whereIn('id', $filteredDownlineIds)->get();

        $investments = Investment::whereIn('user_id', $filteredDownlineIds)
            ->whereIn('status', $activeStatuses)
            ->selectRaw('user_id, SUM(amount) as total_amount')
            ->groupBy('user_id')
            ->pluck('total_amount', 'user_id');

        $commissions = ReferralCommission::whereIn('referred_id', $filteredDownlineIds)
            ->selectRaw('referred_id, SUM(commission_amount) as total_commission')
            ->groupBy('referred_id')
            ->pluck('total_commission', 'referred_id');

        $grabs = History::whereIn('user_id', $filteredDownlineIds)
            ->where('type', 'grab')
            ->selectRaw('user_id, SUM(amount) as total_grabs')
            ->groupBy('user_id')
            ->pluck('total_grabs', 'user_id');

        $earnedCommissions = ReferralCommission::where('referrer_id', $currentUser->id)
            ->whereIn('referred_id', $filteredDownlineIds)
            ->selectRaw('referred_id, SUM(commission_amount) as earned_commission')
            ->groupBy('referred_id')
            ->pluck('earned_commission', 'referred_id');

        $refersusers = collect();
        foreach ($allDownlineUsers as $user) {
            $level = $this->calculateUserLevel($currentUser, $user);

            $user->level = $level;
            $user->total_deposit = $investments[$user->id] ?? 0;
            $user->total_commission = $commissions[$user->id] ?? 0;
            $user->total_grabs = $grabs[$user->id] ?? 0;
            $user->total_profit = $user->total_commission + $user->total_grabs;
            $user->earned_commission = $earnedCommissions[$user->id] ?? 0;

            $refersusers->push($user);
        }
        $data['refersusers'] = $refersusers->sortBy(['level', 'id']);
    } else {
        $data['refersusers'] = collect();
    }

    // User rank & business
    $data['user_rank'] = $currentUser->rank;
    $data['user_business_volume'] = $currentUser->getTeamBusinessVolume();
    $data['user_personal_investment'] = $currentUser->getPersonalInvestment();

    // Rank requirements and progress
    $currentRankRequirements = $this->getRankRequirements($currentUser->rank);
    $nextRankRequirements = $this->getRankRequirements($currentUser->rank + 1);

    $data['current_rank_requirement'] = $currentRankRequirements;
    $data['next_rank_requirement'] = $nextRankRequirements;

    $currentTarget = $currentRankRequirements['business_volume'];
    $nextTarget = $nextRankRequirements['business_volume'] ?? $currentTarget;

    $completed = min($data['user_business_volume'], $currentTarget);
    $remaining = max(0, $currentTarget - $data['user_business_volume']);
    $progressPercent = $currentTarget > 0 ? round(($completed / $currentTarget) * 100, 2) : 0;

    $data['business_completed'] = $completed;
    $data['business_remaining'] = $remaining;
    $data['progress_percent'] = $progressPercent;

    return view('user.team', $data);
}



/**
 * Calculate the level of a user in relation to the current user
 */
public function calculateUserLevel($rootUser, $downlineUser)
{
    // যদি rootUser == downlineUser return 0;
    if($rootUser->id == $downlineUser->id) return 1; // root = level 1

    $level = 1;
    $current = $downlineUser;
    while($current->refer_id != $rootUser->id && $current->refer_id != null){
        $current = User::find($current->refer_id);
        $level++;
    }

    return $level;
}



/**
 * Get max levels allowed for a specific rank
 */
private function getMaxLevelsForRank($rank)
{
    $rankData = \App\Models\ReferralCommissionLevel::where('rank_id', $rank ?? 1)
        ->where('is_active', 1)
        ->first();
    
    if ($rankData) {
        return $rankData->max_levels;
    }
    
    // Fallback values if not found in database
    $maxLevels = [
        1 => 6,   // Rookie
        2 => 9,   // Bronze
        3 => 12,  // Silver
        4 => 15,  // Gold
        5 => 18,  // Diamond
        6 => 22,  // Master
        7 => 26,  // Grand Master
        8 => 30,  // Champion
        9 => 33,  // Legend
        10 => 36, // Mythic
        11 => 38, // Immortal
        12 => 40  // Divine
    ];
    
    return $maxLevels[$rank] ?? 6;
}

/**
 * Get rank requirements for a specific rank
 */
private function getRankRequirements($rank)
{
    $rankRequirement = \App\Models\RankRequirement::where('rank', $rank)->first();

    if ($rankRequirement) {
        return [
            'business_volume' => $rankRequirement->team_business_volume,
            'name' => $rankRequirement->rank_name
        ];
    }

    // Fallback values
    $requirements = [
        0 => ['business_volume' => 0, 'name' => 'Unranked'],
        1 => ['business_volume' => 12000, 'name' => 'Rookie'],
        2 => ['business_volume' => 25000, 'name' => 'Bronze'],
        3 => ['business_volume' => 50000, 'name' => 'Silver'],
        4 => ['business_volume' => 100000, 'name' => 'Gold'],
        5 => ['business_volume' => 200000, 'name' => 'Diamond'],
        6 => ['business_volume' => 350000, 'name' => 'Master'],
        7 => ['business_volume' => 500000, 'name' => 'Grand Master'],
        8 => ['business_volume' => 750000, 'name' => 'Champion'],
        9 => ['business_volume' => 1000000, 'name' => 'Legend'],
        10 => ['business_volume' => 1500000, 'name' => 'Mythic'],
        11 => ['business_volume' => 2000000, 'name' => 'Immortal'],
        12 => ['business_volume' => 3000000, 'name' => 'Divine']
    ];

    return $requirements[$rank] ?? ['business_volume' => 0, 'name' => 'Unknown'];
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
                    $commission = // Commission::find - deprecated, now use ReferralCommissionLevel(1);
                    $user = User::find($order->user_id);
                    $firstuser = User::find($user->refer_id);
                    if (isset($firstuser)) {
                        $refercommission = new ReferralCommission();
                    $refercommission->referrer_id = $firstuser->id;
                    $refercommission->referred_id = $user->id;
                    $refercommission->level = 1;
                    $refercommission->rank = $firstuser->rank ?? 1;
                    $refercommission->investment_amount = $balance;
                    $commissionRate1 = \App\Models\ReferralCommissionLevel::getCommissionRatesForRank($firstuser->rank ?? 1, 1);
                    $refercommission->commission_amount = ($balance / 100) * $commissionRate1;
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
                                'commission_rate' => $commissionRate1,
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
                        $commissionRate2 = \App\Models\ReferralCommissionLevel::getCommissionRatesForRank($seconduser->rank ?? 1, 2);
                        $refercommission2->commission_amount = ($balance / 100) * $commissionRate2;
                        $refercommission2->commission_date = now();
                        $refercommission2->save();

                            $previousBalance = $seconduser->balance;
                            $previousReferCommission = $seconduser->refer_commission;
                            $seconduser->refer_commission = $seconduser->refer_commission + $refercommission2->commission_amount;
                        $seconduser->balance = $seconduser->balance + $refercommission2->commission_amount;
                            $seconduser->save();
                            
                            // Create transaction log
                            $seconduser->createTransactionLog([
                                'type' => 'deposit_referral_commission',
                                'amount' => $refercommission2->commission_amount,
                                'previous_balance' => $previousBalance,
                                'new_balance' => $seconduser->balance,
                                'metadata' => [
                                    'deposit_amount' => $balance,
                                    'commission_rate' => $commissionRate2,
                                    'commission_level' => 2,
                                    'referred_user_id' => $user->id,
                                    'previous_refer_commission' => $previousReferCommission
                                ]
                            ]);

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
                            $commissionRate3 = \App\Models\ReferralCommissionLevel::getCommissionRatesForRank($thirduser->rank ?? 1, 3);
                        $refercommission3->commission_amount = ($balance / 100) * $commissionRate3;
                            $refercommission3->commission_date = now();
                            $refercommission3->save();

                                $previousBalance = $thirduser->balance;
                                $previousReferCommission = $thirduser->refer_commission;
                                $thirduser->refer_commission = $thirduser->refer_commission + $refercommission3->commission_amount;
                            $thirduser->balance = $thirduser->balance + $refercommission3->commission_amount;
                                $thirduser->save();
                                
                                // Create transaction log
                                $thirduser->createTransactionLog([
                                    'type' => 'deposit_referral_commission',
                                    'amount' => $refercommission3->commission_amount,
                                    'previous_balance' => $previousBalance,
                                    'new_balance' => $thirduser->balance,
                                    'metadata' => [
                                        'deposit_amount' => $balance,
                                        'commission_rate' => $commissionRate3,
                                        'commission_level' => 3,
                                        'referred_user_id' => $user->id,
                                        'previous_refer_commission' => $previousReferCommission
                                    ]
                                ]);

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
                                    $commissionRate4 = \App\Models\ReferralCommissionLevel::getCommissionRatesForRank($fourthuser->rank ?? 1, 4);
                        $refercommission4->commission_amount = ($balance / 100) * $commissionRate4;
                                    $refercommission4->commission_date = now();
                                    $refercommission4->save();

                                    $previousBalance = $fourthuser->balance;
                                    $previousReferCommission = $fourthuser->refer_commission;
                                    $fourthuser->refer_commission = $fourthuser->refer_commission + $refercommission4->commission_amount;
                                    $fourthuser->balance = $fourthuser->balance + $refercommission4->commission_amount;
                                    $fourthuser->save();
                                    
                                    // Create transaction log
                                    $fourthuser->createTransactionLog([
                                        'type' => 'deposit_referral_commission',
                                        'amount' => $refercommission4->commission_amount,
                                        'previous_balance' => $previousBalance,
                                        'new_balance' => $fourthuser->balance,
                                        'metadata' => [
                                            'deposit_amount' => $balance,
                                            'commission_rate' => $commissionRate4,
                                            'commission_level' => 4,
                                            'referred_user_id' => $user->id,
                                            'previous_refer_commission' => $previousReferCommission
                                        ]
                                    ]);

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
        
        // Get the admin-set receiver address from settings
        $bep20Setting = SettingBep20::first();
        $walletAddress = $bep20Setting ? $bep20Setting->receiver_address : null;
        
        if (!$depositAmount) {
            return redirect()->route('user.deposit')->with('error', 'Invalid deposit session');
        }
        
        if (!$walletAddress) {
            return redirect()->route('user.deposit')->with('error', 'Manual deposit address not configured. Please contact administrator.');
        }
        
        return view('user.bep20-payment', compact('user', 'depositAmount', 'walletAddress'));
    }

    public function tradingHistory()
    {
        $tradingHistories = TradingHistory::with('uploader')
            ->where('is_active', 1)
            ->orderBy('upload_date', 'desc')
            ->paginate(10);

        return view('user.trading-history', compact('tradingHistories'));
    }


    public function downloadTradingHistory($id)
    {
        $history = \App\Models\TradingHistory::findOrFail($id);
    
        // file_path এর full relative path ধরে নিন
        $filePath = public_path($history->file_path);
    
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }
    
        return response()->download($filePath, $history->file_name);
    }

}
