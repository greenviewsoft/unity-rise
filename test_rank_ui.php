<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING RANK UI CALCULATIONS ===\n\n";

// Get the admin user
$user = \App\Models\User::where('email', 'admin@gmail.com')->first();

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User: {$user->name} ({$user->email})\n";
echo "Current Rank in Database: {$user->rank}\n\n";

// Test the calculations that the UI uses
$personalInvestment = $user->getPersonalInvestment();
$directReferrals = $user->directReferrals()->count();
$teamInvestment = $user->getTeamBusinessVolume();

echo "=== USER'S ACTUAL STATS (UI Calculations) ===\n";
echo "Personal Investment: $" . number_format($personalInvestment, 2) . "\n";
echo "Direct Referrals: {$directReferrals}\n";
echo "Team Investment: $" . number_format($teamInvestment, 2) . "\n\n";

// Get next rank requirements
$nextRank = \App\Models\RankRequirement::where('is_active', 1)
    ->where('rank', '>', $user->rank)
    ->orderBy('rank', 'asc')
    ->first();

if ($nextRank) {
    echo "=== NEXT RANK REQUIREMENTS (Bronze) ===\n";
    echo "Personal Investment Required: $" . number_format($nextRank->personal_investment, 2) . "\n";
    echo "Direct Referrals Required: {$nextRank->count_level}\n";
    echo "Team Investment Required: $" . number_format($nextRank->team_business_volume, 2) . "\n\n";
    
    // Calculate progress percentages (same as UI)
    $personalProgress = $nextRank->personal_investment > 0 ? min(100, ($personalInvestment / $nextRank->personal_investment) * 100) : 100;
    $directProgress = $nextRank->count_level > 0 ? min(100, ($directReferrals / $nextRank->count_level) * 100) : 100;
    $teamProgress = $nextRank->team_business_volume > 0 ? min(100, ($teamInvestment / $nextRank->team_business_volume) * 100) : 100;
    
    echo "=== PROGRESS PERCENTAGES (UI Display) ===\n";
    echo "Personal Investment Progress: " . number_format($personalProgress, 1) . "%\n";
    echo "Direct Referrals Progress: " . number_format($directProgress, 1) . "%\n";
    echo "Team Investment Progress: " . number_format($teamProgress, 1) . "%\n\n";
    
    // Calculate remaining requirements
    $personalRemaining = max(0, $nextRank->personal_investment - $personalInvestment);
    $directRemaining = max(0, $nextRank->count_level - $directReferrals);
    $teamRemaining = max(0, $nextRank->team_business_volume - $teamInvestment);
    
    echo "=== REMAINING REQUIREMENTS ===\n";
    echo "Personal Investment Remaining: $" . number_format($personalRemaining, 2) . "\n";
    echo "Direct Referrals Remaining: {$directRemaining}\n";
    echo "Team Investment Remaining: $" . number_format($teamRemaining, 2) . "\n\n";
    
    // Check if rank unlock is ready
    $rankUnlockReady = ($personalRemaining == 0 && $directRemaining == 0 && $teamRemaining == 0);
    echo "Rank Unlock Ready: " . ($rankUnlockReady ? "YES" : "NO") . "\n";
}

echo "\n=== TEST COMPLETE ===\n";