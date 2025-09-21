<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank',
        'team_business_volume',
        'count_level',
        'personal_investment',
        'reward_amount',
        'is_active'
    ];

    protected $casts = [
        'team_business_volume' => 'decimal:2',
        'personal_investment' => 'decimal:2',
        'reward_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get rank requirements for a specific rank
     */
    public static function getRequirementsForRank($rank)
    {
        return self::where('rank', $rank)
                  ->where('is_active', true)
                  ->first();
    }

    /**
     * Get all active rank requirements
     */
    public static function getActiveRequirements()
    {
        return self::where('is_active', true)
                  ->orderBy('rank')
                  ->get();
    }

    /**
     * Check if user meets requirements for this rank
     */
    public function checkUserEligibility($user)
    {
        // Check team business volume
        $teamBV = $user->getTeamBusinessVolume();
        if ($teamBV < $this->team_business_volume) {
            return false;
        }

        // Check count level (team depth)
        $teamDepth = $user->getTeamDepth();
        if ($teamDepth < $this->count_level) {
            return false;
        }

        // Check personal investment
        $personalInvestment = $user->getPersonalInvestment();
        if ($personalInvestment < $this->personal_investment) {
            return false;
        }

        return true;
    }

    /**
     * Get rank name from database
     */
    public function getRankName()
    {
        return $this->rank_name ?? 'Unknown';
    }

    /**
     * Seed default rank requirements
     */
    public static function seedDefaultRequirements()
    {
        $requirements = [
            1 => ['bv' => 12000, 'level' => 6, 'investment' => 100, 'reward' => 500],
            2 => ['bv' => 25000, 'level' => 8, 'investment' => 250, 'reward' => 1000],
            3 => ['bv' => 50000, 'level' => 10, 'investment' => 500, 'reward' => 2000],
            4 => ['bv' => 100000, 'level' => 12, 'investment' => 1000, 'reward' => 3500],
            5 => ['bv' => 200000, 'level' => 14, 'investment' => 2000, 'reward' => 5000],
            6 => ['bv' => 350000, 'level' => 16, 'investment' => 3500, 'reward' => 7500],
            7 => ['bv' => 500000, 'level' => 18, 'investment' => 5000, 'reward' => 10000],
            8 => ['bv' => 750000, 'level' => 20, 'investment' => 7500, 'reward' => 15000],
            9 => ['bv' => 1000000, 'level' => 22, 'investment' => 10000, 'reward' => 20000],
            10 => ['bv' => 1500000, 'level' => 24, 'investment' => 15000, 'reward' => 30000],
            11 => ['bv' => 2000000, 'level' => 26, 'investment' => 20000, 'reward' => 40000],
            12 => ['bv' => 3000000, 'level' => 28, 'investment' => 30000, 'reward' => 50000]
        ];

        foreach ($requirements as $rank => $req) {
            self::updateOrCreate(
                ['rank' => $rank],
                [
                    'team_business_volume' => $req['bv'],
                    'count_level' => $req['level'],
                    'personal_investment' => $req['investment'],
                    'reward_amount' => $req['reward'],
                    'is_active' => true
                ]
            );
        }
    }
}