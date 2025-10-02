<?php

namespace App\Models;

use Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'rank_upgraded_at',
        'photo',
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
   


/** Get user's rank name */
public function getRankName()
{
    $rankRequirement = \App\Models\RankRequirement::where('rank', $this->rank ?? 0)
                                                   ->where('is_active', true)
                                                   ->first();
    return $rankRequirement ? $rankRequirement->rank_name : 'Unranked';
}

/** Get personal investment */
public function getPersonalInvestment()
{
    return \App\Models\Investment::where('user_id', $this->id)
                                 ->where('status', 'active')
                                 ->sum('amount');
}

/** Get all downline user IDs (recursive) */
public function getDownlineUserIds()
{
    $allDownlineIds = collect([]);
    $queue = [$this->id];

    while(count($queue) > 0) {
        $current = array_shift($queue);
        $children = \App\Models\User::where('refer_id', $current)->pluck('id')->toArray();
        $queue = array_merge($queue, $children);
        $allDownlineIds = $allDownlineIds->merge($children);
    }

    return $allDownlineIds->unique()->toArray();
}

/** Get team business volume */
public function getTeamBusinessVolume()
{
    $teamIds = $this->getDownlineUserIds();
    
    if (empty($teamIds)) {
        return 0;
    }
    
    return \App\Models\Investment::whereIn('user_id', $teamIds)
                                 ->where('status', 'active')
                                 ->sum('amount');
}

    
}