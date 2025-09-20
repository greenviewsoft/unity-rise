<?php

namespace App\Services;

use App\Models\User;
use App\Models\RankRequirement;
use App\Models\Investment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RankUpgradeService
{
    /**
     * Check and process rank upgrades for a specific user
     */
    public function checkUserRankUpgrade(User $user)
    {
        try {
            DB::beginTransaction();
            
            $oldRank = $user->rank ?? 0;
            $newRank = $user->processRankUpgrade();
            
            if ($newRank > $oldRank) {
                Log::info("User {$user->username} upgraded from rank {$oldRank} to {$newRank}");
                
                // Trigger cascade check for upline users
                $this->cascadeRankCheck($user->referrer);
            }
            
            DB::commit();
            return $newRank;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Rank upgrade failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check rank upgrades for upline users (cascade effect)
     */
    private function cascadeRankCheck(?User $uplineUser, $depth = 0)
    {
        if (!$uplineUser || $depth > 10) { // Prevent infinite loops
            return;
        }
        
        $oldRank = $uplineUser->rank ?? 0;
        $newRank = $uplineUser->processRankUpgrade();
        
        if ($newRank > $oldRank) {
            Log::info("Cascade upgrade: User {$uplineUser->username} upgraded from rank {$oldRank} to {$newRank}");
            
            // Continue cascade check
            $this->cascadeRankCheck($uplineUser->referrer, $depth + 1);
        }
    }
    
    /**
     * Process rank upgrade when user makes an investment
     */
    public function processInvestmentRankCheck(Investment $investment)
    {
        $user = $investment->user;
        
        if (!$user) {
            return;
        }
        
        // Check rank upgrade for the investor
        $this->checkUserRankUpgrade($user);
        
        // Check rank upgrades for all upline users (their team BV increased)
        $this->checkUplineRankUpgrades($user);
    }
    
    /**
     * Check rank upgrades for all upline users
     */
    private function checkUplineRankUpgrades(User $user)
    {
        $uplineUser = $user->referrer;
        $level = 1;
        
        while ($uplineUser && $level <= 20) { // Check up to 20 levels
            $this->checkUserRankUpgrade($uplineUser);
            $uplineUser = $uplineUser->referrer;
            $level++;
        }
    }
    
    /**
     * Bulk check rank upgrades for all users
     */
    public function bulkCheckRankUpgrades($limit = 100)
    {
        $users = User::where('status', 'active')
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
        
        $upgradedCount = 0;
        
        foreach ($users as $user) {
            try {
                $oldRank = $user->rank ?? 0;
                $newRank = $this->checkUserRankUpgrade($user);
                
                if ($newRank > $oldRank) {
                    $upgradedCount++;
                }
                
            } catch (\Exception $e) {
                Log::error("Bulk rank check failed for user {$user->id}: " . $e->getMessage());
                continue;
            }
        }
        
        Log::info("Bulk rank check completed. {$upgradedCount} users upgraded out of {$users->count()} checked.");
        
        return $upgradedCount;
    }
    
    /**
     * Get rank upgrade statistics
     */
    public function getRankUpgradeStats()
    {
        $stats = [];
        
        for ($rank = 1; $rank <= 12; $rank++) {
            $requirement = RankRequirement::getRequirementsForRank($rank);
            $userCount = User::where('rank', $rank)->count();
            $eligibleCount = $this->getEligibleUsersCount($rank);
            
            $stats[] = [
                'rank' => $rank,
                'rank_name' => $requirement ? $requirement->getRankName() : 'Unknown',
                'current_users' => $userCount,
                'eligible_users' => $eligibleCount,
                'requirements' => $requirement ? [
                    'team_bv' => $requirement->team_business_volume,
                    'count_level' => $requirement->count_level,
                    'personal_investment' => $requirement->personal_investment,
                    'reward_amount' => $requirement->reward_amount
                ] : null
            ];
        }
        
        return $stats;
    }
    
    /**
     * Get count of users eligible for a specific rank
     */
    private function getEligibleUsersCount($rank)
    {
        $requirement = RankRequirement::getRequirementsForRank($rank);
        
        if (!$requirement) {
            return 0;
        }
        
        $eligibleCount = 0;
        $users = User::where('rank', '<', $rank)
                    ->where('status', 'active')
                    ->get();
        
        foreach ($users as $user) {
            if ($requirement->checkUserEligibility($user)) {
                $eligibleCount++;
            }
        }
        
        return $eligibleCount;
    }
    
    /**
     * Manual rank upgrade for admin purposes
     */
    public function manualRankUpgrade(User $user, $targetRank, $reason = 'Manual upgrade')
    {
        try {
            DB::beginTransaction();
            
            $oldRank = $user->rank ?? 0;
            
            if ($targetRank <= $oldRank) {
                throw new \Exception('Target rank must be higher than current rank');
            }
            
            // Update user rank
            $user->update(['rank' => $targetRank]);
            
            // Process rewards for each rank upgrade
            for ($rank = $oldRank + 1; $rank <= $targetRank; $rank++) {
                $requirement = RankRequirement::getRequirementsForRank($rank);
                
                if ($requirement && $requirement->reward_amount > 0) {
                    $previousBalance = $user->balance;
                    
                    // Create rank reward record
                    $rankReward = \App\Models\RankReward::create([
                        'user_id' => $user->id,
                        'old_rank' => $rank - 1,
                        'new_rank' => $rank,
                        'reward_amount' => $requirement->reward_amount,
                        'reward_type' => 'special',
                        'status' => 'processed',
                        'processed_at' => now()
                    ]);
                    
                    // Update user balance
                    $user->increment('balance', $requirement->reward_amount);
                    $newBalance = $user->fresh()->balance;
                    
                    // Create history record
                    \App\Models\History::create([
                        'user_id' => $user->id,
                        'amount' => $requirement->reward_amount,
                        'type' => 'manual_rank_upgrade',
                        'description' => "Manual rank {$rank} upgrade: {$reason}",
                        'status' => 'completed'
                    ]);
                    
                    // Create transaction log
                    \App\Models\Log::createTransactionLog(
                        $user->id,
                        'manual_rank_reward',
                        $requirement->reward_amount,
                        $previousBalance,
                        $newBalance,
                        'App\\Models\\RankReward',
                        $rankReward->id,
                        "Manual rank {$rank} upgrade: {$reason}",
                        [
                            'old_rank' => $rank - 1,
                            'new_rank' => $rank,
                            'reward_type' => 'manual_upgrade',
                            'reason' => $reason,
                            'admin_action' => true
                        ]
                    );
                }
            }
            
            Log::info("Manual rank upgrade: User {$user->username} upgraded from rank {$oldRank} to {$targetRank}. Reason: {$reason}");
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Manual rank upgrade failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }
}