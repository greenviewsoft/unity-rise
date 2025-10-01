<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RankRequirement;
use App\Models\ReferralCommissionLevel;
use App\Services\ReferralCommissionService;
use App\Services\RankUpgradeService;
use Illuminate\Support\Facades\DB;

echo "=== USER RANK UPGRADE TEST SCRIPT ===\n\n";

class RankUpgradeTest
{
    private $referralService;
    private $rankUpgradeService;

    public function __construct()
    {
        $this->referralService = new ReferralCommissionService();
        $this->rankUpgradeService = new RankUpgradeService();
    }

    public function runTests()
    {
        echo "1. CHECKING RANK REQUIREMENTS CONFIGURATION\n";
        $this->checkRankRequirements();
        
        echo "\n2. CHECKING COMMISSION LEVELS CONFIGURATION\n";
        $this->checkCommissionLevels();
        
        echo "\n3. TESTING USERS FROM DATABASE\n";
        $this->testUsersFromDatabase();
        
        echo "\n4. TESTING RANK UPGRADE LOGIC\n";
        $this->testRankUpgradeLogic();
        
        echo "\n5. SIMULATING RANK UPGRADES\n";
        $this->simulateRankUpgrades();
    }

    private function checkRankRequirements()
    {
        try {
            $requirements = RankRequirement::where('is_active', 1)
                ->orderBy('rank')
                ->get();

            if ($requirements->count() > 0) {
                echo "✓ Found {$requirements->count()} active rank requirements\n";
                
                foreach ($requirements as $req) {
                    echo sprintf(
                        "  Rank %d (%s): Personal: $%s, Team: $%s, Referrals: %d, Reward: $%s\n",
                        $req->rank,
                        $req->rank_name,
                        number_format($req->personal_investment, 2),
                        number_format($req->team_business_volume, 2),
                        $req->count_level,
                        number_format($req->reward_amount, 2)
                    );
                }
            } else {
                echo "✗ No active rank requirements found!\n";
            }
        } catch (Exception $e) {
            echo "✗ Error checking rank requirements: " . $e->getMessage() . "\n";
        }
    }

    private function checkCommissionLevels()
    {
        try {
            $levels = ReferralCommissionLevel::where('is_active', 1)
                ->orderBy('rank_id')
                ->orderBy('level')
                ->get();

            if ($levels->count() > 0) {
                echo "✓ Found {$levels->count()} active commission levels\n";
                
                $groupedLevels = $levels->groupBy('rank_id');
                foreach ($groupedLevels as $rankId => $rankLevels) {
                    echo "  Rank {$rankId}: ";
                    $rates = $rankLevels->pluck('commission_rate')->toArray();
                    echo implode('%, ', $rates) . "%\n";
                }
            } else {
                echo "✗ No active commission levels found!\n";
            }
        } catch (Exception $e) {
            echo "✗ Error checking commission levels: " . $e->getMessage() . "\n";
        }
    }

    private function testUsersFromDatabase()
    {
        // Test specific users from your database
        $testUserIds = [2, 135, 136, 139, 140, 141]; // Based on your user table data
        
        foreach ($testUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                echo "\n--- Testing User ID: {$userId} ({$user->email}) ---\n";
                $this->analyzeUserRankEligibility($user);
            }
        }
    }

    private function analyzeUserRankEligibility($user)
    {
        try {
            echo "Current Rank: {$user->rank}\n";
            echo "Balance: $" . number_format($user->balance, 2) . "\n";
            echo "Refer Commission: $" . number_format($user->refer_commission, 2) . "\n";
            
            // Calculate user stats
            $userStats = $this->calculateUserStats($user);
            echo "Personal Investment: $" . number_format($userStats['personal_investment'], 2) . "\n";
            echo "Team Investment: $" . number_format($userStats['team_investment'], 2) . "\n";
            echo "Direct Referrals: " . $userStats['direct_referrals'] . "\n";
            
            // Check next rank eligibility
            $nextRank = $user->rank + 1;
            $nextRequirement = RankRequirement::where('rank', $nextRank)
                ->where('is_active', 1)
                ->first();
                
            if ($nextRequirement) {
                echo "\nNext Rank ({$nextRank}) Requirements:\n";
                echo "  Personal Investment Required: $" . number_format($nextRequirement->personal_investment, 2) . "\n";
                echo "  Team Investment Required: $" . number_format($nextRequirement->team_business_volume, 2) . "\n";
                echo "  Direct Referrals Required: " . $nextRequirement->count_level . "\n";
                echo "  Reward Amount: $" . number_format($nextRequirement->reward_amount, 2) . "\n";
                
                // Check eligibility
                $eligible = $this->checkRankEligibility($userStats, $nextRequirement);
                echo "\nEligibility Status: " . ($eligible ? "✓ ELIGIBLE" : "✗ NOT ELIGIBLE") . "\n";
                
                if (!$eligible) {
                    $this->showMissingRequirements($userStats, $nextRequirement);
                }
            } else {
                echo "\nUser has reached maximum rank!\n";
            }
            
        } catch (Exception $e) {
            echo "✗ Error analyzing user: " . $e->getMessage() . "\n";
        }
    }

    private function calculateUserStats($user)
    {
        // Calculate personal investment (from investments table)
        $personalInvestment = DB::table('investments')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('amount');

        // Calculate team investment (from downline)
        $teamInvestment = $this->calculateTeamInvestment($user->id);

        // Calculate direct referrals
        $directReferrals = User::where('refer_id', $user->id)->count();

        return [
            'personal_investment' => $personalInvestment,
            'team_investment' => $teamInvestment,
            'direct_referrals' => $directReferrals
        ];
    }

    private function calculateTeamInvestment($userId)
    {
        // Get all downline users
        $downlineUsers = $this->getDownlineUsers($userId);
        
        if (empty($downlineUsers)) {
            return 0;
        }

        // Calculate total investment from downline
        return DB::table('investments')
            ->whereIn('user_id', $downlineUsers)
            ->where('status', 'active')
            ->sum('amount');
    }

    private function getDownlineUsers($userId, $depth = 10)
    {
        $downline = [];
        $currentLevel = [$userId];
        
        for ($i = 0; $i < $depth; $i++) {
            $nextLevel = User::whereIn('refer_id', $currentLevel)
                ->pluck('id')
                ->toArray();
                
            if (empty($nextLevel)) {
                break;
            }
            
            $downline = array_merge($downline, $nextLevel);
            $currentLevel = $nextLevel;
        }
        
        return $downline;
    }

    private function checkRankEligibility($userStats, $requirement)
    {
        return $userStats['personal_investment'] >= $requirement->personal_investment &&
               $userStats['team_investment'] >= $requirement->team_business_volume &&
               $userStats['direct_referrals'] >= $requirement->count_level;
    }

    private function showMissingRequirements($userStats, $requirement)
    {
        echo "\nMissing Requirements:\n";
        
        if ($userStats['personal_investment'] < $requirement->personal_investment) {
            $missing = $requirement->personal_investment - $userStats['personal_investment'];
            echo "  Personal Investment: Need $" . number_format($missing, 2) . " more\n";
        }
        
        if ($userStats['team_investment'] < $requirement->team_business_volume) {
            $missing = $requirement->team_business_volume - $userStats['team_investment'];
            echo "  Team Investment: Need $" . number_format($missing, 2) . " more\n";
        }
        
        if ($userStats['direct_referrals'] < $requirement->count_level) {
            $missing = $requirement->count_level - $userStats['direct_referrals'];
            echo "  Direct Referrals: Need {$missing} more\n";
        }
    }

    private function testRankUpgradeLogic()
    {
        echo "Testing rank upgrade service...\n";
        
        try {
            // Test with user ID 141 (level5@gmail.com) who has good stats
            $testUser = User::find(141);
            if ($testUser) {
                echo "Testing with user: {$testUser->email}\n";
                
                // Check if user can be upgraded
                $userStats = $this->calculateUserStats($testUser);
                $this->analyzeUserRankEligibility($testUser);
                
                // Test the actual upgrade logic (without executing)
                echo "\nTesting upgrade logic (dry run)...\n";
                $this->dryRunRankUpgrade($testUser);
            }
        } catch (Exception $e) {
            echo "✗ Error testing rank upgrade logic: " . $e->getMessage() . "\n";
        }
    }

    private function dryRunRankUpgrade($user)
    {
        $currentRank = $user->rank;
        $nextRank = $currentRank + 1;
        
        echo "Current Rank: {$currentRank}\n";
        echo "Testing upgrade to Rank: {$nextRank}\n";
        
        $requirement = RankRequirement::where('rank', $nextRank)
            ->where('is_active', 1)
            ->first();
            
        if (!$requirement) {
            echo "✗ No requirement found for rank {$nextRank}\n";
            return;
        }
        
        $userStats = $this->calculateUserStats($user);
        $eligible = $this->checkRankEligibility($userStats, $requirement);
        
        if ($eligible) {
            echo "✓ User is eligible for rank upgrade!\n";
            echo "  Would receive reward: $" . number_format($requirement->reward_amount, 2) . "\n";
            echo "  New rank would be: {$nextRank} ({$requirement->rank_name})\n";
        } else {
            echo "✗ User is not eligible for rank upgrade\n";
            $this->showMissingRequirements($userStats, $requirement);
        }
    }

    private function simulateRankUpgrades()
    {
        echo "Simulating rank upgrades for test users...\n";
        
        // Create test scenarios
        $scenarios = [
            [
                'user_id' => 141,
                'description' => 'Test user with existing investments',
                'add_investment' => 1000,
                'add_team_volume' => 5000
            ],
            [
                'user_id' => 140,
                'description' => 'Test user with moderate stats',
                'add_investment' => 500,
                'add_team_volume' => 2000
            ]
        ];
        
        foreach ($scenarios as $scenario) {
            echo "\n--- Scenario: {$scenario['description']} ---\n";
            $this->simulateScenario($scenario);
        }
    }

    private function simulateScenario($scenario)
    {
        $user = User::find($scenario['user_id']);
        if (!$user) {
            echo "✗ User not found\n";
            return;
        }
        
        echo "User: {$user->email}\n";
        
        // Calculate current stats
        $currentStats = $this->calculateUserStats($user);
        echo "Current Stats:\n";
        echo "  Personal: $" . number_format($currentStats['personal_investment'], 2) . "\n";
        echo "  Team: $" . number_format($currentStats['team_investment'], 2) . "\n";
        echo "  Referrals: " . $currentStats['direct_referrals'] . "\n";
        
        // Simulate adding investments
        $newStats = [
            'personal_investment' => $currentStats['personal_investment'] + $scenario['add_investment'],
            'team_investment' => $currentStats['team_investment'] + $scenario['add_team_volume'],
            'direct_referrals' => $currentStats['direct_referrals']
        ];
        
        echo "\nSimulated Stats (after adding investments):\n";
        echo "  Personal: $" . number_format($newStats['personal_investment'], 2) . "\n";
        echo "  Team: $" . number_format($newStats['team_investment'], 2) . "\n";
        echo "  Referrals: " . $newStats['direct_referrals'] . "\n";
        
        // Check eligibility with new stats
        $nextRank = $user->rank + 1;
        $requirement = RankRequirement::where('rank', $nextRank)
            ->where('is_active', 1)
            ->first();
            
        if ($requirement) {
            $eligible = $this->checkRankEligibility($newStats, $requirement);
            echo "\nRank {$nextRank} Eligibility: " . ($eligible ? "✓ ELIGIBLE" : "✗ NOT ELIGIBLE") . "\n";
            
            if ($eligible) {
                echo "  Would upgrade to: {$requirement->rank_name}\n";
                echo "  Would receive reward: $" . number_format($requirement->reward_amount, 2) . "\n";
            } else {
                $this->showMissingRequirements($newStats, $requirement);
            }
        }
    }
}

// Run the tests
try {
    $tester = new RankUpgradeTest();
    $tester->runTests();
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "Check the output above for rank upgrade analysis.\n";
    echo "To actually perform rank upgrades, use the web interface or artisan commands.\n";
    
} catch (Exception $e) {
    echo "✗ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}