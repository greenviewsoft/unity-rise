<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RankRequirement;
use App\Models\RankReward;
use App\Models\History;
use App\Models\Log;
use App\Services\RankUpgradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LaravelLog;

class RankUpgradeController extends Controller
{
    protected $rankUpgradeService;

    public function __construct(RankUpgradeService $rankUpgradeService)
    {
        $this->rankUpgradeService = $rankUpgradeService;
    }

    /**
     * Show rank upgrade page
     */
    public function index()
    {
        $user = Auth::user();
        $rankStats = $this->rankUpgradeService->getUserRankStats($user);
        
        // Get all rank requirements for display
        $allRanks = RankRequirement::where('is_active', 1)
                                  ->orderBy('rank')
                                  ->get();
        
        // Check if user is eligible for any rank upgrade
        $eligibleRank = $this->getEligibleRankForUpgrade($user);
        $canClaim = $eligibleRank > $user->rank;
        
        // Get recent rank rewards
        $recentRewards = RankReward::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->limit(10)
                                  ->get();

        return view('user.rank.upgrade', compact(
            'user',
            'rankStats', 
            'allRanks', 
            'eligibleRank', 
            'canClaim',
            'recentRewards'
        ));
    }

    /**
     * Check user's eligibility for rank upgrade
     */
    public function checkEligibility()
    {
        $user = Auth::user();
        $rankStats = $this->rankUpgradeService->getUserRankStats($user);
        $eligibleRank = $this->getEligibleRankForUpgrade($user);
        
        return response()->json([
            'success' => true,
            'current_rank' => $user->rank,
            'eligible_rank' => $eligibleRank,
            'can_claim' => $eligibleRank > $user->rank,
            'rank_stats' => $rankStats,
            'message' => $eligibleRank > $user->rank 
                ? 'আপনি র‍্যাংক আপগ্রেডের জন্য যোগ্য!' 
                : 'আপনি এখনো র‍্যাংক আপগ্রেডের জন্য যোগ্য নন।'
        ]);
    }

    /**
     * Claim rank upgrade
     */
    public function claimUpgrade(Request $request)
    {
        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            // Check if user is eligible for upgrade
            $eligibleRank = $this->getEligibleRankForUpgrade($user);
            
            if ($eligibleRank <= $user->rank) {
                return response()->json([
                    'success' => false,
                    'message' => 'আপনি এখনো র‍্যাংক আপগ্রেডের জন্য যোগ্য নন।'
                ]);
            }
            
            // Process all eligible rank upgrades
            $oldRank = $user->rank;
            $totalReward = 0;
            $upgradedRanks = [];
            
            for ($targetRank = $oldRank + 1; $targetRank <= $eligibleRank; $targetRank++) {
                $requirement = RankRequirement::where('rank', $targetRank)
                                             ->where('is_active', 1)
                                             ->first();
                
                if ($requirement) {
                    // Update user rank
                    $user->update([
                        'rank' => $targetRank,
                        'rank_upgraded_at' => now()
                    ]);
                    
                    // Process reward
                    if ($requirement->reward_amount > 0) {
                        $this->processRankReward($user, $oldRank, $targetRank, $requirement->reward_amount);
                        $totalReward += $requirement->reward_amount;
                    }
                    
                    $upgradedRanks[] = [
                        'rank' => $targetRank,
                        'name' => $requirement->rank_name,
                        'reward' => $requirement->reward_amount
                    ];
                    
                    $oldRank = $targetRank;
                }
            }
            
            DB::commit();
            
            LaravelLog::info("Manual rank upgrade claimed", [
                'user_id' => $user->id,
                'username' => $user->username,
                'old_rank' => $user->rank,
                'new_rank' => $eligibleRank,
                'total_reward' => $totalReward,
                'upgraded_ranks' => $upgradedRanks
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'অভিনন্দন! আপনার র‍্যাংক সফলভাবে আপগ্রেড হয়েছে।',
                'old_rank' => $user->rank,
                'new_rank' => $eligibleRank,
                'total_reward' => $totalReward,
                'upgraded_ranks' => $upgradedRanks,
                'new_balance' => $user->fresh()->balance
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            LaravelLog::error("Rank upgrade claim failed", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'র‍্যাংক আপগ্রেডে সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।'
            ]);
        }
    }

    /**
     * Get eligible rank for upgrade
     */
    private function getEligibleRankForUpgrade(User $user)
    {
        $currentRank = $user->rank ?? 0;
        $eligibleRank = $currentRank;
        
        // Check each rank from current + 1 to max rank
        for ($rank = $currentRank + 1; $rank <= 12; $rank++) {
            if ($this->checkRankEligibility($user, $rank)) {
                $eligibleRank = $rank;
            } else {
                break; // Stop at first non-eligible rank
            }
        }
        
        return $eligibleRank;
    }

    /**
     * Check if user is eligible for a specific rank
     */
    private function checkRankEligibility(User $user, $targetRank)
    {
        $requirement = RankRequirement::where('rank', $targetRank)
                                     ->where('is_active', 1)
                                     ->first();
        
        if (!$requirement) {
            return false;
        }

        // Check personal investment requirement
        $personalInvestment = $user->investments()
                                  ->where('status', 'active')
                                  ->sum('amount');
        
        if ($personalInvestment < $requirement->personal_investment) {
            return false;
        }

        // Check team business volume requirement
        $teamBusinessVolume = $this->calculateTeamBusinessVolume($user, $requirement->count_level);
        
        if ($teamBusinessVolume < $requirement->team_business_volume) {
            return false;
        }

        return true;
    }

    /**
     * Calculate team business volume up to specified levels
     */
    private function calculateTeamBusinessVolume(User $user, $maxLevels)
    {
        $totalVolume = 0;
        $this->calculateTeamVolumeRecursive($user, $maxLevels, 0, $totalVolume);
        return $totalVolume;
    }

    /**
     * Recursive function to calculate team volume
     */
    private function calculateTeamVolumeRecursive(User $user, $maxLevels, $currentLevel, &$totalVolume)
    {
        if ($currentLevel >= $maxLevels) {
            return;
        }

        $directReferrals = $user->referrals()->whereIn('status', ['1', 'on'])->get();
        
        foreach ($directReferrals as $referral) {
            // Add referral's investment to total volume
            $referralInvestment = $referral->investments()
                                          ->where('status', 'active')
                                          ->sum('amount');
            $totalVolume += $referralInvestment;
            
            // Continue to next level
            $this->calculateTeamVolumeRecursive($referral, $maxLevels, $currentLevel + 1, $totalVolume);
        }
    }

    /**
     * Process rank reward
     */
    private function processRankReward(User $user, $oldRank, $newRank, $rewardAmount)
    {
        $previousBalance = $user->balance;
        
        // Create rank reward record
        $rankReward = RankReward::create([
            'user_id' => $user->id,
            'old_rank' => $oldRank,
            'new_rank' => $newRank,
            'reward_amount' => $rewardAmount,
            'reward_type' => 'manual_claim',
            'status' => 'processed',
            'processed_at' => now()
        ]);
        
        // Update user balance
        $user->increment('balance', $rewardAmount);
        $newBalance = $user->fresh()->balance;
        
        // Create history record
        History::create([
            'user_id' => $user->id,
            'amount' => $rewardAmount,
            'type' => 'rank_upgrade_reward'
        ]);
        
        // Create transaction log
        Log::createTransactionLog(
            $user->id,
            'rank_upgrade_manual',
            $rewardAmount,
            $previousBalance,
            $newBalance,
            'App\\Models\\RankReward',
            $rankReward->id,
            "Manual rank {$newRank} upgrade reward claimed",
            [
                'old_rank' => $oldRank,
                'new_rank' => $newRank,
                'reward_type' => 'manual_claim'
            ]
        );
    }

    /**
     * Get rank upgrade history
     */
    public function history()
    {
        $user = Auth::user();
        
        $rankRewards = RankReward::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);
        
        return view('user.rank.history', compact('rankRewards'));
    }
}