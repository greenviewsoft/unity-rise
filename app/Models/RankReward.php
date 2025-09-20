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
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->status) {
                $model->status = 'pending';
            }
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
     * Get rank reward amounts
     */
    public static function getRankRewards()
    {
        return [
            2 => 100,   // Rank 2 reward
            3 => 250,   // Rank 3 reward
            4 => 500,   // Rank 4 reward
            5 => 1000,  // Rank 5 reward
            6 => 2000,  // Rank 6 reward
            7 => 3500,  // Rank 7 reward
            8 => 5000,  // Rank 8 reward
            9 => 7500,  // Rank 9 reward
            10 => 10000, // Rank 10 reward
            11 => 15000, // Rank 11 reward
            12 => 25000  // Rank 12 reward
        ];
    }

    /**
     * Get reward amount for specific rank
     */
    public static function getRewardForRank($rank)
    {
        $rewards = self::getRankRewards();
        return $rewards[$rank] ?? 0;
    }
}