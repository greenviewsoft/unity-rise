<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email', 
        'password',
        'rank',
        'refer_id',
        'refer_code',
        'parent_id',
        'username',
        'phone',
        'invitation_code',
        'balance',
        'refer_commission',
        'type',
        'status',
        'wallet_address',
        'wallet_private_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'wallet_private_key',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'balance' => 'decimal:2',
        'refer_commission' => 'decimal:2'
    ];

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Note: Automatic rank upgrades removed - users must manually claim via Rank Upgrade Center
    }


/**
 * Clear the team business volume cache for this user and optionally uplines
 */
public function clearTeamBusinessVolumeCache()
{
    // Clear this user's team volume cache
    \Cache::forget('team_volume_'.$this->id);

    // Optionally, clear cache for uplines if you have a parent_id or hierarchy
    if ($this->parent_id) {
        $parent = self::find($this->parent_id);
        if ($parent) {
            $parent->clearTeamBusinessVolumeCache();
        }
    }
}



    // Relationships
    public function deposites()
    {
        return $this->hasMany(Deposite::class, 'user_id');
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'user_id');
    }

    public function histories()
    {
        return $this->hasMany(History::class, 'user_id');
    }

    public function info()
    {
        return $this->hasOne(Info::class, 'user_id');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class, 'user_id');
    }

    // Referral relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'refer_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'refer_id');
    }

    public function directReferrals()
    {
        return $this->hasMany(User::class, 'refer_id');
    }

    public function referralCommissions()
    {
        return $this->hasMany(ReferralCommission::class, 'referrer_id');
    }

    public function rankRewards()
    {
        return $this->hasMany(RankReward::class, 'user_id');
    }

    // Note: processRankUpgrade method removed - rank upgrades are now manual only

    
    
    /**
 * Get all downline user IDs recursively
 */
public function getDownlineUserIds($depth = 0, $maxDepth = 40)
{
    if ($depth >= $maxDepth) {
        return [];
    }
    
    $downlineIds = [];
    
    // Get all direct referrals (both active and inactive)
    $directReferrals = $this->referrals()->get();
    
    foreach ($directReferrals as $referral) {
        $downlineIds[] = $referral->id;
        
        // Recursively get their downline
        $subDownlineIds = $referral->getDownlineUserIds($depth + 1, $maxDepth);
        $downlineIds = array_merge($downlineIds, $subDownlineIds);
    }
    
    return array_unique($downlineIds);
}






/**
 * Get team business volume
 */
public function getTeamBusinessVolume($depth = 0, $maxDepth = 40)
{
    if ($depth >= $maxDepth) {
        return 0;
    }
    
    $totalVolume = 0;
    
    // Get direct referrals (both active and inactive users can have investments)
    $directReferrals = $this->referrals()->get();
    
    foreach ($directReferrals as $referral) {
        // Add referral's investment (only active investments)
        $referralInvestment = $referral->investments()
                                      ->where('status', 'active')
                                      ->sum('amount');
        $totalVolume += $referralInvestment;
        
        // Add team volume from this referral's downline
        $totalVolume += $referral->getTeamBusinessVolume($depth + 1, $maxDepth);
    }
    
    return $totalVolume;
}

/**
 * Get personal investment
 */
public function getPersonalInvestment()
{
    return $this->investments()
                ->where('status', 'active')
                ->sum('amount');
}

/**
 * Create transaction log method for User model
 */
public function createTransactionLog($data)
{
    return \App\Models\Log::createTransactionLog(
        $this->id,
        $data['type'],
        $data['amount'],
        $data['previous_balance'],
        $data['new_balance'],
        isset($data['model']) ? $data['model'] : 'App\\Models\\User',
        isset($data['reference_id']) ? $data['reference_id'] : null,
        isset($data['description']) ? $data['description'] : $data['type'],
        isset($data['metadata']) ? $data['metadata'] : []
    );
}

    // Note: updateRank method removed - rank upgrades are now manual only

    /**
     * Get the rank name for the user's current rank
     * 
     * @return string Rank name
     */
    public function getRankName()
    {
        $currentRank = $this->rank ?? 0;
        
        if ($currentRank <= 0) {
            return 'No Rank';
        }

        try {
            // Try to get rank name from database first
            $rankRequirement = \App\Models\RankRequirement::where('rank', $currentRank)
                ->where('is_active', true)
                ->first();
            
            if ($rankRequirement && $rankRequirement->rank_name) {
                return $rankRequirement->rank_name;
            }

            // Fallback to hardcoded rank names
            $rankNames = [
                1 => 'Rookie',
                2 => 'Bronze',
                3 => 'Bronze+',
                4 => 'Silver',
                5 => 'Silver+',
                6 => 'Gold',
                7 => 'Gold+',
                8 => 'Platinum',
                9 => 'Platinum+',
                10 => 'Diamond',
                11 => 'Diamond+',
                12 => 'Master',
                13 => 'Grand Master',
                14 => 'Champion',
                15 => 'Legend',
                16 => 'Mythic',
                17 => 'Immortal',
                18 => 'Divine'
            ];

            return $rankNames[$currentRank] ?? "Rank {$currentRank}";
            
        } catch (\Exception $e) {
            \Log::error("Failed to get rank name for user {$this->id}: " . $e->getMessage());
            return "Rank {$currentRank}";
        }
    }
    
}