<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Services\RankUpgradeService;

class CreateDeepReferrals extends Command
{
    protected $signature = 'test:create-deep-referrals {user_id}';
    protected $description = 'Create deep referral levels for rank testing';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $mainUser = User::find($userId);
        
        if (!$mainUser) {
            $this->error('User not found');
            return 1;
        }
        
        $this->info("Creating deep referral structure for: {$mainUser->username}");
        
        $plan = InvestmentPlan::where('status', true)->first();
        
        // Create 10 levels deep referral chain
        $currentReferrer = $mainUser;
        $createdUsers = [];
        
        for ($level = 1; $level <= 10; $level++) {
            $levelUser = User::create([
                'name' => "Level {$level} User",
                'username' => "level_{$level}_user",
                'email' => "level{$level}@test.com",
                'password' => bcrypt('password'),
                'type' => 'user',
                'rank' => 1,
                'balance' => 0,
                'refer_commission' => 0,
                'refer_id' => $currentReferrer->id,
                'status' => 'active'
            ]);
            
            // Create investment for this level user
            $investmentAmount = rand(300, 500);
            Investment::create([
                'user_id' => $levelUser->id,
                'amount' => $investmentAmount,
                'plan_type' => 'starter',
                'plan_id' => $plan->id,
                'start_date' => now(),
                'daily_profit' => $investmentAmount * 0.008,
                'total_profit' => 0,
                'status' => 'active',
                'profit_days_completed' => 0
            ]);
            
            $this->info("Created Level {$level}: {$levelUser->username} -> \${$investmentAmount} (referred by {$currentReferrer->username})");
            
            $createdUsers[] = $levelUser;
            $currentReferrer = $levelUser; // Next level refers to this user
        }
        
        $this->info("\nTesting active levels count...");
        
        // Test the levels count
        $rankService = new RankUpgradeService();
        $reflection = new \ReflectionClass($rankService);
        $method = $reflection->getMethod('getActiveLevelsCount');
        $method->setAccessible(true);
        
        $activeLevels = $method->invoke($rankService, $mainUser->fresh(), 10);
        $this->info("Active levels detected: {$activeLevels}");
        
        // Test team volume
        $teamVolumeMethod = $reflection->getMethod('calculateTeamBusinessVolume');
        $teamVolumeMethod->setAccessible(true);
        $teamVolume = $teamVolumeMethod->invoke($rankService, $mainUser->fresh(), 10);
        $this->info("Team volume: \${$teamVolume}");
        
        // Now test rank upgrade
        $this->info("\nTesting rank upgrade...");
        $personalInv = $mainUser->investments()->where('status', 'active')->sum('amount');
        $directRefs = $mainUser->referrals()->count();
        
        $this->info("Final stats:");
        $this->info("- Personal Investment: \${$personalInv}");
        $this->info("- Team Volume: \${$teamVolume}");
        $this->info("- Direct Referrals: {$directRefs}");
        $this->info("- Active Levels: {$activeLevels}");
        
        $rank2Req = \App\Models\RankRequirement::where('rank', 2)->first();
        $eligible = ($personalInv >= $rank2Req->personal_investment &&
                    $teamVolume >= $rank2Req->team_business_volume &&
                    $activeLevels >= $rank2Req->count_level);
        
        $this->info("Eligible for rank 2: " . ($eligible ? "YES" : "NO"));
        
        if ($eligible) {
            try {
                $oldRank = $mainUser->rank;
                $newRank = $rankService->checkUserRankUpgrade($mainUser->fresh());
                
                if ($newRank > $oldRank) {
                    $this->info("🎉 SUCCESS! Rank upgraded from {$oldRank} to {$newRank}");
                    
                    $reward = \App\Models\RankReward::where('user_id', $mainUser->id)
                        ->where('new_rank', $newRank)
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    if ($reward) {
                        $this->info("🎁 Rank reward issued: \${$reward->reward_amount}");
                        $this->info("💰 User balance updated: \${$mainUser->fresh()->balance}");
                    }
                    
                } else {
                    $this->warn("⚠ Rank upgrade still failed");
                }
                
            } catch (\Exception $e) {
                $this->error("Rank upgrade error: " . $e->getMessage());
                $this->error($e->getTraceAsString());
            }
        }
        
        return 0;
    }
}
