<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\RankRequirement;
use App\Services\RankUpgradeService;

class DebugRankUpgrade extends Command
{
    protected $signature = 'debug:rank-upgrade {user_id}';
    protected $description = 'Debug rank upgrade service step by step';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error('User not found');
            return 1;
        }
        
        $this->info("Debugging rank upgrade for: {$user->username}");
        
        $rankService = new RankUpgradeService();
        $requirement = RankRequirement::where('rank', 2)->where('is_active', 1)->first();
        
        if (!$requirement) {
            $this->error('Rank 2 requirement not found');
            return 1;
        }
        
        $this->info("Rank 2 Requirements:");
        $this->info("- Personal Investment: \${$requirement->personal_investment}");
        $this->info("- Team Business Volume: \${$requirement->team_business_volume}");
        $this->info("- Count Level: {$requirement->count_level}");
        
        // Test personal investment
        $personalInvestment = $user->investments()
                                  ->where('status', 'active')
                                  ->sum('amount');
        $personalMet = $personalInvestment >= $requirement->personal_investment;
        $this->info("\n1. Personal Investment:");
        $this->info("   Current: \${$personalInvestment}");
        $this->info("   Required: \${$requirement->personal_investment}");
        $this->info("   Met: " . ($personalMet ? "YES" : "NO"));
        
        // Test team business volume using service method
        $teamBusinessVolume = $this->calculateTeamBusinessVolume($user, $requirement->count_level);
        $teamMet = $teamBusinessVolume >= $requirement->team_business_volume;
        $this->info("\n2. Team Business Volume:");
        $this->info("   Current: \${$teamBusinessVolume}");
        $this->info("   Required: \${$requirement->team_business_volume}");
        $this->info("   Met: " . ($teamMet ? "YES" : "NO"));
        
        // Test active levels
        $activeLevels = $this->getActiveLevelsCount($user, $requirement->count_level);
        $levelsMet = $activeLevels >= $requirement->count_level;
        $this->info("\n3. Active Levels:");
        $this->info("   Current: {$activeLevels}");
        $this->info("   Required: {$requirement->count_level}");
        $this->info("   Met: " . ($levelsMet ? "YES" : "NO"));
        
        $allMet = $personalMet && $teamMet && $levelsMet;
        $this->info("\n=== SUMMARY ===");
        $this->info("All requirements met: " . ($allMet ? "YES" : "NO"));
        
        if (!$allMet) {
            if (!$personalMet) $this->error("❌ Personal investment insufficient");
            if (!$teamMet) $this->error("❌ Team business volume insufficient");
            if (!$levelsMet) $this->error("❌ Active levels insufficient");
        } else {
            $this->info("✅ All requirements met - should be eligible for upgrade");
        }
        
        return 0;
    }
    
    // Copy methods from RankUpgradeService for debugging
    private function calculateTeamBusinessVolume(User $user, $maxLevels)
    {
        $totalVolume = 0;
        $this->calculateTeamVolumeRecursive($user, $maxLevels, 0, $totalVolume);
        return $totalVolume;
    }

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

    private function getActiveLevelsCount(User $user, $requiredLevels)
    {
        $activeLevels = 0;
        $this->countActiveLevelsRecursive($user, $requiredLevels, 0, $activeLevels);
        return $activeLevels;
    }

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
}
