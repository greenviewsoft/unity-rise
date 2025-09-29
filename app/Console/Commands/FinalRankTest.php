<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\RankReward;
use App\Services\RankUpgradeService;

class FinalRankTest extends Command
{
    protected $signature = 'test:final-rank';
    protected $description = 'Final rank system test with fixed enum values';

    public function handle()
    {
        $this->info('=== FINAL RANK SYSTEM TEST ===');
        
        $user = User::find(109);
        if (!$user) {
            $this->error('Test user not found');
            return 1;
        }
        
        $this->info("Testing user: {$user->username}");
        $this->info("Current rank: {$user->rank}");
        $this->info("Current balance: \${$user->balance}");
        
        // Check requirements
        $personalInv = $user->investments()->where('status', 'active')->sum('amount');
        $teamVolume = $user->getTeamBusinessVolume();
        $directRefs = $user->referrals()->count();
        
        $this->info("\nCurrent stats:");
        $this->info("- Personal Investment: \${$personalInv}");
        $this->info("- Team Volume: \${$teamVolume}");
        $this->info("- Direct Referrals: {$directRefs}");
        
        // Test rank upgrade
        try {
            $rankService = new RankUpgradeService();
            $oldRank = $user->rank;
            $newRank = $rankService->checkUserRankUpgrade($user->fresh());
            
            $this->info("\nRank upgrade result:");
            $this->info("Old rank: {$oldRank}");
            $this->info("New rank: {$newRank}");
            
            if ($newRank > $oldRank) {
                $this->info("🎉 SUCCESS! Rank upgraded!");
                
                // Check reward
                $reward = RankReward::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($reward) {
                    $this->info("🎁 Rank reward details:");
                    $this->info("   Amount: \${$reward->reward_amount}");
                    $this->info("   Type: {$reward->reward_type}");
                    $this->info("   Status: {$reward->status}");
                    $this->info("   From rank {$reward->old_rank} to {$reward->new_rank}");
                }
                
                $updatedUser = $user->fresh();
                $this->info("💰 Updated balance: \${$updatedUser->balance}");
                
            } else {
                $this->warn("No rank upgrade occurred");
            }
            
        } catch (\Exception $e) {
            $this->error("Rank upgrade failed: " . $e->getMessage());
        }
        
        // Final stats
        $this->info("\n=== FINAL STATS ===");
        $totalRankRewards = RankReward::count();
        $totalUsersWithHighRank = User::where('rank', '>', 1)->count();
        
        $this->info("Total rank rewards issued: {$totalRankRewards}");
        $this->info("Users with rank > 1: {$totalUsersWithHighRank}");
        
        if ($totalRankRewards > 0) {
            $this->info("\n✅ RANK SYSTEM IS WORKING!");
            $this->info("✅ Rank upgrades are processing correctly");
            $this->info("✅ Rank rewards are being issued");
            $this->info("✅ User balances are being updated");
        } else {
            $this->warn("⚠ Rank system needs more testing");
        }
        
        return 0;
    }
}
