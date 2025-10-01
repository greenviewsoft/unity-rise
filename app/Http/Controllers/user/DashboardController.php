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
use App\Models\RankReward;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(){
        $user = Auth::user();
        
        // Note: Automatic rank updates disabled - users must manually claim via Rank Upgrade Center
        
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
            
        // Today's rank rewards (leader rewards) from RankReward table
        $data['todayrankrewards'] = RankReward::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('created_at', $today)
            ->sum('reward_amount');
            
        // All time rank rewards (leader rewards)
        $data['allrankrewards'] = RankReward::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('reward_amount');

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
        $directReferrals = $user->referrals()->get();
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

    // user current stats
    $personalInvestment = $user->getPersonalInvestment();   // নিজের ইনভেস্টমেন্ট
    $directReferrals    = $user->referrals()->count();     // সরাসরি রেফারাল সংখ্যা
    
    // টিম ইনভেস্টমেন্ট (all downline levels)
    $teamInvestment = $user->getTeamBusinessVolume();

    // Find the highest rank the user qualifies for
    $qualifiedRank = RankRequirement::where('is_active', 1)
        ->orderBy('rank', 'asc')
        ->get()
        ->filter(function ($rank) use ($personalInvestment, $directReferrals, $teamInvestment) {
            return $personalInvestment >= $rank->personal_investment &&
                   $directReferrals >= $rank->count_level &&
                   $teamInvestment >= $rank->team_business_volume;
        })
        ->last();

    // Get current rank from database
    $currentRank = RankRequirement::where('is_active', 1)
        ->where('rank', $user->rank)
        ->first();

    // পরবর্তী rank বের করা
    $nextRank = RankRequirement::where('is_active', 1)
        ->where('rank', '>', $user->rank)
        ->orderBy('rank', 'asc')
        ->first();

    // If no current rank found, use default values
    if (!$currentRank) {
        $currentRank = (object) [
            'rank_name' => 'Unranked',
            'personal_investment' => 0,
            'count_level' => 0,
            'team_business_volume' => 0,
            'reward_amount' => 0
        ];
    }

    // Calculate progress towards current rank requirements
    $currentPersonalProgress = $personalInvestment;
    $currentDirectProgress = $directReferrals;
    $currentTeamProgress = $teamInvestment;

    // Required progress for current rank
    $requiredPersonalProgress = $currentRank->personal_investment;
    $requiredDirectProgress = $currentRank->count_level;
    $requiredTeamProgress = $currentRank->team_business_volume;

    // Progress percentages (ensure no negative values)
    $personalProgress = $requiredPersonalProgress > 0 ? min(100, ($currentPersonalProgress / $requiredPersonalProgress) * 100) : 100;
    $directProgress = $requiredDirectProgress > 0 ? min(100, ($currentDirectProgress / $requiredDirectProgress) * 100) : 100;
    $teamProgress = $requiredTeamProgress > 0 ? min(100, ($currentTeamProgress / $requiredTeamProgress) * 100) : 100;

    // Remaining requirements for current rank
    $personalRemaining = max(0, $currentRank->personal_investment - $personalInvestment);
    $directRemaining = max(0, $currentRank->count_level - $directReferrals);
    $teamRemaining = max(0, $currentRank->team_business_volume - $teamInvestment);

    // Check if user meets requirements for NEXT rank (for upgrade eligibility)
    $nextRankUnlockReady = false;
    if ($nextRank) {
        $nextPersonalRemaining = max(0, $nextRank->personal_investment - $personalInvestment);
        $nextDirectRemaining = max(0, $nextRank->count_level - $directReferrals);
        $nextTeamRemaining = max(0, $nextRank->team_business_volume - $teamInvestment);
        
        $nextRankUnlockReady = ($nextPersonalRemaining == 0 && $nextDirectRemaining == 0 && $nextTeamRemaining == 0);
    }
    
    // Current rank unlock condition (for display purposes)
    $rankUnlockReady = ($personalRemaining == 0 && $directRemaining == 0 && $teamRemaining == 0);

    return view('user.rank-requirements', [
        'current_rank_name' => $currentRank->rank_name ?? 'Unranked',
        'next_rank_name' => $nextRank ? $nextRank->rank_name : null,
        'current_rank_reward' => $currentRank->reward_amount,

        'current_rank_personal_req' => $currentRank->personal_investment,
        'current_rank_direct_req' => $currentRank->count_level,
        'current_rank_team_req' => $currentRank->team_business_volume,

        // Current user stats
        'personal_investment' => $personalInvestment,
        'direct_referrals' => $directReferrals,
        'team_investment' => $teamInvestment,

        // Progress towards current rank
        'current_personal_progress' => $currentPersonalProgress,
        'current_direct_progress' => $currentDirectProgress,
        'current_team_progress' => $currentTeamProgress,
        
        'required_personal_progress' => $requiredPersonalProgress,
        'required_direct_progress' => $requiredDirectProgress,
        'required_team_progress' => $requiredTeamProgress,

        // Progress percentages
        'personal_investment_progress' => min(100, $personalProgress),
        'direct_referrals_progress' => min(100, $directProgress),
        'team_investment_progress' => min(100, $teamProgress),

        // Remaining to complete current rank
        'personal_investment_remaining' => $personalRemaining,
        'direct_referrals_remaining' => $directRemaining,
        'team_investment_remaining' => $teamRemaining,

        'rank_unlock_ready' => $rankUnlockReady,
        'next_rank_unlock_ready' => $nextRankUnlockReady,
    ]);
}

public function upgradeRank()
{
    try {
        $user = auth()->user();
        
        // Get user current stats
        $personalInvestment = $user->getPersonalInvestment();
        $directReferrals = $user->referrals()->count();
        $teamInvestment = $user->getTeamBusinessVolume();
        
        // Get next rank requirements
        $nextRank = RankRequirement::where('is_active', 1)
            ->where('rank', '>', ($user->rank ?? 0))
            ->orderBy('rank', 'asc')
            ->first();
            
        if (!$nextRank) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reached the maximum rank!'
            ]);
        }
        
        // Check if user meets all requirements
        $meetsPersonal = $personalInvestment >= $nextRank->personal_investment;
        $meetsDirect = $directReferrals >= $nextRank->count_level;
        $meetsTeam = $teamInvestment >= $nextRank->team_business_volume;
        
        if (!($meetsPersonal && $meetsDirect && $meetsTeam)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not meet all requirements for the next rank yet!'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            $oldRank = $user->rank ?? 0;
            
            // Update user rank
            $user->update([
                'rank' => $nextRank->rank,
                'rank_upgraded_at' => now()
            ]);
            
            // Add bonus reward to user balance
            if ($nextRank->reward_amount > 0) {
                $user->increment('balance', $nextRank->reward_amount);
                
                // Create rank reward record
                RankReward::create([
                    'user_id' => $user->id,
                    'rank' => $nextRank->rank,
                    'reward_amount' => $nextRank->reward_amount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Create history record
                History::create([
                    'user_id' => $user->id,
                    'amount' => $nextRank->reward_amount,
                    'type' => 'Rank Bonus',
                    'status' => 'completed',
                    'description' => "Rank upgrade bonus for achieving {$nextRank->rank_name} rank",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Congratulations! You have been upgraded to {$nextRank->rank_name} rank!",
                'bonus_amount' => $nextRank->reward_amount,
                'new_rank' => $nextRank->rank_name,
                'redirect_url' => route('rank.requirements')
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while upgrading your rank. Please try again.'
        ]);
    }
}

}
