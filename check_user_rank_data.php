<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RankRequirement;

echo "=== USER RANK DATA VERIFICATION ===\n\n";

// Get the authenticated user (assuming user ID 1 for testing)
$user = User::find(1);

if (!$user) {
    echo "User not found. Please check user ID.\n";
    exit;
}

echo "User: {$user->name} ({$user->email})\n";
echo "Current Rank in Database: {$user->rank}\n\n";

// Get current rank details
$currentRank = RankRequirement::where('is_active', 1)
    ->where('rank', $user->rank)
    ->first();

if ($currentRank) {
    echo "=== CURRENT RANK DETAILS ===\n";
    echo "Rank: {$currentRank->rank} - {$currentRank->rank_name}\n";
    echo "Requirements:\n";
    echo "- Personal Investment: \${$currentRank->personal_investment}\n";
    echo "- Direct Referrals: {$currentRank->count_level}\n";
    echo "- Team Investment: \${$currentRank->team_business_volume}\n";
    echo "- Reward: \${$currentRank->reward_amount}\n\n";
}

// Get next rank details
$nextRank = RankRequirement::where('is_active', 1)
    ->where('rank', '>', $user->rank)
    ->orderBy('rank', 'asc')
    ->first();

if ($nextRank) {
    echo "=== NEXT RANK DETAILS ===\n";
    echo "Rank: {$nextRank->rank} - {$nextRank->rank_name}\n";
    echo "Requirements:\n";
    echo "- Personal Investment: \${$nextRank->personal_investment}\n";
    echo "- Direct Referrals: {$nextRank->count_level}\n";
    echo "- Team Investment: \${$nextRank->team_business_volume}\n";
    echo "- Reward: \${$nextRank->reward_amount}\n\n";
}

// Get user's actual stats
$personalInvestment = $user->getPersonalInvestment();
$directReferrals = $user->referrals()->count();
$teamInvestment = $user->getTeamBusinessVolume();

echo "=== USER'S ACTUAL STATS ===\n";
echo "Personal Investment: \${$personalInvestment}\n";
echo "Direct Referrals: {$directReferrals}\n";
echo "Team Investment: \${$teamInvestment}\n\n";

// Check if user qualifies for current rank
if ($currentRank) {
    echo "=== CURRENT RANK QUALIFICATION CHECK ===\n";
    $qualifiesPersonal = $personalInvestment >= $currentRank->personal_investment;
    $qualifiesReferrals = $directReferrals >= $currentRank->count_level;
    $qualifiesTeam = $teamInvestment >= $currentRank->team_business_volume;
    
    echo "Personal Investment: " . ($qualifiesPersonal ? "✓ PASS" : "✗ FAIL") . " ({$personalInvestment} >= {$currentRank->personal_investment})\n";
    echo "Direct Referrals: " . ($qualifiesReferrals ? "✓ PASS" : "✗ FAIL") . " ({$directReferrals} >= {$currentRank->count_level})\n";
    echo "Team Investment: " . ($qualifiesTeam ? "✓ PASS" : "✗ FAIL") . " ({$teamInvestment} >= {$currentRank->team_business_volume})\n";
    
    $qualifiesForCurrent = $qualifiesPersonal && $qualifiesReferrals && $qualifiesTeam;
    echo "Overall: " . ($qualifiesForCurrent ? "✓ QUALIFIES" : "✗ DOES NOT QUALIFY") . " for {$currentRank->rank_name}\n\n";
}

// Check if user qualifies for next rank
if ($nextRank) {
    echo "=== NEXT RANK QUALIFICATION CHECK ===\n";
    $qualifiesPersonal = $personalInvestment >= $nextRank->personal_investment;
    $qualifiesReferrals = $directReferrals >= $nextRank->count_level;
    $qualifiesTeam = $teamInvestment >= $nextRank->team_business_volume;
    
    echo "Personal Investment: " . ($qualifiesPersonal ? "✓ PASS" : "✗ FAIL") . " ({$personalInvestment} >= {$nextRank->personal_investment})\n";
    echo "Direct Referrals: " . ($qualifiesReferrals ? "✓ PASS" : "✗ FAIL") . " ({$directReferrals} >= {$nextRank->count_level})\n";
    echo "Team Investment: " . ($qualifiesTeam ? "✓ PASS" : "✗ FAIL") . " ({$teamInvestment} >= {$nextRank->team_business_volume})\n";
    
    $qualifiesForNext = $qualifiesPersonal && $qualifiesReferrals && $qualifiesTeam;
    echo "Overall: " . ($qualifiesForNext ? "✓ QUALIFIES" : "✗ DOES NOT QUALIFY") . " for {$nextRank->rank_name}\n\n";
}

// Check all users and their ranks
echo "=== ALL USERS RANK STATUS ===\n";
$users = User::all();
foreach ($users as $u) {
    $userRank = RankRequirement::where('rank', $u->rank)->first();
    $rankName = $userRank ? $userRank->rank_name : 'Unranked';
    echo "User: {$u->name} ({$u->email}) - Rank: {$u->rank} ({$rankName})\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";