<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCommissionLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank_id',
        'rank_name',
        'level',
        'commission_rate',
        'rank_reward',
        'max_levels',
        'is_active'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:4',
        'rank_reward' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get commission rates for a specific rank
     */
    public static function getCommissionRatesForRank($rankId, $level = null)
    {
        $query = self::where('rank_id', $rankId)->where('is_active', true);
        
        if ($level !== null) {
            $query->where('level', $level);
            return $query->first()?->commission_rate ?? 0;
        }
        
        return $query->pluck('commission_rate', 'level')->toArray();
    }

    /**
     * Get all rank names
     */
    public static function getRankNames()
    {
        return self::select('rank_id', 'rank_name')
            ->distinct()
            ->orderBy('rank_id')
            ->pluck('rank_name', 'rank_id')
            ->toArray();
    }

    /**
     * Get max levels per rank
     */
    public static function getMaxLevelsPerRank()
    {
        return self::select('rank_id', 'max_levels')
            ->distinct()
            ->orderBy('rank_id')
            ->pluck('max_levels', 'rank_id')
            ->toArray();
    }

    /**
     * Get rank rewards
     */
    public static function getRankRewards()
    {
        return self::select('rank_id', 'rank_reward')
            ->distinct()
            ->orderBy('rank_id')
            ->pluck('rank_reward', 'rank_id')
            ->toArray();
    }

    /**
     * Get rank progression data
     */
    public static function getRankProgression()
    {
        $ranks = self::select('rank_id', 'rank_name', 'max_levels')
            ->distinct()
            ->orderBy('rank_id')
            ->get();

        $progression = [];
        foreach ($ranks as $rank) {
            $progression[$rank->rank_id] = [
                'name' => $rank->rank_name,
                'max_levels' => $rank->max_levels,
                'next_rank' => $rank->rank_id < 12 ? $rank->rank_id + 1 : null
            ];
        }

        return $progression;
    }
}
