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
     * Seed default rank requirements - data now comes from database
     * This method is kept for reference but requirements are managed through admin panel
     */
    public static function seedDefaultRequirements()
    {
        // Requirements are now managed dynamically through the database
        // Use admin panel to manage rank requirements instead of hardcoded values
        return true;
    }
}