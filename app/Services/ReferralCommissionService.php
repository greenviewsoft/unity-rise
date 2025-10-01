<?php

namespace App\Services;

use App\Models\ReferralCommission;
use App\Models\ReferralCommissionLevel;
use App\Models\RankRequirement;
use App\Models\User;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ReferralCommissionService
{
    public function distributeCommissions(Investment $investment)
    {
        $investor = $investment->user;
        $investmentAmount = $investment->amount;
        
        Log::info("Starting commission distribution for investment ID: {$investment->id}, Amount: {$investmentAmount}");
        
        // Check if investor was referred by someone
        if (!$investor->refer_id) {
            Log::info('No referrer found - refer_id is null for user: ' . $investor->id);
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Start from level 1 and go up the referral chain
            $currentUser = $investor;
            $level = 1;
            
            while ($currentUser->refer_id && $level <= 40) { // Max 40 levels as per system
                $referrer = User::find($currentUser->refer_id);
                
                if (!$referrer) {
                    Log::info("Referrer not found with ID: {$currentUser->refer_id} at level {$level}");
                    break;
                }
                
                // Get referrer's current rank
                $referrerRank = $this->getUserRank($referrer);
                
                // Check if this level is within the referrer's rank max levels
                $maxLevelsForRank = $this->getMaxLevelsForRank($referrerRank);
                
                if ($level > $maxLevelsForRank) {
                    Log::info("Level {$level} exceeds max levels ({$maxLevelsForRank}) for rank {$referrerRank} of user {$referrer->id}");
                    // Move to next level but don't give commission
                    $currentUser = $referrer;
                    $level++;
                    continue;
                }
                
                // Get commission rate for this level and rank
                $commissionData = $this->getCommissionRate($referrerRank, $level);
                
                if ($commissionData && $commissionData->commission_rate > 0) {
                    $commissionAmount = ($investmentAmount * $commissionData->commission_rate) / 100;
                    
                    // Create commission record
                    $this->createCommissionRecord($referrer, $investor, $investment, $commissionAmount, $level, $commissionData);
                    
                    Log::info("Level {$level} commission created for user {$referrer->id} (Rank: {$referrerRank}), Amount: {$commissionAmount}");
                } else {
                    Log::info("No commission rate found for rank {$referrerRank} at level {$level}");
                }
                
                // Move to next level
                $currentUser = $referrer;
                $level++;
            }
            
            DB::commit();
            Log::info("Commission distribution completed for investment ID: {$investment->id}");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Commission distribution failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    
    /**
     * Get user's current rank
     */
    private function getUserRank(User $user)
    {
        // Check if user has a rank field, otherwise default to Rookie (rank 1)
        return $user->rank ?? 1; // Default to rank 1 (Rookie) if no rank set
    }
    
    /**
     * Get commission rate for specific rank and level
     */
    private function getCommissionRate($rankId, $level)
    {
        return ReferralCommissionLevel::where('rank_id', $rankId)
            ->where('level', $level)
            ->where('is_active', 1)
            ->first();
    }
    
    /**
     * Get maximum levels for a specific rank
     */
    private function getMaxLevelsForRank($rankId)
    {
        $commissionLevel = ReferralCommissionLevel::where('rank_id', $rankId)
            ->where('is_active', 1)
            ->first();
            
        return $commissionLevel ? $commissionLevel->max_levels : 6; // Default to Rookie level
    }
    
    /**
     * Create commission record and update balances
     */
    private function createCommissionRecord($referrer, $investor, $investment, $commissionAmount, $level, $commissionData)
    {
        // Create commission record
        $commission = ReferralCommission::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $investor->id,
            'investment_amount' => $investment->amount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionData->commission_rate,
            'level' => $level,
            'rank' => $commissionData->rank_id, // Use rank_id instead of rank_name
            'rank_id' => $commissionData->rank_id,
            'commission_date' => now(),
            'status' => 'approved' // Auto approved
        ]);
        
        // Add commission to referrer's refer_commission field
        $referrer->increment('refer_commission', $commissionAmount);
        
        // Add commission to referrer's balance field
        $referrer->increment('balance', $commissionAmount);
        
        // Update referrer's total earnings (if this field exists)
        if (Schema::hasColumn('users', 'total_referral_earnings')) {
            $referrer->increment('total_referral_earnings', $commissionAmount);
        }
        
        return $commission;
    }
    
    /**
     * Check and update user rank if requirements are met
     */
    public function checkAndUpdateUserRank(User $user)
    {
        $currentRank = $user->rank ?? 1;
        
        // Check for rank upgrades (starting from current rank + 1)
        $requirements = RankRequirement::where('rank', '>', $currentRank)
            ->where('is_active', 1)
            ->orderBy('rank')
            ->get();
            
        foreach ($requirements as $requirement) {
            if ($this->meetsRankRequirement($user, $requirement->rank)) {
                // Update user rank
                $oldRank = $user->rank;
                $user->rank = $requirement->rank;
                $user->save();
                
                // Award rank reward if applicable
                if ($requirement->reward_amount > 0) {
                    $user->balance += $requirement->reward_amount;
                    $user->save();
                    
                    // Create a transaction record for the rank reward
                    \App\Models\Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'rank_reward',
                        'amount' => $requirement->reward_amount,
                        'description' => "Rank upgrade reward from {$requirement->rank_name}",
                        'status' => 'completed',
                        'created_at' => now(),
                    ]);
                    
                    Log::info("Rank reward awarded to user {$user->id}: {$requirement->reward_amount} for upgrading to {$requirement->rank_name}");
                }
                
                Log::info("User {$user->id} upgraded from rank {$oldRank} to rank {$requirement->rank} ({$requirement->rank_name})");
                
                // Continue checking for higher ranks
                $currentRank = $requirement->rank;
            } else {
                // If user doesn't meet this rank requirement, stop checking higher ranks
                break;
            }
        }
    }
    
    /**
     * Get user statistics for rank calculation
     */
    private function getUserStats($userId)
    {
        return [
            'total_referrals' => User::where('refer_id', $userId)->count(),
            'active_referrals' => User::where('refer_id', $userId)
                ->whereHas('investments', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'total_team_investment' => $this->getTeamInvestment($userId),
            'total_commissions' => ReferralCommission::where('referrer_id', $userId)->sum('commission_amount'),
            'monthly_volume' => $this->getMonthlyVolume($userId)
        ];
    }
    
    /**
     * Check if user meets rank requirement
     */
    private function meetsRankRequirement($userStats, $requirement)
    {
        // Check personal investment requirement
        if (isset($requirement->personal_investment) && $userStats['personal_investment'] < $requirement->personal_investment) {
            return false;
        }
        
        // Check team business volume requirement
        if (isset($requirement->team_business_volume) && $userStats['total_team_investment'] < $requirement->team_business_volume) {
            return false;
        }
        
        // Check total referrals requirement
        if (isset($requirement->total_referrals) && $userStats['total_referrals'] < $requirement->total_referrals) {
            return false;
        }
        
        // Check active referrals requirement
        if (isset($requirement->active_referrals) && $userStats['active_referrals'] < $requirement->active_referrals) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get team investment total
     */
    private function getTeamInvestment($userId, $level = 1, $maxLevel = 40)
    {
        if ($level > $maxLevel) return 0;
        
        $directReferrals = User::where('refer_id', $userId)->get();
        $totalInvestment = 0;
        
        foreach ($directReferrals as $referral) {
            $totalInvestment += $referral->investments()->sum('amount');
            $totalInvestment += $this->getTeamInvestment($referral->id, $level + 1, $maxLevel);
        }
        
        return $totalInvestment;
    }
    
    /**
     * Get monthly volume
     */
    private function getMonthlyVolume($userId)
    {
        return ReferralCommission::where('referrer_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');
    }
    
    /**
     * Get referral statistics
     */
    public function getReferralStats($userId)
    {
        $user = User::find($userId);
        $currentRank = ReferralCommissionLevel::where('rank_id', $user->rank ?? 1)->first();
        
        return [
            'total_referrals' => User::where('refer_id', $userId)->count(),
            'active_referrals' => User::where('refer_id', $userId)
                ->whereHas('investments', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'total_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->sum('commission_amount'),
            'this_month_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->thisMonth()
                ->sum('commission_amount'),
            'today_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->today()
                ->sum('commission_amount'),
            'current_rank' => $currentRank ? $currentRank->rank_name : 'Rookie',
            'team_investment' => $this->getTeamInvestment($userId),
            'total_investment_volume' => ReferralCommission::where('referrer_id', $userId)->sum('investment_amount'),
            'pending_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'pending')
                ->sum('commission_amount')
        ];
    }
}