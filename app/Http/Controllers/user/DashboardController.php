<?php

namespace App\Http\Controllers\user;

use App\Models\User;
use App\Models\Event;
use App\Models\History;
use App\Models\Investment;
use App\Models\Deposite;
use App\Models\Withdraw;
use App\Models\Profitwith;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\InvestmentPlan;
use App\Models\RankRequirement;
use App\Models\InvestmentProfit;
use App\Models\ReferralCommission;
use App\Http\Controllers\Controller;
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

        // OPTIMIZED Team Statistics for Dashboard
        $directReferrals = User::where('refer_id', $user->id)->get();
        $data['direct_member_count'] = $directReferrals->count();
        
        // Get direct business total with single query
        $directUserIds = $directReferrals->pluck('id')->toArray();
        $data['direct_business_total'] = empty($directUserIds) ? 0 : 
            Investment::whereIn('user_id', $directUserIds)->where('status', 'active')->sum('amount');
        
        // Active and Inactive Direct Members
        $data['direct_active_members'] = $directReferrals->where('status', 1)->count();
        $data['direct_inactive_members'] = $directReferrals->where('status', 0)->count();

        // Total Team Members (All Downline Levels) - Optimized
        $allDownlineUserIds = $user->getDownlineUserIds();
        $data['total_team_members'] = count($allDownlineUserIds);
        
        if (!empty($allDownlineUserIds)) {
            $teamMemberStats = User::whereIn('id', $allDownlineUserIds)
                                  ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active')
                                  ->first();
            $data['total_active_team_members'] = $teamMemberStats->active ?? 0;
            $data['total_inactive_team_members'] = ($teamMemberStats->total ?? 0) - ($teamMemberStats->active ?? 0);
        } else {
            $data['total_active_team_members'] = 0;
            $data['total_inactive_team_members'] = 0;
        }
        
        // Total Downline Business (All Levels) - Use optimized method
        $data['total_downline_business'] = $user->getTeamBusinessVolume();
        
        // Total Referral Income (All Commission Types)
        $data['total_referral_income'] = ReferralCommission::where('referrer_id', $user->id)->sum('commission_amount') + 
                                       History::where('user_id', $user->id)->where('type', 'B-receive')->sum('amount') +
                                       History::where('user_id', $user->id)->where('type', 'Refer commmission')->sum('amount');

        // Ranking Progress Data
        $currentRank = $user->rank ?? 0;
        $nextRank = $currentRank + 1;
        
        // Get current and next rank requirements
        $currentRankRequirement = \App\Models\RankRequirement::getRequirementsForRank($currentRank);
        $nextRankRequirement = \App\Models\RankRequirement::getRequirementsForRank($nextRank);
        
        // Personal Investment (user's own investments)
        $data['personal_investment'] = $user->getPersonalInvestment();
        
        // Team Investment (total downline business)
        $data['team_investment'] = $data['total_downline_business'];
        
        // Direct Referrals count
        $data['direct_referrals_count'] = $data['direct_member_count'];
        
        // Current rank data
        $data['current_rank_requirement'] = $currentRankRequirement;
        $data['next_rank_requirement'] = $nextRankRequirement;
        
        // Progress calculations for next rank
        if ($nextRankRequirement) {
            // Personal Investment Progress
            $data['personal_investment_required'] = $nextRankRequirement->personal_investment;
            $data['personal_investment_progress'] = min(100, ($data['personal_investment'] / $nextRankRequirement->personal_investment) * 100);
            $data['personal_investment_remaining'] = max(0, $nextRankRequirement->personal_investment - $data['personal_investment']);
            
            // Team Investment Progress
            $data['team_investment_required'] = $nextRankRequirement->team_business_volume;
            $data['team_investment_progress'] = min(100, ($data['team_investment'] / $nextRankRequirement->team_business_volume) * 100);
            $data['team_investment_remaining'] = max(0, $nextRankRequirement->team_business_volume - $data['team_investment']);
            
            // Direct Referrals Progress
            $data['direct_referrals_required'] = $nextRankRequirement->count_level;
            $data['direct_referrals_progress'] = min(100, ($data['direct_referrals_count'] / $nextRankRequirement->count_level) * 100);
            $data['direct_referrals_remaining'] = max(0, $nextRankRequirement->count_level - $data['direct_referrals_count']);
            
            // Bonus amount for next rank
            $data['next_rank_bonus'] = $nextRankRequirement->reward_amount;
            
            // Check if all conditions are met
            $data['rank_unlock_ready'] = (
                $data['personal_investment'] >= $nextRankRequirement->personal_investment &&
                $data['team_investment'] >= $nextRankRequirement->team_business_volume &&
                $data['direct_referrals_count'] >= $nextRankRequirement->count_level
            );
        } else {
            // Max rank reached
            $data['personal_investment_required'] = 0;
            $data['personal_investment_progress'] = 100;
            $data['personal_investment_remaining'] = 0;
            $data['team_investment_required'] = 0;
            $data['team_investment_progress'] = 100;
            $data['team_investment_remaining'] = 0;
            $data['direct_referrals_required'] = 0;
            $data['direct_referrals_progress'] = 100;
            $data['direct_referrals_remaining'] = 0;
            $data['next_rank_bonus'] = 0;
            $data['rank_unlock_ready'] = true;
        }
        
        // Next rank name
        $data['next_rank_name'] = $nextRankRequirement ? $nextRankRequirement->getRankName() : 'Max Rank';

        return view('user.dashboard', $data);
    }
    
    
public function rankRequirements()
{
    $user = auth()->user();

    // user current stats (example, তোমার নিজের relation/table থেকে আনবে)
 $personalInvestment = $user->investments()->sum('amount');   // নিজের ইনভেস্টমেন্ট
$directReferrals    = $user->directReferrals()->count();     // সরাসরি রেফারাল সংখ্যা
    
// টিম ইনভেস্টমেন্ট (level 1 downline)
$teamInvestment = $user->directReferrals()
    ->with('investments')
    ->get()
    ->sum(fn($ref) => $ref->investments->sum('amount'));

    // বর্তমান rank বের করা
    $currentRank = RankRequirement::where('is_active', 1)
        ->orderBy('rank', 'asc')
        ->get()
        ->filter(function ($rank) use ($personalInvestment, $directReferrals, $teamInvestment) {
            return $personalInvestment >= $rank->personal_investment &&
                   $directReferrals >= $rank->count_level &&
                   $teamInvestment >= $rank->team_business_volume;
        })
        ->last();

    // পরবর্তী rank বের করা
    $nextRank = RankRequirement::where('is_active', 1)
        ->where('rank', '>', ($currentRank->rank ?? 0))
        ->orderBy('rank', 'asc')
        ->first();

    if (!$nextRank) {
        return view('user.rank-requirements', [
        'next_rank_name' => null, // মানে max rank এ পৌঁছে গেছে
    ]);
    }

    // Progress হিসাব
    $personalProgress = ($personalInvestment / $nextRank->personal_investment) * 100;
    $directProgress   = ($directReferrals / $nextRank->count_level) * 100;
    $teamProgress     = ($teamInvestment / $nextRank->team_business_volume) * 100;

    // Remaining হিসাব
    $personalRemaining = max(0, $nextRank->personal_investment - $personalInvestment);
    $directRemaining   = max(0, $nextRank->count_level - $directReferrals);
    $teamRemaining     = max(0, $nextRank->team_business_volume - $teamInvestment);

    // Unlock condition
    $rankUnlockReady = ($personalRemaining == 0 && $directRemaining == 0 && $teamRemaining == 0);

    return view('user.rank-requirements', [
        'next_rank_name' => $nextRank->rank_name,
        'next_rank_reward' => $nextRank->reward_amount,

        'next_rank_personal_req' => $nextRank->personal_investment,
        'next_rank_direct_req'   => $nextRank->count_level,
        'next_rank_team_req'     => $nextRank->team_business_volume,

        'personal_investment' => $personalInvestment,
        'direct_referrals'    => $directReferrals,
        'team_investment'     => $teamInvestment,

        'personal_investment_progress' => min(100, $personalProgress),
        'direct_referrals_progress'    => min(100, $directProgress),
        'team_investment_progress'     => min(100, $teamProgress),

        'personal_investment_remaining' => $personalRemaining,
        'direct_referrals_remaining'    => $directRemaining,
        'team_investment_remaining'     => $teamRemaining,

        'rank_unlock_ready' => $rankUnlockReady,
    ]);
}


}
