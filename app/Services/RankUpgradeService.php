<?php

namespace App\Services;

use App\Models\User;
use App\Models\RankRequirement;
use App\Models\Investment;
use App\Models\RankReward;
use App\Models\History;
use App\Models\Log;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RankUpgradeService
{
    const MAX_CASCADE_DEPTH = 10;
    const MAX_UPLINE_LEVELS = 20;
    const MAX_RANK = 12;
    const BULK_PROCESS_LIMIT = 100;
    const CACHE_TTL = 300; // 5 minutes
    
    /**
     * Get rank names
     */
    private function getRankNames()
    {
        return [
            1 => 'Rookie',
            2 => 'Bronze',
            3 => 'Bronze+',
            4 => 'Silver',
            5 => 'Silver+',
            6 => 'Gold',
            7 => 'Gold+',
            8 => 'Platinum',
            9 => 'Platinum+',
            10 => 'Diamond',
            11 => 'Diamond+',
            12 => 'Master'
        ];
    }

    /**
     * Check and process rank upgrades for a specific user
     */
    public function checkUserRankUpgrade(User $user)
    {
        if (!$user || !$user->exists) {
            throw new \InvalidArgumentException('Invalid user provided');
        }

        try {
            DB::beginTransaction();
            
            $oldRank = $user->rank ?? 0;
            
            // Check if user is already at maximum rank
            if ($oldRank >= self::MAX_RANK) {
                LaravelLog::info("User {$user->username} already at maximum rank {$oldRank}");
                DB::commit();
                return $oldRank;
            }

            // Check for possible rank upgrades
            $newRank = $this->calculateEligibleRank($user);
            
            if ($newRank > $oldRank) {
                // Process rank upgrades step by step
                for ($targetRank = $oldRank + 1; $targetRank <= $newRank; $targetRank++) {
                    $this->processRankUpgrade($user, $targetRank);
                }
                
                LaravelLog::info("User {$user->username} upgraded from rank {$oldRank} to {$newRank}");
                
                // Clear user-specific cache
                $this->clearUserRankCache($user->id);
                
                // Trigger cascade check for upline users
                if ($user->referrer) {
                    $this->cascadeRankCheck($user->referrer);
                }
            }
            
            DB::commit();
            return $newRank;
            
        } catch (\Exception $e) {
            DB::rollBack();
            LaravelLog::error("Rank upgrade failed for user {$user->id}: " . $e->getMessage(), [
                'user_id' => $user->id,
                'old_rank' => $oldRank ?? 0,
                'error' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate the highest rank user is eligible for
     */
    private function calculateEligibleRank(User $user)
    {
        $currentRank = $user->rank ?? 0;
        $eligibleRank = $currentRank;
        
        // Check each rank from current + 1 to max rank
        for ($rank = $currentRank + 1; $rank <= self::MAX_RANK; $rank++) {
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

        // Check if user has required number of levels with referrals
        $activeLevels = $this->getActiveLevelsCount($user, $requirement->count_level);
        
        if ($activeLevels < $requirement->count_level) {
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

        $directReferrals = $user->referrals()->where('status', 'active')->get();
        
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
     * Get count of active levels under user
     */
    private function getActiveLevelsCount(User $user, $requiredLevels)
    {
        $activeLevels = 0;
        $this->countActiveLevelsRecursive($user, $requiredLevels, 0, $activeLevels);
        return $activeLevels;
    }

    /**
     * Recursive function to count active levels
     */
    private function countActiveLevelsRecursive(User $user, $maxLevels, $currentLevel, &$activeLevels)
    {
        if ($currentLevel >= $maxLevels) {
            return;
        }

        $directReferrals = $user->referrals()
                               ->where('status', 'active')
                               ->whereHas('investments', function($query) {
                                   $query->where('status', 'active');
                               })
                               ->get();
        
        if ($directReferrals->count() > 0) {
            $activeLevels = max($activeLevels, $currentLevel + 1);
            
            foreach ($directReferrals as $referral) {
                $this->countActiveLevelsRecursive($referral, $maxLevels, $currentLevel + 1, $activeLevels);
            }
        }
    }

    /**
     * Process individual rank upgrade
     */
    private function processRankUpgrade(User $user, $targetRank)
    {
        $requirement = RankRequirement::where('rank', $targetRank)
                                     ->where('is_active', 1)
                                     ->first();
        
        if (!$requirement) {
            throw new \Exception("Rank requirement not found for rank {$targetRank}");
        }

        $oldRank = $user->rank ?? 0;
        
        // Update user rank
        $user->update([
            'rank' => $targetRank,
            'rank_upgraded_at' => now()
        ]);

        // Process reward if applicable
        if ($requirement->reward_amount > 0) {
            $this->processRankReward($user, $oldRank, $targetRank, $requirement->reward_amount);
        }

        LaravelLog::info("Rank upgrade processed", [
            'user_id' => $user->id,
            'username' => $user->username,
            'old_rank' => $oldRank,
            'new_rank' => $targetRank,
            'reward_amount' => $requirement->reward_amount
        ]);
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
            'reward_type' => 'rank_achievement',
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
            'rank_upgrade_reward',
            $rewardAmount,
            $previousBalance,
            $newBalance,
            'App\\Models\\RankReward',
            $rankReward->id,
            "Rank {$newRank} upgrade reward",
            [
                'old_rank' => $oldRank,
                'new_rank' => $newRank,
                'reward_type' => 'automatic_upgrade'
            ]
        );
    }

    /**
     * Check rank upgrades for upline users (cascade effect)
     */
    private function cascadeRankCheck(?User $uplineUser, $depth = 0)
    {
        if (!$uplineUser || $depth >= self::MAX_CASCADE_DEPTH) {
            return;
        }
        
        $oldRank = $uplineUser->rank ?? 0;
        $newRank = $this->calculateEligibleRank($uplineUser);
        
        if ($newRank > $oldRank) {
            // Process rank upgrades for upline user
            for ($targetRank = $oldRank + 1; $targetRank <= $newRank; $targetRank++) {
                $this->processRankUpgrade($uplineUser, $targetRank);
            }
            
            LaravelLog::info("Cascade upgrade: User {$uplineUser->username} upgraded from rank {$oldRank} to {$newRank}");
            
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
        
        while ($uplineUser && $level <= self::MAX_UPLINE_LEVELS) {
            try {
                $this->checkUserRankUpgrade($uplineUser);
            } catch (\Exception $e) {
                LaravelLog::error("Upline rank check failed for user {$uplineUser->id}: " . $e->getMessage());
            }
            
            $uplineUser = $uplineUser->referrer;
            $level++;
        }
    }

    /**
     * Get user rank statistics
     */
    public function getUserRankStats(User $user)
    {
        $currentRank = $user->rank ?? 0;
        $nextRank = min($currentRank + 1, self::MAX_RANK);
        $nextRequirement = null;
        $eligibilityCheck = null;
        
        if ($nextRank <= self::MAX_RANK) {
            $nextRequirement = RankRequirement::where('rank', $nextRank)
                                             ->where('is_active', 1)
                                             ->first();
            
            if ($nextRequirement) {
                $personalInvestment = $user->investments()
                                          ->where('status', 'active')
                                          ->sum('amount');
                
                $teamBusinessVolume = $this->calculateTeamBusinessVolume($user, $nextRequirement->count_level);
                $activeLevels = $this->getActiveLevelsCount($user, $nextRequirement->count_level);
                
                $eligibilityCheck = [
                    'personal_investment' => [
                        'current' => $personalInvestment,
                        'required' => $nextRequirement->personal_investment,
                        'met' => $personalInvestment >= $nextRequirement->personal_investment
                    ],
                    'team_business_volume' => [
                        'current' => $teamBusinessVolume,
                        'required' => $nextRequirement->team_business_volume,
                        'met' => $teamBusinessVolume >= $nextRequirement->team_business_volume
                    ],
                    'active_levels' => [
                        'current' => $activeLevels,
                        'required' => $nextRequirement->count_level,
                        'met' => $activeLevels >= $nextRequirement->count_level
                    ]
                ];
            }
        }
        
        $rankNames = $this->getRankNames();
        
        return [
            'current_rank' => $currentRank,
            'current_rank_name' => $rankNames[$currentRank] ?? 'Unranked',
            'next_rank' => $nextRank,
            'next_rank_name' => $rankNames[$nextRank] ?? 'Max Rank',
            'next_requirement' => $nextRequirement,
            'eligibility_check' => $eligibilityCheck,
            'is_eligible_for_next' => $nextRequirement ? $this->checkRankEligibility($user, $nextRank) : false
        ];
    }

    /**
     * Clear user-specific rank cache
     */
    private function clearUserRankCache($userId)
    {
        Cache::forget("user_rank_stats_{$userId}");
        Cache::forget('rank_upgrade_stats');
    }

    /**
     * Bulk check rank upgrades
     */
    public function bulkCheckRankUpgrades($limit = null)
    {
        $limit = $limit ?? self::BULK_PROCESS_LIMIT;
        
        $users = User::where('status', 'active')
                    ->whereHas('investments', function($query) {
                        $query->where('status', 'active');
                    })
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
        
        $results = [
            'processed' => 0,
            'upgraded' => 0,
            'errors' => 0,
            'details' => []
        ];
        
        foreach ($users as $user) {
            try {
                $oldRank = $user->rank ?? 0;
                $newRank = $this->checkUserRankUpgrade($user);
                
                $results['processed']++;
                
                if ($newRank > $oldRank) {
                    $results['upgraded']++;
                    $results['details'][] = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank
                    ];
                }
                
            } catch (\Exception $e) {
                $results['errors']++;
                LaravelLog::error("Bulk rank check failed for user {$user->id}: " . $e->getMessage());
                continue;
            }
        }
        
        LaravelLog::info("Bulk rank check completed", $results);
        
        return $results;
    }
}