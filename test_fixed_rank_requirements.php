<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\RankRequirement;

echo "=== Testing Fixed Rank Requirements Logic ===\n\n";

// Get the test user
$user = User::where('email', 'admin@gmail.com')->first();

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User: {$user->email}\n";
echo "Current Rank in DB: {$user->rank}\n\n";

// Get user stats
$personalInvestment = $user->getPersonalInvestment();
$directReferrals = $user->referrals()->count();
$teamInvestment = $user->getTeamBusinessVolume();

echo "=== User Current Stats ===\n";
echo "Personal Investment: $" . number_format($personalInvestment, 2) . "\n";
echo "Direct Referrals: {$directReferrals}\n";
echo "Team Investment: $" . number_format($teamInvestment, 2) . "\n\n";

// Get current rank requirements
$currentRank = RankRequirement::where('is_active', 1)
    ->where('rank', $user->rank)
    ->first();

echo "=== Current Rank Requirements (Rank {$user->rank}) ===\n";
if ($currentRank) {
    echo "Rank Name: {$currentRank->rank_name}\n";
    echo "Personal Investment Required: $" . number_format($currentRank->personal_investment, 2) . "\n";
    echo "Direct Referrals Required: {$currentRank->count_level}\n";
    echo "Team Investment Required: $" . number_format($currentRank->team_business_volume, 2) . "\n";
    echo "Reward Amount: $" . number_format($currentRank->reward_amount, 2) . "\n\n";
    
    // Calculate progress for current rank
    echo "=== Progress Towards Current Rank ===\n";
    
    $personalProgress = $currentRank->personal_investment > 0 ? 
        min(100, ($personalInvestment / $currentRank->personal_investment) * 100) : 100;
    $directProgress = $currentRank->count_level > 0 ? 
        min(100, ($directReferrals / $currentRank->count_level) * 100) : 100;
    $teamProgress = $currentRank->team_business_volume > 0 ? 
        min(100, ($teamInvestment / $currentRank->team_business_volume) * 100) : 100;
    
    echo "Personal Investment Progress: " . number_format($personalProgress, 1) . "%\n";
    echo "Direct Referrals Progress: " . number_format($directProgress, 1) . "%\n";
    echo "Team Investment Progress: " . number_format($teamProgress, 1) . "%\n\n";
    
    // Calculate remaining requirements
    $personalRemaining = max(0, $currentRank->personal_investment - $personalInvestment);
    $directRemaining = max(0, $currentRank->count_level - $directReferrals);
    $teamRemaining = max(0, $currentRank->team_business_volume - $teamInvestment);
    
    echo "=== Remaining Requirements ===\n";
    echo "Personal Investment Remaining: $" . number_format($personalRemaining, 2) . "\n";
    echo "Direct Referrals Remaining: {$directRemaining}\n";
    echo "Team Investment Remaining: $" . number_format($teamRemaining, 2) . "\n\n";
    
    // Check if current rank is completed
    $rankCompleted = ($personalRemaining == 0 && $directRemaining == 0 && $teamRemaining == 0);
    echo "Current Rank Completed: " . ($rankCompleted ? "YES" : "NO") . "\n\n";
    
} else {
    echo "No current rank found in database!\n\n";
}

// Get next rank
$nextRank = RankRequirement::where('is_active', 1)
    ->where('rank', '>', $user->rank)
    ->orderBy('rank', 'asc')
    ->first();

if ($nextRank) {
    echo "=== Next Rank Available ===\n";
    echo "Next Rank: {$nextRank->rank_name} (Rank {$nextRank->rank})\n";
    echo "Next Rank Reward: $" . number_format($nextRank->reward_amount, 2) . "\n";
} else {
    echo "=== Maximum Rank Achieved ===\n";
    echo "No higher rank available.\n";
}

echo "\n=== Test Complete ===\n";