<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RankRequirement;
use App\Models\User;
use App\Models\Investment;
use App\Models\RankReward;
use App\Services\RankUpgradeService;

class TestRankSystem extends Command
{
    protected $signature = 'test:rank-system';
    protected $description = 'Test the rank system functionality';

    public function handle()
    {
        $this->info('=== RANK SYSTEM TEST ===');
        
        // 1. Check rank requirements
        $this->info("\n1. RANK REQUIREMENTS:");
        $ranks = RankRequirement::orderBy('rank')->get();
        foreach($ranks as $rank) {
            $this->info("Rank {$rank->rank} ({$rank->rank_name}): Personal: \${$rank->personal_investment}, Team: \${$rank->team_business_volume}, Levels: {$rank->count_level}, Reward: \${$rank->reward_amount}");
        }
        
        // 2. Current user ranks
        $this->info("\n2. CURRENT USER RANKS:");
        $userRanks = User::selectRaw('`rank`, COUNT(*) as count')
            ->groupBy('rank')
            ->orderBy('rank')
            ->get();
        foreach($userRanks as $ur) {
            $this->info("Rank {$ur->rank}: {$ur->count} users");
        }
        
        // 3. Test a specific user
        $this->info("\n3. SAMPLE USER TEST:");
        $testUser = User::where('rank', 1)->first();
        if($testUser) {
            $this->info("User: {$testUser->username} (ID: {$testUser->id})");
            $this->info("Current Rank: {$testUser->rank}");
            $this->info("Balance: \${$testUser->balance}");
            
            // Check investments
            $personalInvestment = $testUser->investments()->where('status', 'active')->sum('amount');
            $this->info("Personal Investment: \${$personalInvestment}");
            
            // Check referrals
            $directReferrals = $testUser->referrals()->count();
            $this->info("Direct Referrals: {$directReferrals}");
            
            // Check team volume
            $teamVolume = $testUser->getTeamBusinessVolume();
            $this->info("Team Business Volume: \${$teamVolume}");
            
            // Check eligibility for next rank
            $nextRank = RankRequirement::where('rank', $testUser->rank + 1)->first();
            if($nextRank) {
                $this->info("\nRequirements for Rank " . ($testUser->rank + 1) . ":");
                $this->info("- Personal Investment: \${$nextRank->personal_investment} (Current: \${$personalInvestment})");
                $this->info("- Team Volume: \${$nextRank->team_business_volume} (Current: \${$teamVolume})");
                $this->info("- Levels: {$nextRank->count_level} (Current: {$directReferrals})");
                
                $eligible = ($personalInvestment >= $nextRank->personal_investment &&
                            $teamVolume >= $nextRank->team_business_volume &&
                            $directReferrals >= $nextRank->count_level);
                
                $this->info("Eligible for upgrade: " . ($eligible ? "YES" : "NO"));
                
                if($eligible) {
                    $this->info("Testing rank upgrade...");
                    try {
                        $rankService = new RankUpgradeService();
                        $newRank = $rankService->checkUserRankUpgrade($testUser);
                        $this->info("Rank upgrade result: New rank = {$newRank}");
                    } catch(\Exception $e) {
                        $this->error("Rank upgrade failed: " . $e->getMessage());
                    }
                }
            }
        } else {
            $this->info("No rank 1 users found");
        }
        
        // 4. Rank rewards
        $this->info("\n4. RANK REWARDS:");
        $rankRewards = RankReward::count();
        $this->info("Total rank rewards issued: {$rankRewards}");
        
        if($rankRewards > 0) {
            $recentRewards = RankReward::orderBy('created_at', 'desc')->take(3)->get();
            foreach($recentRewards as $reward) {
                $user = User::find($reward->user_id);
                $this->info("User {$user->username}: Rank {$reward->old_rank} -> {$reward->new_rank}, Reward: \${$reward->reward_amount}");
            }
        }
        
        // 5. Investment activity
        $this->info("\n5. INVESTMENT ACTIVITY:");
        $activeInvestments = Investment::where('status', 'active')->count();
        $totalInvestmentAmount = Investment::where('status', 'active')->sum('amount');
        $this->info("Active investments: {$activeInvestments}");
        $this->info("Total investment amount: \${$totalInvestmentAmount}");
        
        // 6. Test rank service functionality
        $this->info("\n6. RANK SERVICE TEST:");
        try {
            $rankService = new RankUpgradeService();
            $this->info("✓ RankUpgradeService instantiated successfully");
            
            // Test bulk check
            $results = $rankService->bulkCheckRankUpgrades(5);
            $this->info("Bulk check results: Processed: {$results['processed']}, Upgraded: {$results['upgraded']}, Errors: {$results['errors']}");
            
        } catch(\Exception $e) {
            $this->error("✗ RankUpgradeService error: " . $e->getMessage());
        }
        
        $this->info("\n=== TEST COMPLETE ===");
        
        // Summary
        $totalUsers = User::count();
        $highRankUsers = User::where('rank', '>', 1)->count();
        $rankSystemActive = ($ranks->count() == 12 && $activeInvestments > 0);
        
        $this->info("\n=== SUMMARY ===");
        $this->info("Total Users: {$totalUsers}");
        $this->info("Users with Rank > 1: {$highRankUsers}");
        $this->info("Rank Requirements Configured: " . $ranks->count() . "/12");
        $this->info("Active Investments: {$activeInvestments}");
        $this->info("Rank System Status: " . ($rankSystemActive ? "✓ ACTIVE" : "✗ INACTIVE"));
        
        return 0;
    }
}
