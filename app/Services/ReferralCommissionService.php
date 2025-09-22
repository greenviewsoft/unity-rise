<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralCommission;
use App\Models\RankReward;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class ReferralCommissionService
{
    /**
     * Calculate and distribute referral commissions for an investment
     */
    public function distributeCommissions(Investment $investment)
    {
        $investor = $investment->user;
        $investmentAmount = $investment->amount;
        
        if (!$investor->refer_id) {
            return; // No referrer, no commission to distribute
        }

        DB::transaction(function () use ($investor, $investmentAmount) {
            $this->processReferralChain($investor, $investmentAmount);
        });
    }

    /**
     * Process the referral chain and calculate commissions
     */
    private function processReferralChain(User $investor, $investmentAmount)
    {
        $currentUser = $investor;
        $level = 1;
        $maxLevels = 40; // Maximum levels to process
        
        while ($currentUser->refer_id && $level <= $maxLevels) {
            $referrer = User::find($currentUser->refer_id);
            
            if (!$referrer) {
                break; // Referrer not found, stop processing
            }

            // Get commission rate based on referrer's rank and current level
            $commissionRate = ReferralCommission::getCommissionRate($referrer->rank, $level);
            
            if ($commissionRate > 0) {
                $commissionAmount = ($investmentAmount * $commissionRate) / 100;
                
                // Create commission record
                ReferralCommission::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $investor->id,
                    'investment_amount' => $investmentAmount,
                    'commission_amount' => $commissionAmount,
                    'level' => $level,
                    'rank' => $referrer->rank,
                    'commission_date' => now()
                ]);
                
                // Capture previous balances for logging
                $previousBalance = $referrer->balance;
                $previousReferCommission = $referrer->refer_commission;
                
                // Add commission to both refer_commission and main balance
                $referrer->increment('refer_commission', $commissionAmount);
                $referrer->increment('balance', $commissionAmount);
                
                // Get new balances for logging
                $referrer->refresh();
                $newBalance = $referrer->balance;
                $newReferCommission = $referrer->refer_commission;
                
                // Create transaction log
                $referrer->createTransactionLog([
                    'type' => 'referral_commission',
                    'amount' => $commissionAmount,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $newBalance,
                    'metadata' => [
                        'investor_id' => $investor->id,
                        'investor_name' => $investor->name,
                        'investment_amount' => $investmentAmount,
                        'commission_level' => $level,
                        'commission_rate' => $commissionRate,
                        'referrer_rank' => $referrer->rank,
                        'previous_refer_commission' => $previousReferCommission,
                        'new_refer_commission' => $newReferCommission
                    ]
                ]);
                
                Log::info("Commission distributed", [
                    'referrer_id' => $referrer->id,
                    'investor_id' => $investor->id,
                    'level' => $level,
                    'rate' => $commissionRate,
                    'amount' => $commissionAmount
                ]);
            }
            
            // Move to next level
            $currentUser = $referrer;
            $level++;
        }
    }

    /**
     * Process rank reward when user achieves new rank
     */
    public function processRankReward(User $user, $oldRank, $newRank)
    {
        try {
            DB::beginTransaction();
            
            $rewardAmount = RankReward::getRewardForRank($newRank);
            
            if ($rewardAmount > 0) {
                // Create rank reward record (auto-approved and processed)
                $rankReward = RankReward::create([
                    'user_id' => $user->id,
                    'old_rank' => $oldRank,
                    'new_rank' => $newRank,
                    'reward_amount' => $rewardAmount,
                    'reward_type' => 'rank_achievement'
                    // status, processed_at, balance update, and logging handled automatically in boot method
                ]);
                
                Log::info("Rank reward auto-processed for user {$user->id}: {$rewardAmount} for achieving rank {$newRank}");
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error processing rank reward: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get referral statistics for a user
     */
    public function getReferralStats(User $user)
    {
        $directReferrals = $user->referrals()->count();
        $totalCommissions = $user->referralCommissions()->sum('commission_amount');
        $todayCommissions = $user->referralCommissions()->today()->sum('commission_amount');
        $thisMonthCommissions = $user->referralCommissions()
            ->whereMonth('commission_date', now()->month)
            ->whereYear('commission_date', now()->year)
            ->sum('commission_amount');
        
        return [
            'direct_referrals' => $directReferrals,
            'total_commissions' => $totalCommissions,
            'today_commissions' => $todayCommissions,
            'this_month_commissions' => $thisMonthCommissions,
            'commission_levels' => $this->getCommissionLevels($user->rank)
        ];
    }

    /**
     * Get commission levels for a rank
     */
    private function getCommissionLevels($rank)
    {
        $levels = [];
        $maxLevel = $this->getMaxLevelForRank($rank);
        
        for ($level = 1; $level <= $maxLevel; $level++) {
            $rate = ReferralCommission::getCommissionRate($rank, $level);
            if ($rate > 0) {
                $levels[] = [
                    'level' => $level,
                    'rate' => $rate
                ];
            }
        }
        
        return $levels;
    }

    /**
     * Get maximum level for a rank
     */
    private function getMaxLevelForRank($rank)
    {
        $maxLevels = [
            1 => 5,   // Bronze
            2 => 6,   // Bronze+
            3 => 7,   // Silver
            4 => 8,   // Silver+
            5 => 9,   // Gold
            6 => 10,  // Gold+
            7 => 15,  // Platinum
            8 => 16,  // Platinum+
            9 => 20,  // Diamond
            10 => 25, // Diamond+
            11 => 30, // Master
            12 => 40  // Grand Master
        ];
        
        return $maxLevels[$rank] ?? 5;
    }
}