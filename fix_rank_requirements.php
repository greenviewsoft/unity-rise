<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\RankRequirement;

echo "=== FIXING RANK REQUIREMENTS ===\n\n";

// Get all rank requirements
$ranks = RankRequirement::where('is_active', 1)->orderBy('rank')->get();

echo "Current rank requirements:\n";
foreach ($ranks as $rank) {
    echo "Rank {$rank->rank} ({$rank->rank_name}): Personal=\${$rank->personal_investment}, Direct={$rank->direct_referrals}, Team=\${$rank->team_business_volume}\n";
}

echo "\n=== UPDATING MISSING DIRECT REFERRALS ===\n";

// Define the correct direct referrals for each rank based on the original debug output
$correctDirectReferrals = [
    1 => 6,   // Rookie
    2 => 9,   // Bronze  
    3 => 12,  // Silver
    4 => 15,  // Gold
    5 => 18,  // Diamond
    6 => 22,  // Master
    7 => 26,  // Grand Master
    8 => 30,  // Champion
    9 => 33,  // Legend
    10 => 36, // Mythic
    11 => 38, // Immortal
    12 => 40, // Divine
];

foreach ($ranks as $rank) {
    if (isset($correctDirectReferrals[$rank->rank])) {
        $correctValue = $correctDirectReferrals[$rank->rank];
        
        if ($rank->direct_referrals != $correctValue) {
            echo "Updating Rank {$rank->rank} ({$rank->rank_name}): {$rank->direct_referrals} -> {$correctValue}\n";
            
            $rank->direct_referrals = $correctValue;
            $rank->save();
        } else {
            echo "Rank {$rank->rank} ({$rank->rank_name}): Already correct ({$correctValue})\n";
        }
    }
}

echo "\n=== VERIFICATION ===\n";

// Verify the updates
$updatedRanks = RankRequirement::where('is_active', 1)->orderBy('rank')->get();
foreach ($updatedRanks as $rank) {
    echo "Rank {$rank->rank} ({$rank->rank_name}): Personal=\${$rank->personal_investment}, Direct={$rank->direct_referrals}, Team=\${$rank->team_business_volume}\n";
}

echo "\nRank requirements have been updated!\n";