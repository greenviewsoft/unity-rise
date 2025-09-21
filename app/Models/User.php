<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Investment;
use App\Models\ReferralCommission;
use App\Models\RankReward;
use App\Models\RankRequirement;
use App\Models\History;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'wallet_private_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Update rank when balance is updated
        static::updated(function ($user) {
            if ($user->isDirty('balance')) {
                $user->updateRank();
            }
        });
    }

    public function trx()
    {
        return $this->hasOne(Addresstrx::class, 'user_id');
    }

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

    public function referralCommissions()
    {
        return $this->hasMany(ReferralCommission::class, 'referrer_id');
    }

    /**
     * Get all rank rewards earned by this user
     */
    public function rankRewards()
    {
        return $this->hasMany(RankReward::class);
    }

    /**
     * Get team business volume (total investment of all downline users)
     */
    public function getTeamBusinessVolume()
    {
        $totalBV = 0;
        
        // Get all downline users recursively
        $downlineUsers = $this->getAllDownlineUsers();
        
        foreach ($downlineUsers as $user) {
            $totalBV += $user->investments()->sum('amount');
        }
        
        return $totalBV;
    }

    /**
     * Get team depth (maximum level of downline)
     */
    public function getTeamDepth()
    {
        return $this->calculateMaxDepth(1);
    }

    /**
     * Calculate maximum depth recursively
     */
    private function calculateMaxDepth($currentDepth)
    {
        $directReferrals = $this->referrals;
        
        if ($directReferrals->isEmpty()) {
            return $currentDepth;
        }
        
        $maxDepth = $currentDepth;
        
        foreach ($directReferrals as $referral) {
            $depth = $referral->calculateMaxDepth($currentDepth + 1);
            $maxDepth = max($maxDepth, $depth);
        }
        
        return $maxDepth;
    }

    /**
     * Get personal investment amount
     */
    public function getPersonalInvestment()
    {
        return $this->investments()->sum('amount');
    }

    /**
     * Get all downline users recursively
     */
    public function getAllDownlineUsers()
    {
        $downlineUsers = collect();
        $directReferrals = $this->referrals;
        
        foreach ($directReferrals as $referral) {
            $downlineUsers->push($referral);
            $downlineUsers = $downlineUsers->merge($referral->getAllDownlineUsers());
        }
        
        return $downlineUsers;
    }

    /**
     * Check if user is eligible for rank upgrade
     */
    public function checkRankUpgradeEligibility()
    {
        $currentRank = $this->rank ?? 0;
        $nextRank = $currentRank + 1;
        
        $requirement = RankRequirement::getRequirementsForRank($nextRank);
        
        if (!$requirement) {
            return false; // No more ranks available
        }
        
        return $requirement->checkUserEligibility($this);
    }

    /**
     * Calculate highest eligible rank
     */
    public function calculateEligibleRank()
    {
        $currentRank = $this->rank ?? 0;
        $eligibleRank = $currentRank;
        
        // Check each rank from current+1 to maximum
        for ($rank = $currentRank + 1; $rank <= 12; $rank++) {
            $requirement = RankRequirement::getRequirementsForRank($rank);
            
            if ($requirement && $requirement->checkUserEligibility($this)) {
                $eligibleRank = $rank;
            } else {
                break; // Stop at first rank that doesn't meet requirements
            }
        }
        
        return $eligibleRank;
    }

    /**
     * Process automatic rank upgrade
     */
    public function processRankUpgrade()
    {
        $oldRank = $this->rank ?? 0;
        $eligibleRank = $this->calculateEligibleRank();
        
        if ($eligibleRank > $oldRank) {
            // Update user rank
            $this->update(['rank' => $eligibleRank]);
            
            // Process rank rewards for each rank upgrade
            for ($rank = $oldRank + 1; $rank <= $eligibleRank; $rank++) {
                $this->processRankUpgradeReward($rank);
                $this->processLeaderRewards($rank);
            }
            
            return $eligibleRank;
        }
        
        return $oldRank;
    }

    /**
     * Process rank upgrade reward for the user
     */
    private function processRankUpgradeReward($rank)
    {
        $requirement = RankRequirement::getRequirementsForRank($rank);
        
        if ($requirement && $requirement->reward_amount > 0) {
            $oldRank = $this->rank ?? 0;
            $previousBalance = $this->balance;
            
            // Create rank reward record
            $rankReward = RankReward::create([
                'user_id' => $this->id,
                'old_rank' => $oldRank,
                'new_rank' => $rank,
                'reward_amount' => $requirement->reward_amount,
                'reward_type' => 'rank_achievement',
                'status' => 'processed',
                'processed_at' => now()
            ]);
            
            // Update user balance
            $this->increment('balance', $requirement->reward_amount);
            $newBalance = $this->fresh()->balance;
            
            // Create history record
            History::create([
                'user_id' => $this->id,
                'amount' => $requirement->reward_amount,
                'type' => 'rank_upgrade_reward'
            ]);
            
            // Create transaction log
            \App\Models\Log::createTransactionLog(
                $this->id,
                'rank_reward',
                $requirement->reward_amount,
                $previousBalance,
                $newBalance,
                'App\\Models\\RankReward',
                $rankReward->id,
                "Rank {$rank} achievement reward",
                [
                    'old_rank' => $oldRank,
                    'new_rank' => $rank,
                    'reward_type' => 'rank_achievement'
                ]
            );
        }
    }

    /**
     * Process leader rewards for upline users
     */
    private function processLeaderRewards($rank)
    {
        $uplineUser = $this->referrer;
        $level = 1;
        
        while ($uplineUser && $level <= 10) { // Limit to 10 levels
            // Check if upline user is eligible for leader bonus
            if ($uplineUser->rank >= $rank) {
                $leaderBonus = $this->calculateLeaderBonus($rank, $level);
                
                if ($leaderBonus > 0) {
                    $previousBalance = $uplineUser->balance;
                    
                    // Create leader reward
                    $leaderReward = RankReward::create([
                        'user_id' => $uplineUser->id,
                        'old_rank' => $uplineUser->rank ?? 0,
                        'new_rank' => $uplineUser->rank ?? 0,
                        'reward_amount' => $leaderBonus,
                        'reward_type' => 'bonus',
                        'status' => 'processed',
                        'processed_at' => now()
                    ]);
                    
                    // Update upline user balance
                    $uplineUser->increment('balance', $leaderBonus);
                    $newBalance = $uplineUser->fresh()->balance;
                    
                    // Create history record
                    History::create([
                        'user_id' => $uplineUser->id,
                        'amount' => $leaderBonus,
                        'type' => 'leader_bonus',
                        'description' => "Leader bonus from {$this->username} rank {$rank} upgrade",
                        'status' => 'completed'
                    ]);
                    
                    // Create transaction log
                    \App\Models\Log::createTransactionLog(
                        $uplineUser->id,
                        'leader_bonus',
                        $leaderBonus,
                        $previousBalance,
                        $newBalance,
                        'App\\Models\\RankReward',
                        $leaderReward->id,
                        "Leader bonus from {$this->username} rank {$rank} upgrade",
                        [
                            'source_user_id' => $this->id,
                            'source_username' => $this->username,
                            'rank_achieved' => $rank,
                            'level' => $level,
                            'reward_type' => 'leader_bonus'
                        ]
                    );
                }
            }
            
            $uplineUser = $uplineUser->referrer;
            $level++;
        }
    }

    /**
     * Calculate leader bonus amount
     */
    private function calculateLeaderBonus($rank, $level)
    {
        // Leader bonus structure (percentage of rank reward)
        $bonusPercentages = [
            1 => 0.10, // 10% for level 1
            2 => 0.08, // 8% for level 2
            3 => 0.06, // 6% for level 3
            4 => 0.04, // 4% for level 4
            5 => 0.02, // 2% for level 5
        ];
        
        if (!isset($bonusPercentages[$level])) {
            return 0;
        }
        
        $requirement = RankRequirement::getRequirementsForRank($rank);
        
        if (!$requirement) {
            return 0;
        }
        
        return $requirement->reward_amount * $bonusPercentages[$level];
    }

    /**
     * Update user rank (legacy method for compatibility)
     */
    public function updateRank()
    {
        return $this->processRankUpgrade();
    }

    /**
     * Get rank name
     */
    public function getRankName()
    {
        $rankRequirement = \App\Models\RankRequirement::where('rank', $this->rank)->first();
        
        if ($rankRequirement) {
            return $rankRequirement->getRankName();
        }
        
        // Fallback for unknown ranks
        return 'Rookie';
    }

    /**
     * Get the transaction logs for the user
     */
    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class);
    }

    /**
     * Create a transaction log entry
     */
    public function createTransactionLog(array $data)
    {
        return $this->transactionLogs()->create([
            'type' => $data['type'],
            'amount' => $data['amount'],
            'previous_balance' => $data['previous_balance'],
            'new_balance' => $data['new_balance'],
            'metadata' => $data['metadata'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'reference_type' => $data['reference_type'] ?? null,
        ]);
    }
}
