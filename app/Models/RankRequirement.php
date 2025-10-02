<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank',
        'rank_name',
        'team_business_volume',
        'count_level',
        'personal_investment',
        'direct_referrals',
        'reward_amount',
        'is_active'
    ];

    protected $casts = [
        'team_business_volume' => 'decimal:2',
        'personal_investment' => 'decimal:2',
        'reward_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /** Get current rank for user */
    public static function getCurrentRankForUser($user)
    {
        return self::where('is_active', true)
                   ->where('rank', $user->rank ?? 1)
                   ->first();
    }

    /** Get next rank for user */
    public static function getNextRankForUser($user)
    {
        return self::where('is_active', true)
                   ->where('rank', '>', $user->rank ?? 0)
                   ->orderBy('rank')
                   ->first();
    }

    /** Get requirements for specific rank */
    public static function getRequirementsForRank($rankNumber)
    {
        return self::where('is_active', true)
                   ->where('rank', $rankNumber)
                   ->first();
    }

    /** Get all active ranks */
    public static function getActiveRequirements()
    {
        return self::where('is_active', true)
                   ->orderBy('rank')
                   ->get();
    }

    /** Recursive team members IDs */
    public static function getTeamIds($userId)
    {
        $allTeamIds = collect([]);
        $queue = [$userId];

        while(count($queue) > 0) {
            $current = array_shift($queue);
            $children = \App\Models\User::where('refer_id', $current)->pluck('id')->toArray();
            $queue = array_merge($queue, $children);
            $allTeamIds = $allTeamIds->merge($children);
        }

        return $allTeamIds->unique();
    }

    /** Get user stats */
    public static function getUserStats($user)
    {
        // Personal Investment
        $personalInvestment = \App\Models\Investment::where('user_id', $user->id)
                                                    ->where('status', 'active')
                                                    ->sum('amount');

        // Direct referrals
        $directReferrals = \App\Models\User::where('refer_id', $user->id)->count();

        // Team Investment (recursive)
        $teamIds = self::getTeamIds($user->id);
        $teamInvestment = $teamIds->count() > 0
            ? \App\Models\Investment::whereIn('user_id', $teamIds)->where('status', 'active')->sum('amount')
            : 0;

        return [
            'personal_investment' => (float) $personalInvestment,
            'direct_referrals'    => (int) $directReferrals,
            'team_investment'     => (float) $teamInvestment,
        ];
    }

    /** Check eligibility for a specific rank */
    public static function checkUserEligibility($user, $rank)
    {
        $stats = self::getUserStats($user);

        $personalMet = $stats['personal_investment'] >= $rank->personal_investment;
        $referralsMet = $stats['direct_referrals'] >= $rank->direct_referrals;
        $teamMet = $stats['team_investment'] >= $rank->team_business_volume;

        return [
            'personal_investment' => $personalMet,
            'direct_referrals'    => $referralsMet,
            'team_investment'     => $teamMet,
            'eligible'            => $personalMet && $referralsMet && $teamMet
        ];
    }

    /** Can user upgrade? */
    public static function canUserUpgrade($user)
    {
        $nextRank = self::getNextRankForUser($user);
        if (!$nextRank) return false;

        $eligibility = self::checkUserEligibility($user, $nextRank);
        return $eligibility['eligible'];
    }

    /** Get rank name */
    public function getRankName()
    {
        return $this->rank_name ?? 'Rank ' . $this->rank;
    }
}