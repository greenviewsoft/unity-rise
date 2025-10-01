<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RankRequirement;
use Illuminate\Support\Facades\DB;

echo "=== RANK UPGRADE CHECK SIMULATION ===\n\n";

function checkRankUpgradeEligibility($currentRank, $personalInvestment, $teamBusinessVolume, $referrals) {
    echo "🔍 CHECKING RANK UPGRADE ELIGIBILITY\n";
    echo "=====================================\n\n";
    
    // Current rank info
    echo "👤 CURRENT USER STATUS:\n";
    echo "Current Rank: {$currentRank} (Rookie)\n";
    echo "Personal Investment: $" . number_format($personalInvestment, 2) . "\n";
    echo "Team Business Volume: $" . number_format($teamBusinessVolume, 2) . "\n";
    echo "Referrals: {$referrals}\n\n";
    
    // Get next rank requirements
    $nextRank = $currentRank + 1;
    $requirement = RankRequirement::where('rank', $nextRank)
        ->where('is_active', 1)
        ->first();
    
    if (!$requirement) {
        echo "❌ No next rank available!\n";
        return false;
    }
    
    echo "🎯 NEXT RANK REQUIREMENTS:\n";
    echo "Rank {$nextRank} ({$requirement->rank_name}):\n";
    echo "- Personal Investment Required: $" . number_format($requirement->personal_investment, 2) . "\n";
    echo "- Team Business Volume Required: $" . number_format($requirement->team_business_volume, 2) . "\n";
    echo "- Referrals Required: {$requirement->count_level}\n";
    echo "- Reward Amount: $" . number_format($requirement->reward_amount, 2) . "\n\n";
    
    // Check each requirement
    echo "✅ REQUIREMENT CHECK:\n";
    
    $personalCheck = $personalInvestment >= $requirement->personal_investment;
    $teamCheck = $teamBusinessVolume >= $requirement->team_business_volume;
    $referralCheck = $referrals >= $requirement->count_level;
    
    // Personal Investment Check
    echo "1. Personal Investment: ";
    if ($personalCheck) {
        echo "✅ PASSED ($" . number_format($personalInvestment, 2) . " >= $" . number_format($requirement->personal_investment, 2) . ")\n";
    } else {
        $needed = $requirement->personal_investment - $personalInvestment;
        echo "❌ FAILED (Need $" . number_format($needed, 2) . " more)\n";
    }
    
    // Team Business Volume Check
    echo "2. Team Business Volume: ";
    if ($teamCheck) {
        echo "✅ PASSED ($" . number_format($teamBusinessVolume, 2) . " >= $" . number_format($requirement->team_business_volume, 2) . ")\n";
    } else {
        $needed = $requirement->team_business_volume - $teamBusinessVolume;
        echo "❌ FAILED (Need $" . number_format($needed, 2) . " more)\n";
    }
    
    // Referral Check
    echo "3. Referrals: ";
    if ($referralCheck) {
        echo "✅ PASSED ({$referrals} >= {$requirement->count_level})\n";
    } else {
        $needed = $requirement->count_level - $referrals;
        echo "❌ FAILED (Need {$needed} more referrals)\n";
    }
    
    echo "\n";
    
    // Final result
    $eligible = $personalCheck && $teamCheck && $referralCheck;
    
    echo "🏆 FINAL RESULT:\n";
    if ($eligible) {
        echo "✅ ELIGIBLE FOR RANK UPGRADE!\n";
        echo "🎉 User can upgrade to Rank {$nextRank} ({$requirement->rank_name})\n";
        echo "💰 Will receive reward: $" . number_format($requirement->reward_amount, 2) . "\n";
        echo "🚀 Ready to claim upgrade!\n";
    } else {
        echo "❌ NOT ELIGIBLE FOR RANK UPGRADE\n";
        echo "📋 Missing requirements listed above\n";
        echo "💡 User needs to fulfill all requirements before upgrading\n";
    }
    
    return $eligible;
}

// Test Case 1: Your example user (not eligible)
echo "TEST CASE 1: Current User Status (From Your Example)\n";
echo "====================================================\n";
checkRankUpgradeEligibility(1, 100, 1000, 6);

echo "\n" . str_repeat("=", 60) . "\n\n";

// Test Case 2: User after meeting requirements
echo "TEST CASE 2: Same User After Meeting All Requirements\n";
echo "===================================================\n";
checkRankUpgradeEligibility(1, 500, 2500, 9);

echo "\n" . str_repeat("=", 60) . "\n\n";

// Test Case 3: User with partial requirements met
echo "TEST CASE 3: User With Only Financial Requirements Met\n";
echo "====================================================\n";
checkRankUpgradeEligibility(1, 600, 3000, 5);

echo "\n=== RANK UPGRADE CHECK COMPLETED ===\n";
echo "This simulation shows exactly how the rank upgrade check works.\n";
echo "Users can see their progress on the /user/rank/requirements page.\n";