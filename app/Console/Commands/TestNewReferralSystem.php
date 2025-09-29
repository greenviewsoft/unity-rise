<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ReferralCommission;
use App\Models\ReferralCommissionLevel;
use App\Models\RankRequirement;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\InvestmentProfit;
use App\Services\ReferralCommissionService;

class TestNewReferralSystem extends Command
{
    protected $signature = 'test:new-referral-system {--show-structure} {--test-commissions}';
    protected $description = 'Test the new referral system with updated models';

    public function handle()
    {
        $this->info('=== NEW REFERRAL SYSTEM TEST ===');
        
        if ($this->option('show-structure')) {
            $this->showSystemStructure();
        }
        
        if ($this->option('test-commissions')) {
            $this->testCommissionSystem();
        }
        
        if (!$this->option('show-structure') && !$this->option('test-commissions')) {
            $this->showOverview();
        }
        
        return 0;
    }
    
    private function showOverview()
    {
        $this->info("\n1. SYSTEM OVERVIEW:");
        
        // Check new models
        $this->info("New Models Status:");
        $this->checkModel('ReferralCommission', ReferralCommission::class);
        $this->checkModel('ReferralCommissionLevel', ReferralCommissionLevel::class);
        $this->checkModel('RankRequirement', RankRequirement::class);
        $this->checkModel('Investment', Investment::class);
        $this->checkModel('InvestmentPlan', InvestmentPlan::class);
        $this->checkModel('InvestmentProfit', InvestmentProfit::class);
        
        // Check old Commission model
        if (class_exists('App\Models\Commission')) {
            $this->error("✗ Old Commission model still exists");
        } else {
            $this->info("✓ Old Commission model removed");
        }
        
        $this->info("\n2. DATA SUMMARY:");
        $this->info("Users: " . User::count());
        $this->info("Referral Commissions: " . ReferralCommission::count());
        $this->info("Commission Levels: " . ReferralCommissionLevel::count());
        $this->info("Rank Requirements: " . RankRequirement::count());
        $this->info("Investment Plans: " . InvestmentPlan::count());
        $this->info("Active Investments: " . Investment::where('status', 'active')->count());
        
        $this->info("\n=== OPTIONS ===");
        $this->info("--show-structure     Show detailed system structure");
        $this->info("--test-commissions   Test commission calculations");
    }
    
    private function checkModel($name, $class)
    {
        try {
            $count = $class::count();
            $this->info("✓ {$name}: {$count} records");
        } catch (\Exception $e) {
            $this->error("✗ {$name}: Error - " . $e->getMessage());
        }
    }
    
    private function showSystemStructure()
    {
        $this->info("\n=== SYSTEM STRUCTURE ===");
        
        $this->info("\n1. REFERRAL COMMISSION LEVELS:");
        $levels = ReferralCommissionLevel::orderBy('rank_id')->orderBy('level')->get();
        
        if ($levels->count() > 0) {
            foreach ($levels as $level) {
                $this->info("Rank {$level->rank_id} ({$level->rank_name}) - Level {$level->level}: {$level->commission_rate}%");
            }
        } else {
            $this->warn("No commission levels configured");
        }
        
        $this->info("\n2. RANK REQUIREMENTS:");
        $ranks = RankRequirement::orderBy('rank')->get();
        
        if ($ranks->count() > 0) {
            foreach ($ranks as $rank) {
                $this->info("Rank {$rank->rank} ({$rank->rank_name}): Personal \${$rank->personal_investment}, Team \${$rank->team_business_volume}, Levels {$rank->count_level}");
            }
        } else {
            $this->warn("No rank requirements configured");
        }
        
        $this->info("\n3. INVESTMENT PLANS:");
        $plans = InvestmentPlan::orderBy('min_amount')->get();
        
        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $this->info("Plan {$plan->name}: \${$plan->min_amount}-\${$plan->max_amount}, {$plan->daily_profit_percentage}% daily for {$plan->duration} days");
            }
        } else {
            $this->warn("No investment plans configured");
        }
    }
    
    private function testCommissionSystem()
    {
        $this->info("\n=== COMMISSION SYSTEM TEST ===");
        
        try {
            $referralService = new ReferralCommissionService();
            $this->info("✓ ReferralCommissionService loaded successfully");
            
            // Test commission calculation
            $this->info("\n1. TESTING COMMISSION RATES:");
            
            $ranks = RankRequirement::orderBy('rank')->take(3)->get();
            foreach ($ranks as $rank) {
                $this->info("Testing Rank {$rank->rank} ({$rank->rank_name}):");
                
                $rates = ReferralCommissionLevel::getCommissionRatesForRank($rank->rank);
                if (!empty($rates)) {
                    foreach ($rates as $level => $rate) {
                        $this->info("  Level {$level}: {$rate}%");
                    }
                } else {
                    $this->warn("  No commission rates found for this rank");
                }
            }
            
            // Test with sample users
            $this->info("\n2. TESTING WITH USERS:");
            $users = User::where('rank', '>', 0)->take(3)->get();
            
            foreach ($users as $user) {
                $this->info("User {$user->username} (Rank {$user->rank}):");
                
                $commissions = ReferralCommission::where('referrer_id', $user->id)->count();
                $totalCommission = ReferralCommission::where('referrer_id', $user->id)->sum('commission_amount');
                
                $this->info("  Commissions earned: {$commissions} (\${$totalCommission})");
                
                $directReferrals = $user->referrals()->count();
                $this->info("  Direct referrals: {$directReferrals}");
            }
            
        } catch (\Exception $e) {
            $this->error("Commission system test failed: " . $e->getMessage());
        }
    }
}
