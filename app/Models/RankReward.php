<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RankReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'old_rank',
        'new_rank',
        'reward_amount',
        'reward_type',
        'status',
        'processed_at'
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    /**
     * Boot method to set default values and auto-approve
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-process all rank rewards
            $model->status = 'processed';
            $model->processed_at = now();
        });


    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending rewards
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for processed rewards
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Scope for today's rewards
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope by reward type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('reward_type', $type);
    }

    /**
     * Mark reward as processed
     */
    public function markAsProcessed()
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now()
        ]);
    }

    /**
     * Get rank reward amounts from database
     */
    public static function getRankRewards()
    {
        $requirements = \App\Models\RankRequirement::where('is_active', true)
            ->orderBy('rank')
            ->get();

        $rewards = [];
        foreach ($requirements as $requirement) {
            $rewards[$requirement->rank] = $requirement->reward_amount;
        }

        return $rewards;
    }

    /**
     * Get reward amount for specific rank from database
     */
    public static function getRewardForRank($rank)
    {
        $rankRequirement = \App\Models\RankRequirement::where('rank', $rank)
            ->where('is_active', true)
            ->first();

        return $rankRequirement ? $rankRequirement->reward_amount : 0;
    }
}