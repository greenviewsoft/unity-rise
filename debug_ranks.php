<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RankRequirement;
use App\Models\User;

echo "=== RANK REQUIREMENTS DEBUG ===\n\n";

// Get all rank requirements
$ranks = RankRequirement::where('is_active', 1)->orderBy('rank')->get();

echo "All Active Rank Requirements:\n";
foreach ($ranks as $rank) {
    echo "Rank {$rank->rank}: {$rank->rank_name}\n";
    echo "  - Personal Investment: $" . number_format($rank->personal_investment, 2) . "\n";
    echo "  - Direct Referrals: {$rank->count_level}\n";
    echo "  - Team Investment: $" . number_format($rank->team_business_volume, 2) . "\n";
    echo "  - Reward: $" . number_format($rank->reward_amount, 2) . "\n";
    echo "\n";
}

// Get current user (assuming user ID 1 for testing)
$user = User::find(1);
if ($user) {
    echo "Current User Stats:\n";
    echo "  - Current Rank: {$user->rank}\n";
    echo "  - Personal Investment: $" . number_format($user->getPersonalInvestment(), 2) . "\n";
    echo "  - Direct Referrals: " . $user->referrals()->count() . "\n";
    echo "  - Team Investment: $" . number_format($user->getTeamBusinessVolume(), 2) . "\n";
    echo "\n";
    
    // Check next rank
    $nextRank = RankRequirement::where('is_active', 1)
        ->where('rank', '>', $user->rank)
        ->orderBy('rank', 'asc')
        ->first();
        
    if ($nextRank) {
        echo "Next Rank: {$nextRank->rank_name} (Rank {$nextRank->rank})\n";
        echo "Requirements:\n";
        echo "  - Personal Investment: $" . number_format($nextRank->personal_investment, 2) . "\n";
        echo "  - Direct Referrals: {$nextRank->count_level}\n";
        echo "  - Team Investment: $" . number_format($nextRank->team_business_volume, 2) . "\n";
        
        $personalInvestment = $user->getPersonalInvestment();
        $directReferrals = $user->referrals()->count();
        $teamInvestment = $user->getTeamBusinessVolume();
        
        $meetsPersonal = $personalInvestment >= $nextRank->personal_investment;
        $meetsDirect = $directReferrals >= $nextRank->count_level;
        $meetsTeam = $teamInvestment >= $nextRank->team_business_volume;
        
        echo "\nEligibility Check:\n";
        echo "  - Personal Investment: " . ($meetsPersonal ? "✓" : "✗") . " ($personalInvestment >= {$nextRank->personal_investment})\n";
        echo "  - Direct Referrals: " . ($meetsDirect ? "✓" : "✗") . " ($directReferrals >= {$nextRank->count_level})\n";
        echo "  - Team Investment: " . ($meetsTeam ? "✓" : "✗") . " ($teamInvestment >= {$nextRank->team_business_volume})\n";
        echo "  - Overall Eligible: " . ($meetsPersonal && $meetsDirect && $meetsTeam ? "YES" : "NO") . "\n";
    }
}