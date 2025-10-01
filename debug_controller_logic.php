<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RankRequirement;

echo "=== CONTROLLER LOGIC DEBUG ===\n\n";

// Get user (assuming user ID 1)
$user = User::find(1);
if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User ID: {$user->id}\n";
echo "User Rank: {$user->rank}\n\n";

// Get current rank requirements
$currentRank = RankRequirement::where('is_active', 1)
    ->where('rank', $user->rank ?? 1)
    ->first();

if ($currentRank) {
    echo "Current Rank Requirements (Rank {$currentRank->rank} - {$currentRank->rank_name}):\n";
    echo "  - Personal Investment: \${$currentRank->personal_investment}\n";
    echo "  - Direct Referrals: {$currentRank->direct_referrals}\n";
    echo "  - Team Investment: \${$currentRank->team_business_volume}\n\n";
}

// Get user stats
$personalInvestment = $user->getPersonalInvestment();
$directReferrals = $user->referrals()->count();
$teamInvestment = $user->getTeamBusinessVolume();

echo "User Current Stats:\n";
echo "  - Personal Investment: \${$personalInvestment}\n";
echo "  - Direct Referrals: {$directReferrals}\n";
echo "  - Team Investment: \${$teamInvestment}\n\n";

// Check current rank completion
if ($currentRank) {
    echo "Current Rank Completion Check:\n";
    echo "  - Personal: \${$personalInvestment} >= \${$currentRank->personal_investment} = " . 
         ($personalInvestment >= $currentRank->personal_investment ? "TRUE" : "FALSE") . "\n";
    echo "  - Direct: {$directReferrals} >= {$currentRank->direct_referrals} = " . 
         ($directReferrals >= $currentRank->direct_referrals ? "TRUE" : "FALSE") . "\n";
    echo "  - Team: \${$teamInvestment} >= \${$currentRank->team_business_volume} = " . 
         ($teamInvestment >= $currentRank->team_business_volume ? "TRUE" : "FALSE") . "\n\n";
}

// Get next rank requirements
$nextRank = RankRequirement::where('is_active', 1)
    ->where('rank', '>', ($user->rank ?? 0))
    ->orderBy('rank', 'asc')
    ->first();

if ($nextRank) {
    echo "Next Rank Requirements (Rank {$nextRank->rank} - {$nextRank->rank_name}):\n";
    echo "  - Personal Investment: \${$nextRank->personal_investment}\n";
    echo "  - Direct Referrals: {$nextRank->direct_referrals}\n";
    echo "  - Team Investment: \${$nextRank->team_business_volume}\n\n";
    
    echo "Next Rank Completion Check:\n";
    echo "  - Personal: \${$personalInvestment} >= \${$nextRank->personal_investment} = " . 
         ($personalInvestment >= $nextRank->personal_investment ? "TRUE" : "FALSE") . "\n";
    echo "  - Direct: {$directReferrals} >= {$nextRank->direct_referrals} = " . 
         ($directReferrals >= $nextRank->direct_referrals ? "TRUE" : "FALSE") . "\n";
    echo "  - Team: \${$teamInvestment} >= \${$nextRank->team_business_volume} = " . 
         ($teamInvestment >= $nextRank->team_business_volume ? "TRUE" : "FALSE") . "\n\n";
}