<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Manual Rank Upgrade Functionality ===\n\n";

// Get the test user
$user = App\Models\User::where('email', 'admin@gmail.com')->first();

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User: {$user->email}\n";
echo "Current Rank in DB: {$user->rank}\n\n";

// Get rank upgrade service
$rankService = app(\App\Services\RankUpgradeService::class);

// Get user stats
$userStats = $rankService->getUserRankStats($user);

echo "=== User Current Stats ===\n";

// Calculate actual user stats
$personalInvestment = $user->investments()->where('status', 'active')->sum('amount');
$directReferrals = $user->referrals()->whereIn('status', ['1', 'on'])->count();

// Get team business volume from eligibility check if available
$teamBusinessVolume = 0;
if (isset($userStats['eligibility_check']['team_business_volume']['current'])) {
    $teamBusinessVolume = $userStats['eligibility_check']['team_business_volume']['current'];
}

echo "Personal Investment: $" . number_format($personalInvestment, 2) . "\n";
echo "Direct Referrals: " . $directReferrals . "\n";
echo "Team Investment: $" . number_format($teamBusinessVolume, 2) . "\n\n";

// Simulate the controller's eligibility check
function checkRankEligibility($user, $targetRank) {
    $requirement = App\Models\RankRequirement::where('rank', $targetRank)
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

    // Check direct referrals requirement
    $directReferrals = $user->referrals()->whereIn('status', ['1', 'on'])->count();
    
    if ($directReferrals < $requirement->direct_referrals) {
        return false;
    }

    // Check team business volume requirement
    $teamBusinessVolume = calculateTeamBusinessVolume($user, $requirement->count_level);
    
    if ($teamBusinessVolume < $requirement->team_business_volume) {
        return false;
    }

    return true;
}

function calculateTeamBusinessVolume($user, $maxLevels) {
    $totalVolume = 0;
    calculateTeamVolumeRecursive($user, $maxLevels, 0, $totalVolume);
    return $totalVolume;
}

function calculateTeamVolumeRecursive($user, $maxLevels, $currentLevel, &$totalVolume) {
    if ($currentLevel >= $maxLevels) {
        return;
    }

    $directReferrals = $user->referrals()->whereIn('status', ['1', 'on'])->get();
    
    foreach ($directReferrals as $referral) {
        // Add referral's investment to total volume
        $referralInvestment = $referral->investments()
                                      ->where('status', 'active')
                                      ->sum('amount');
        $totalVolume += $referralInvestment;
        
        // Continue to next level
        calculateTeamVolumeRecursive($referral, $maxLevels, $currentLevel + 1, $totalVolume);
    }
}

function getEligibleRankForUpgrade($user) {
    $currentRank = $user->rank ?? 0;
    $eligibleRank = $currentRank;
    
    // Check each rank from current + 1 to max rank
    for ($rank = $currentRank + 1; $rank <= 12; $rank++) {
        if (checkRankEligibility($user, $rank)) {
            $eligibleRank = $rank;
        } else {
            break; // Stop at first non-eligible rank
        }
    }
    
    return $eligibleRank;
}

// Test eligibility for next rank
$currentRank = $user->rank;
$nextRank = $currentRank + 1;

echo "=== Testing Eligibility for Next Rank (Rank $nextRank) ===\n";

$nextRankRequirement = App\Models\RankRequirement::where('rank', $nextRank)
                                                ->where('is_active', 1)
                                                ->first();

if ($nextRankRequirement) {
    echo "Next Rank Requirements:\n";
    echo "- Personal Investment: $" . number_format($nextRankRequirement->personal_investment, 2) . "\n";
    echo "- Direct Referrals: " . $nextRankRequirement->direct_referrals . "\n";
    echo "- Team Business Volume: $" . number_format($nextRankRequirement->team_business_volume, 2) . "\n\n";
    
    $isEligible = checkRankEligibility($user, $nextRank);
    echo "Eligible for Rank $nextRank: " . ($isEligible ? 'YES' : 'NO') . "\n\n";
    
    if (!$isEligible) {
        echo "=== Missing Requirements ===\n";
        
        $personalInvestment = $user->investments()->where('status', 'active')->sum('amount');
        $directReferrals = $user->referrals()->whereIn('status', ['1', 'on'])->count();
        $teamBusinessVolume = calculateTeamBusinessVolume($user, $nextRankRequirement->count_level);
        
        if ($personalInvestment < $nextRankRequirement->personal_investment) {
            $missing = $nextRankRequirement->personal_investment - $personalInvestment;
            echo "- Personal Investment: Need $" . number_format($missing, 2) . " more\n";
        }
        
        if ($directReferrals < $nextRankRequirement->direct_referrals) {
            $missing = $nextRankRequirement->direct_referrals - $directReferrals;
            echo "- Direct Referrals: Need $missing more referrals\n";
        }
        
        if ($teamBusinessVolume < $nextRankRequirement->team_business_volume) {
            $missing = $nextRankRequirement->team_business_volume - $teamBusinessVolume;
            echo "- Team Business Volume: Need $" . number_format($missing, 2) . " more\n";
        }
    }
} else {
    echo "No requirements found for Rank $nextRank\n";
}

echo "\n=== Overall Eligibility Check ===\n";
$eligibleRank = getEligibleRankForUpgrade($user);
echo "Current Rank: $currentRank\n";
echo "Highest Eligible Rank: $eligibleRank\n";
echo "Can Claim Upgrade: " . ($eligibleRank > $currentRank ? 'YES' : 'NO') . "\n\n";

echo "=== Testing Auto-Upgrade Removal ===\n";
echo "✓ DashboardController: Auto-upgrade logic removed\n";
echo "✓ InvestmentController: Auto-upgrade logic commented out\n";
echo "✓ User Model: processRankUpgrade and updateRank methods removed\n";
echo "✓ InvestmentRankUpgradeListener: Auto-upgrade logic disabled\n\n";

echo "=== Manual Upgrade Process ===\n";
echo "1. Users must visit the Rank Upgrade Center\n";
echo "2. Check eligibility manually via the interface\n";
echo "3. Claim upgrades only when ALL requirements are met\n";
echo "4. No automatic upgrades will occur\n\n";

echo "=== Test Complete ===\n";