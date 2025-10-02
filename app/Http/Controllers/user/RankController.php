<?php

namespace App\Http\Controllers\User;

use Log;
use App\Models\User;
use App\Models\History;
use App\Models\RankReward;
use Illuminate\Http\Request;
use App\Models\RankRequirement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RankController extends Controller
{
   public function requirements()
{
    $user = Auth::user();

    $currentRank = RankRequirement::getCurrentRankForUser($user);
    $nextRank    = RankRequirement::getNextRankForUser($user);
    $allRanks    = RankRequirement::getActiveRequirements();

    $stats = RankRequirement::getUserStats($user);

    // Current rank completion check
    $currentRankMet = $stats['personal_investment'] >= $currentRank->personal_investment
                   && $stats['direct_referrals'] >= $currentRank->direct_referrals
                   && $stats['team_investment'] >= $currentRank->team_business_volume;

    // Next rank eligibility
    $nextRankReady = $nextRank ? RankRequirement::canUserUpgrade($user) : false;

    // CURRENT RANK PROGRESS (for display)
    $current_personal_progress = $stats['personal_investment'];
    $required_personal_progress = $currentRank->personal_investment;
    $personal_investment_progress = $required_personal_progress > 0 
        ? min(100, ($current_personal_progress / $required_personal_progress) * 100) 
        : 100;
    $personal_investment_remaining = max(0, $required_personal_progress - $current_personal_progress);

    $current_direct_progress = $stats['direct_referrals'];
    $required_direct_progress = $currentRank->direct_referrals;
    $direct_referrals_progress = $required_direct_progress > 0 
        ? min(100, ($current_direct_progress / $required_direct_progress) * 100) 
        : 100;
    $direct_referrals_remaining = max(0, $required_direct_progress - $current_direct_progress);

    $current_team_progress = $stats['team_investment'];
    $required_team_progress = $currentRank->team_business_volume;
    $team_investment_progress = $required_team_progress > 0 
        ? min(100, ($current_team_progress / $required_team_progress) * 100) 
        : 100;
    $team_investment_remaining = max(0, $required_team_progress - $current_team_progress);

    return view('user.rank.requirements-simple', [
        'user'                         => $user,
        'currentRank'                  => $currentRank,
        'nextRank'                     => $nextRank,
        'allRanks'                     => $allRanks,
        'personal_investment'          => $stats['personal_investment'],
        'direct_referrals'             => $stats['direct_referrals'],
        'team_investment'              => $stats['team_investment'],
        'current_rank_name'            => $currentRank->rank_name ?? 'Unranked',
        'next_rank_name'               => $nextRank->rank_name ?? null,
        'current_rank_personal_req'    => $currentRank->personal_investment,
        'current_rank_direct_req'      => $currentRank->direct_referrals,
        'current_rank_team_req'        => $currentRank->team_business_volume,
        'current_rank_reward'          => $currentRank->reward_amount,
        'rank_unlock_ready'            => $currentRankMet,
        'next_rank_unlock_ready'       => $nextRankReady,
        
        // Progress data for current rank
        'current_personal_progress'    => $current_personal_progress,
        'required_personal_progress'   => $required_personal_progress,
        'personal_investment_progress' => $personal_investment_progress,
        'personal_investment_remaining'=> $personal_investment_remaining,
        
        'current_direct_progress'      => $current_direct_progress,
        'required_direct_progress'     => $required_direct_progress,
        'direct_referrals_progress'    => $direct_referrals_progress,
        'direct_referrals_remaining'   => $direct_referrals_remaining,
        
        'current_team_progress'        => $current_team_progress,
        'required_team_progress'       => $required_team_progress,
        'team_investment_progress'     => $team_investment_progress,
        'team_investment_remaining'    => $team_investment_remaining,
    ]);
}
  


  
// public function upgrade(Request $request)
// {
//     $user = Auth::user();
//     $currentRank = RankRequirement::getCurrentRankForUser($user);
//     $nextRank = RankRequirement::getNextRankForUser($user);

//     \Log::info('=== RANK UPGRADE ATTEMPT ===');
//     \Log::info('User ID: ' . $user->id . ' | Current Rank: ' . ($user->rank ?? 0));

//     // Check if reward already claimed
//     $alreadyClaimed = RankReward::where('user_id', $user->id)
//                                 ->where('new_rank', $user->rank)
//                                 ->where('status', 'processed')
//                                 ->exists();

//     if ($alreadyClaimed && !$nextRank) {
//         return response()->json(['success' => false, 'message' => 'Already at max rank']);
//     }

//     // Check current rank requirements
//     $stats = RankRequirement::getUserStats($user);
//     $currentRankMet = $stats['personal_investment'] >= $currentRank->personal_investment
//                    && $stats['direct_referrals'] >= $currentRank->direct_referrals
//                    && $stats['team_investment'] >= $currentRank->team_business_volume;

//     if (!$currentRankMet) {
//         return response()->json(['success' => false, 'message' => 'Complete current rank requirements first']);
//     }

//    DB::beginTransaction();
// try {
//     $previousBalance = $user->balance;
//     $oldRank = $user->rank;

//     // Give current rank reward if not claimed
//     if (!$alreadyClaimed) {
//         $rankReward = RankReward::create([
//             'user_id'       => $user->id,
//             'old_rank'      => $oldRank,
//             'new_rank'      => $oldRank, // Currently same, will update if upgrade happens
//             'reward_amount' => $currentRank->reward_amount,
//             'reward_type'   => 'rank_achievement',
//             'status'        => 'processed',
//             'processed_at'  => now()
//         ]);

//         if ($currentRank->reward_amount > 0) {
//             $user->increment('balance', $currentRank->reward_amount);
//             $newBalance = $user->fresh()->balance;
            
//             History::create([
//                 'user_id' => $user->id,
//                 'type'    => 'rank_achievement_bonus',
//                 'amount'  => $currentRank->reward_amount,
//             ]);
//         }
//     }

//     // AUTO UPGRADE to next rank if exists
//     $upgradedToRank = $oldRank;
//     if ($nextRank) {
//         $user->update([
//             'rank' => $nextRank->rank,
//             'rank_upgraded_at' => now()
//         ]);
//         $upgradedToRank = $nextRank->rank;
        
//         // Update rank_reward entry with new rank
//         if (isset($rankReward)) {
//             $rankReward->update(['new_rank' => $nextRank->rank]);
//         }
        
//         $message = "Claimed {$currentRank->rank_name} reward & upgraded to {$nextRank->rank_name}!";
//         $newRankName = $nextRank->rank_name;
//     } else {
//         $message = "Claimed {$currentRank->rank_name} reward!";
//         $newRankName = $currentRank->rank_name;
//     }

//     // Create log entry with correct data
//     \App\Models\Log::create([
//         'user_id'          => $user->id,
//         'transaction_type' => 'rank_reward_auto',
//         'amount'           => $currentRank->reward_amount,
//         'previous_balance' => $previousBalance,
//         'new_balance'      => $user->fresh()->balance,
//         'reference_type'   => 'App\Models\RankReward',
//         'reference_id'     => $rankReward->id ?? null,
//         'description'      => "Auto-approved rank reward - Rank {$oldRank} to {$upgradedToRank}",
//         'metadata'         => json_encode([
//             'old_rank' => $oldRank,
//             'new_rank' => $upgradedToRank,
//             'reward_type' => 'rank_achievement',
//             'reward_amount' => $currentRank->reward_amount
//         ]),
//         'status'           => 'completed',
//         'ip_address'       => request()->ip(),
//         'user_agent'       => request()->userAgent(),
//     ]);

//     DB::commit();
    
//     return response()->json([
//         'success' => true,
//         'message' => $message,
//         'data'    => [
//             'rank'      => $user->fresh()->rank,
//             'rank_name' => $newRankName,
//             'reward'    => $currentRank->reward_amount,
//             'balance'   => $user->fresh()->balance
//         ]
//     ]);
// } catch (\Exception $e) {
//     DB::rollBack();
//     \Log::error('FAILED: ' . $e->getMessage());
    
//     return response()->json([
//         'success' => false, 
//         'message' => 'Upgrade failed: ' . $e->getMessage()
//     ]);
// }

// }

public function upgrade(Request $request)
{
    $user = Auth::user();
    $currentRank = RankRequirement::getCurrentRankForUser($user);
    $nextRank = RankRequirement::getNextRankForUser($user);

    \Log::info('=== RANK UPGRADE ATTEMPT ===');
    \Log::info('User ID: ' . $user->id . ' | Current Rank: ' . ($user->rank ?? 0));

    // Check if reward already claimed for current rank
    $alreadyClaimed = RankReward::where('user_id', $user->id)
                                ->where('new_rank', $user->rank)
                                ->where('status', 'processed')
                                ->exists();

    if ($alreadyClaimed && !$nextRank) {
        return response()->json(['success' => false, 'message' => 'Already at max rank']);
    }

    // Check current rank requirements
    $stats = RankRequirement::getUserStats($user);
    $currentRankMet = $stats['personal_investment'] >= $currentRank->personal_investment
                   && $stats['direct_referrals'] >= $currentRank->direct_referrals
                   && $stats['team_investment'] >= $currentRank->team_business_volume;

    if (!$currentRankMet) {
        return response()->json(['success' => false, 'message' => 'Complete current rank requirements first']);
    }
DB::beginTransaction();
try {
    $previousBalance = $user->balance;
    $oldRank = $user->rank;
    $upgradedToRank = $oldRank;
    $rankReward = null;

    // Create rank reward if not claimed
    if (!$alreadyClaimed) {
        // Add reward to balance first
        if ($currentRank->reward_amount > 0) {
            $user->increment('balance', $currentRank->reward_amount);
        }
    }

    // Upgrade to next rank if exists
    if ($nextRank) {
        $user->update([
            'rank' => $nextRank->rank,
            'rank_upgraded_at' => now()
        ]);
        $upgradedToRank = $nextRank->rank;
        $message = "Claimed {$currentRank->rank_name} reward & upgraded to {$nextRank->rank_name}!";
        $newRankName = $nextRank->rank_name;
    } else {
        $message = "Claimed {$currentRank->rank_name} reward!";
        $newRankName = $currentRank->rank_name;
    }

    // Create rank reward entry AFTER upgrade (with correct new_rank)
    if (!$alreadyClaimed) {
        $rankReward = RankReward::create([
            'user_id'       => $user->id,
            'old_rank'      => $oldRank,
            'new_rank'      => $upgradedToRank, // Already has correct value
            'reward_amount' => $currentRank->reward_amount,
            'reward_type'   => 'rank_achievement',
            'status'        => 'processed',
            'processed_at'  => now()
        ]);

        // Create log ONCE
        \App\Models\Log::create([
            'user_id'          => $user->id,
            'transaction_type' => 'rank_reward_auto',
            'amount'           => $currentRank->reward_amount,
            'previous_balance' => $previousBalance,
            'new_balance'      => $user->fresh()->balance,
            'reference_type'   => 'App\Models\RankReward',
            'reference_id'     => $rankReward->id,
            'description'      => "Auto-approved rank reward - Rank {$oldRank} to {$upgradedToRank}",
            'metadata'         => json_encode([
                'old_rank' => $oldRank,
                'new_rank' => $upgradedToRank,
                'reward_type' => 'rank_achievement',
                'reward_amount' => $currentRank->reward_amount
            ]),
            'status'           => 'completed',
            'ip_address'       => request()->ip(),
            'user_agent'       => request()->userAgent(),
        ]);
    }

    DB::commit();

    return response()->json([
        'success' => true,
        'message' => $message,
        'data'    => [
            'rank'      => $user->fresh()->rank,
            'rank_name' => $newRankName,
            'reward'    => $currentRank->reward_amount,
            'balance'   => $user->fresh()->balance
        ]
    ]);

} catch (\Exception $e) {
    DB::rollBack();
    \Log::error('FAILED: ' . $e->getMessage());
    return response()->json(['success' => false, 'message' => 'Upgrade failed: ' . $e->getMessage()]);
}

}

}