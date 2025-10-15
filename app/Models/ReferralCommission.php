<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCommission extends Model
{
    use HasFactory;

 protected $fillable = [
    'referrer_id',
    'referred_id',
    'investment_id',        // ✅ Add
    'investment_amount',
    'commission_amount',
    'commission_rate',      // ✅ Add
    'level',
    'rank',
    'rank_id',              // ✅ Add
    'status',               // ✅ Add
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
            if (!$commission->commission_date) {
                $commission->commission_date = now();
            }
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
     * Get rank info from referral_commission_levels
     */
    public function rankInfo()
    {
        return $this->hasOne(ReferralCommissionLevel::class, 'rank_id', 'rank');
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
    public function scopeByRank($query, $rankId)
    {
        return $query->where('rank', $rankId);
    }
}