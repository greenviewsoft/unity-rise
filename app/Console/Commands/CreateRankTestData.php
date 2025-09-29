<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Services\RankUpgradeService;

class CreateRankTestData extends Command
{
    protected $signature = 'test:create-rank-data';
    protected $description = 'Create test data to fully test rank system';

    public function handle()
    {
        $this->info('Creating comprehensive rank test data...');
        
        // Create investment plan if needed
        $plan = InvestmentPlan::where('status', true)->first();
        if (!$plan) {
            $plan = InvestmentPlan::create([
                'name' => 'Test Plan',
                'min_amount' => 100,
                'max_amount' => 10000,
                'daily_profit_percentage' => 1.0,
                'total_profit_percentage' => 50.0,
                'duration_days' => 50,
                'status' => true,
                'description' => 'Test investment plan',
                'created_by' => 1
            ]);
        }
        
        // Find or create main test user
        $mainUser = User::where('username', 'rank_test_main')->first();
        if (!$mainUser) {
            $mainUser = User::create([
                'name' => 'Rank Test Main',
                'username' => 'rank_test_main',
                'email' => 'ranktest@test.com',
                'password' => bcrypt('password'),
                'type' => 'user',
                'rank' => 1,
                'balance' => 0,
                'refer_commission' => 0,
                'status' => 'on'
            ]);
        }
        
        $this->info("Main test user: {$mainUser->username} (ID: {$mainUser->id})");
        
        // Create personal investment for main user
        $mainInvestment = Investment::create([
            'user_id' => $mainUser->id,
            'amount' => 600, // More than required $500
            'plan_type' => 'starter',
            'plan_id' => $plan->id,
            'start_date' => now(),
            'daily_profit' => 600 * 0.008,
            'total_profit' => 0,
            'status' => 'active',
            'profit_days_completed' => 0
        ]);
        
        $this->info("Created main user investment: \${$mainInvestment->amount}");
        
        // Create referral chain with investments to build team volume
        $referrers = [];
        $totalTeamVolume = 0;
        
        for ($i = 1; $i <= 15; $i++) { // Need 9+ for rank 2, creating 15 to be safe
            $referrer = User::create([
                'name' => "Rank Test Referral {$i}",
                'username' => "rank_test_ref{$i}",
                'email' => "ranktest{$i}@test.com",
                'password' => bcrypt('password'),
                'type' => 'user',
                'rank' => 1,
                'balance' => 0,
                'refer_commission' => 0,
                'refer_id' => $mainUser->id, // Direct referral to main user
                'status' => 'on'
            ]);
            
            // Create investment for this referral
            $investmentAmount = rand(200, 400);
            $investment = Investment::create([
                'user_id' => $referrer->id,
                'amount' => $investmentAmount,
                'plan_type' => 'starter',
                'plan_id' => $plan->id,
                'start_date' => now(),
                'daily_profit' => $investmentAmount * 0.008,
                'total_profit' => 0,
                'status' => 'active',
                'profit_days_completed' => 0
            ]);
            
            $totalTeamVolume += $investmentAmount;
            $referrers[] = $referrer;
            
            $this->info("Created referral {$i}: {$referrer->username} with \${$investmentAmount} investment");
        }
        
        $this->info("Total team volume created: \${$totalTeamVolume}");
        
        // Test the rank upgrade
        $this->info("\nTesting rank upgrade for main user...");
        
        $mainUser = $mainUser->fresh();
        $personalInv = $mainUser->investments()->where('status', 'active')->sum('amount');
        $teamVolume = $mainUser->getTeamBusinessVolume();
        $directRefs = $mainUser->referrals()->count();
        
        $this->info("Current stats:");
        $this->info("- Personal Investment: \${$personalInv}");
        $this->info("- Team Volume: \${$teamVolume}");
        $this->info("- Direct Referrals: {$directRefs}");
        
        // Check rank 2 requirements
        $rank2Req = \App\Models\RankRequirement::where('rank', 2)->first();
        $this->info("\nRank 2 requirements:");
        $this->info("- Personal Investment: \${$rank2Req->personal_investment}");
        $this->info("- Team Volume: \${$rank2Req->team_business_volume}");
        $this->info("- Direct Referrals: {$rank2Req->count_level}");
        
        $eligible = ($personalInv >= $rank2Req->personal_investment &&
                    $teamVolume >= $rank2Req->team_business_volume &&
                    $directRefs >= $rank2Req->count_level);
        
        $this->info("\nEligible for rank 2: " . ($eligible ? "YES" : "NO"));
        
        if ($eligible) {
            try {
                $rankService = new RankUpgradeService();
                $oldRank = $mainUser->rank;
                $newRank = $rankService->checkUserRankUpgrade($mainUser);
                
                if ($newRank > $oldRank) {
                    $this->info("✓ SUCCESS! Rank upgraded from {$oldRank} to {$newRank}");
                    
                    // Check reward
                    $reward = \App\Models\RankReward::where('user_id', $mainUser->id)
                        ->where('new_rank', $newRank)
                        ->first();
                        
                    if ($reward) {
                        $this->info("✓ Rank reward issued: \${$reward->reward_amount}");
                    }
                    
                    $updatedUser = $mainUser->fresh();
                    $this->info("✓ Updated balance: \${$updatedUser->balance}");
                    
                } else {
                    $this->warn("⚠ No rank upgrade occurred despite meeting requirements");
                }
                
            } catch (\Exception $e) {
                $this->error("Rank upgrade failed: " . $e->getMessage());
            }
        } else {
            $this->warn("User does not meet all requirements yet");
        }
        
        $this->info("\n=== RANK SYSTEM TEST DATA CREATION COMPLETE ===");
        
        return 0;
    }
}
