<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RankRequirement;
use Illuminate\Support\Facades\DB;

echo "=== SIMPLE RANK UPGRADE TEST ===\n\n";

// Test users from your database
$testUsers = [
    ['id' => 2, 'email' => 'user@gmail.com'],
    ['id' => 135, 'email' => 'adstrate@gmail.com'],
    ['id' => 136, 'email' => 'test1759144243@example.com'],
    ['id' => 139, 'email' => 'xdmedia@gmail.com'],
    ['id' => 140, 'email' => 'kabir99@gmail.com'],
    ['id' => 141, 'email' => 'level5@gmail.com'],
    ['id' => 143, 'email' => 'level7@gmail.com']
];

function calculateUserStats($userId) {
    // Personal investment from investments table
    $personalInvestment = DB::table('investments')
        ->where('user_id', $userId)
        ->where('status', 'active')
        ->sum('amount') ?? 0;

    // Team investment (from downline users)
    $downlineUsers = getDownlineUsers($userId);
    $teamInvestment = 0;
    if (!empty($downlineUsers)) {
        $teamInvestment = DB::table('investments')
            ->whereIn('user_id', $downlineUsers)
            ->where('status', 'active')
            ->sum('amount') ?? 0;
    }

    // Direct referrals count
    $directReferrals = User::where('refer_id', $userId)->count();

    return [
        'personal' => $personalInvestment,
        'team' => $teamInvestment,
        'referrals' => $directReferrals
    ];
}

function getDownlineUsers($userId, $maxDepth = 5) {
    $downline = [];
    $currentLevel = [$userId];
    
    for ($i = 0; $i < $maxDepth; $i++) {
        $nextLevel = User::whereIn('refer_id', $currentLevel)
            ->pluck('id')
            ->toArray();
            
        if (empty($nextLevel)) break;
        
        $downline = array_merge($downline, $nextLevel);
        $currentLevel = $nextLevel;
    }
    
    return $downline;
}

function checkRankEligibility($userStats, $requirement) {
    return $userStats['personal'] >= $requirement->personal_investment &&
           $userStats['team'] >= $requirement->team_business_volume &&
           $userStats['referrals'] >= $requirement->count_level;
}

function simulateRankUpgrade($userId, $addPersonal = 0, $addTeam = 0) {
    $user = User::find($userId);
    if (!$user) return false;

    $stats = calculateUserStats($userId);
    
    // Add simulated investments
    $newStats = [
        'personal' => $stats['personal'] + $addPersonal,
        'team' => $stats['team'] + $addTeam,
        'referrals' => $stats['referrals']
    ];

    $nextRank = $user->rank + 1;
    $requirement = RankRequirement::where('rank', $nextRank)
        ->where('is_active', 1)
        ->first();

    if (!$requirement) return false;

    $eligible = checkRankEligibility($newStats, $requirement);
    
    echo "User: {$user->email} (ID: {$userId})\n";
    echo "Current Rank: {$user->rank}\n";
    echo "Current Stats: Personal: $" . number_format($stats['personal'], 2) . 
         ", Team: $" . number_format($stats['team'], 2) . 
         ", Referrals: {$stats['referrals']}\n";
    
    if ($addPersonal > 0 || $addTeam > 0) {
        echo "After adding investments: Personal: $" . number_format($newStats['personal'], 2) . 
             ", Team: $" . number_format($newStats['team'], 2) . "\n";
    }
    
    echo "Next Rank ({$nextRank}) Requirements: Personal: $" . number_format($requirement->personal_investment, 2) . 
         ", Team: $" . number_format($requirement->team_business_volume, 2) . 
         ", Referrals: {$requirement->count_level}\n";
    
    echo "Eligible for upgrade: " . ($eligible ? "✓ YES" : "✗ NO") . "\n";
    
    if ($eligible) {
        echo "Would receive reward: $" . number_format($requirement->reward_amount, 2) . "\n";
        echo "New rank: {$requirement->rank_name}\n";
    } else {
        echo "Missing: ";
        $missing = [];
        if ($newStats['personal'] < $requirement->personal_investment) {
            $missing[] = "Personal: $" . number_format($requirement->personal_investment - $newStats['personal'], 2);
        }
        if ($newStats['team'] < $requirement->team_business_volume) {
            $missing[] = "Team: $" . number_format($requirement->team_business_volume - $newStats['team'], 2);
        }
        if ($newStats['referrals'] < $requirement->count_level) {
            $missing[] = "Referrals: " . ($requirement->count_level - $newStats['referrals']);
        }
        echo implode(", ", $missing) . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
    
    return $eligible;
}

// Test each user
echo "TESTING CURRENT USER STATUS:\n\n";
foreach ($testUsers as $testUser) {
    simulateRankUpgrade($testUser['id']);
}

echo "\nTESTING WITH SIMULATED INVESTMENTS:\n\n";
echo "Adding $1000 personal + $5000 team investment to each user:\n\n";
foreach ($testUsers as $testUser) {
    simulateRankUpgrade($testUser['id'], 1000, 5000);
}

echo "=== TEST COMPLETED ===\n";
echo "Summary: The script tested rank upgrade eligibility for all users.\n";
echo "To perform actual upgrades, use the web interface at /user/rank/requirements\n";