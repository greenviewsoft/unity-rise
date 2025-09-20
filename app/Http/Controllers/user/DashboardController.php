<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\History;
use App\Models\InvestmentPlan;
use App\Models\InvestmentProfit;
use App\Models\Profitwith;
use App\Models\ReferralCommission;

use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){
        $user = Auth::user();
        
        // Update user rank based on current balance
        $user->updateRank();
        
        // Today's investment profits from InvestmentProfit table
        $today = now()->toDateString();
        $data['todaygrabs'] = InvestmentProfit::where('user_id', $user->id)
            ->whereDate('profit_date', $today)
            ->sum('amount');

        $data['withdraws'] = Withdraw::inRandomOrder()->take(5)->get();
        
        // Today's referral commissions from ReferralCommission table
        $data['refercom'] = ReferralCommission::where('referrer_id', $user->id)
            ->whereDate('commission_date', $today)
            ->sum('commission_amount');
            
        // All time referral commissions
        $data['allrefercom'] = ReferralCommission::where('referrer_id', $user->id)
            ->sum('commission_amount');

        // All time investment profits
        $data['allgrabs'] = InvestmentProfit::where('user_id', $user->id)
            ->sum('amount');

        $data['events'] = Event::orderBy('id', 'asc')->get();
        $data['profitwithdraws'] = Profitwith::inRandomOrder(10)->get();



        // Add rank data
        $data['userRank'] = $user->rank;
        $data['userRankName'] = $user->getRankName();

        // Add investment plans data
        $data['investmentPlans'] = InvestmentPlan::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.dashboard', $data);
    }
}
