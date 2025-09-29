<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Services\RankUpgradeService;

class TestRankUpgrade extends Command
{
    protected $signature = 'test:rank-upgrade {user_id?}';
    protected $description = 'Test rank upgrade by creating qualifying investments';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::where('rank', 1)->first();
        }
        
        if (!$user) {
            $this->error('No suitable user found for testing');
            return 1;
        }
        
        $this->info("Testing rank upgrade for user: {$user->username} (ID: {$user->id})");
        $this->info("Current rank: {$user->rank}");
        
        // Check current investment
        $currentInvestment = $user->investments()->where('status', 'active')->sum('amount');
        $this->info("Current personal investment: \${$currentInvestment}");
        
        // Get rank 2 requirements
        $rank2Req = \App\Models\RankRequirement::where('rank', 2)->first();
        $needed = $rank2Req->personal_investment - $currentInvestment;
        
        if ($needed > 0) {
            $this->info("Need \${$needed} more investment for rank 2");
            
            // Create an investment plan if none exists
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
                $this->info("Created test investment plan");
            }
            
            // Create qualifying investment
            $investmentAmount = max($needed, 100);
            $investment = Investment::create([
                'user_id' => $user->id,
                'amount' => $investmentAmount,
                'plan_type' => 'starter',
                'plan_id' => $plan->id,
                'start_date' => now(),
                'daily_profit' => ($investmentAmount * 0.008),
                'total_profit' => 0,
                'status' => 'active',
                'profit_days_completed' => 0
            ]);
            
            $this->info("Created investment of \${$investmentAmount}");
        }
        
        // Test rank upgrade
        $this->info("\nTesting rank upgrade service...");
        try {
            $rankService = new RankUpgradeService();
            $oldRank = $user->rank;
            $newRank = $rankService->checkUserRankUpgrade($user->fresh());
            
            if ($newRank > $oldRank) {
                $this->info("✓ Rank upgrade successful! Old: {$oldRank}, New: {$newRank}");
                
                // Check if reward was given
                $reward = \App\Models\RankReward::where('user_id', $user->id)
                    ->where('new_rank', $newRank)
                    ->first();
                    
                if ($reward) {
                    $this->info("✓ Rank reward issued: \${$reward->reward_amount}");
                } else {
                    $this->warn("⚠ No rank reward found");
                }
                
                // Check balance update
                $updatedUser = $user->fresh();
                $this->info("Updated balance: \${$updatedUser->balance}");
                
            } else {
                $this->info("No rank upgrade occurred. Current rank: {$newRank}");
                
                // Check why upgrade didn't happen
                $this->info("\nChecking requirements:");
                $personalInv = $user->fresh()->investments()->where('status', 'active')->sum('amount');
                $teamVolume = $user->fresh()->getTeamBusinessVolume();
                $directRefs = $user->fresh()->referrals()->count();
                
                $this->info("Personal Investment: \${$personalInv} (Required: \${$rank2Req->personal_investment})");
                $this->info("Team Volume: \${$teamVolume} (Required: \${$rank2Req->team_business_volume})");
                $this->info("Direct Referrals: {$directRefs} (Required: {$rank2Req->count_level})");
            }
            
        } catch (\Exception $e) {
            $this->error("Rank upgrade failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
        
        return 0;
    }
}
