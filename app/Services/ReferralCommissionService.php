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
            
            while ($currentUser->refer_id && $level <= 40) { // Max 40 levels as per your data
                $referrer = User::find($currentUser->refer_id);
                
                if (!$referrer) {
                    Log::info("Referrer not found with ID: {$currentUser->refer_id} at level {$level}");
                    break;
                }
                
                // Get referrer's current rank
                $referrerRank = $this->getUserRank($referrer);
                
                // Get commission rate for this level and rank
                $commissionData = $this->getCommissionRate($referrerRank, $level);
                
                if ($commissionData && $commissionData->commission_rate > 0) {
                    $commissionAmount = ($investmentAmount * $commissionData->commission_rate) / 100;
                    
                    // Create commission record
                    $this->createCommissionRecord($referrer, $investor, $investment, $commissionAmount, $level, $commissionData);
                    
                    Log::info("Level {$level} commission created for user {$referrer->id}, Amount: {$commissionAmount}");
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
     * Get user's current rank based on requirements
     */
    private function getUserRank($user)
    {
        // Use existing rank field from users table
        return $user->rank ?? 1;
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
     * Check and update user rank based on requirements
     */
    public function checkAndUpdateUserRank($userId)
    {
        $user = User::find($userId);
        if (!$user) return;
        
        // Get all rank requirements ordered by rank
        $rankRequirements = RankRequirement::orderBy('rank_id')->get();
        
        $userStats = $this->getUserStats($userId);
        
        foreach ($rankRequirements as $requirement) {
            if ($this->meetsRankRequirement($userStats, $requirement)) {
                if ($user->rank < $requirement->rank_id) {
                    $user->update(['rank' => $requirement->rank_id]);
                    
                    // Give rank reward
                    $rankData = ReferralCommissionLevel::where('rank_id', $requirement->rank_id)->first();
                    if ($rankData && $rankData->rank_reward > 0) {
                        $user->increment('refer_commission', $rankData->rank_reward);
                        Log::info("Rank reward given to user {$userId}: {$rankData->rank_reward}");
                    }
                }
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
        // Implement your rank requirement logic here
        // This depends on your rank_requirements table structure
        return true; // Placeholder - implement based on your requirements
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