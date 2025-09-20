<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferralCommissionLevel;

class ReferralCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'investment_amount',
        'commission_amount',
        'level',
        'rank',
        'commission_date'
    ];

    protected $casts = [
        'investment_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_date' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($commission) {
            $commission->commission_date = now();
        });
    }

    /**
     * Get the referrer user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the referred user
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Scope for today's commissions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('commission_date', today());
    }

    /**
     * Scope for commissions by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('commission_date', [$startDate, $endDate]);
    }

    /**
     * Scope for commissions by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for commissions by rank
     */
    public function scopeByRank($query, $rank)
    {
        return $query->where('rank', $rank);
    }

    /**
     * Get commission rate for specific rank and level
     * @param int $rank Rank ID (1-12)
     * @param int|null $level Level number (if null, returns all levels for the rank)
     * @return float|array Commission rate(s)
     */
    public static function getCommissionRate($rank, $level = null)
    {
        return ReferralCommissionLevel::getCommissionRatesForRank($rank, $level);
    }

    /**
     * Get rank rewards
     */
    public static function getRankRewards()
    {
        return ReferralCommissionLevel::getRankRewards();
    }
    
    /**
     * Get maximum commission rate for a rank
     */
    public static function getMaxCommissionRate($rank)
    {
        $rates = self::getCommissionRate($rank, null);
        return !empty($rates) ? max($rates) : 0;
    }
    
    /**
     * Get total levels count for a rank
     */
    public static function getTotalLevels($rank)
    {
        $rates = self::getCommissionRate($rank, null);
        return count($rates);
    }
    
    /**
     * Check if rank has higher benefits than another
     */
    public static function isHigherRank($rank1, $rank2)
    {
        return $rank1 > $rank2;
    }
    
    /**
     * Get rank progression data
     */
    public static function getRankProgression()
    {
        return ReferralCommissionLevel::getRankProgression();
    }
}