<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'daily_profit_percentage',
        'total_profit_percentage',
        'duration_days',
        'status',
        'description',
        'commission_levels',
        'rank_requirement',
        'created_by'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'daily_profit_percentage' => 'decimal:2',
        'total_profit_percentage' => 'decimal:2',
        'duration_days' => 'integer',
        'status' => 'boolean',
        'commission_levels' => 'integer',
        'rank_requirement' => 'integer'
    ];

    /**
     * Get the user who created this investment plan
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all investments for this plan
     */
    public function investments()
    {
        return $this->hasMany(Investment::class, 'plan_id');
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for inactive plans
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Get formatted min amount
     */
    public function getFormattedMinAmountAttribute()
    {
        return '$' . number_format($this->min_amount, 2);
    }

    /**
     * Get formatted max amount
     */
    public function getFormattedMaxAmountAttribute()
    {
        return '$' . number_format($this->max_amount, 2);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status ? 'badge-success' : 'badge-danger';
    }

    /**
     * Calculate daily profit for given amount
     */
    public function calculateDailyProfit($amount)
    {
        return ($amount * $this->daily_profit_percentage) / 100;
    }

    /**
     * Calculate total profit for given amount
     */
    public function calculateTotalProfit($amount)
    {
        return ($amount * $this->total_profit_percentage) / 100;
    }

    /**
     * Check if amount is within plan limits
     */
    public function isAmountValid($amount)
    {
        return $amount >= $this->min_amount && $amount <= $this->max_amount;
    }

    /**
     * Check if user meets rank requirement
     */
    public function userMeetsRankRequirement($user)
    {
        if (!$this->rank_requirement) {
            return true;
        }
        
        return $user->rank >= $this->rank_requirement;
    }
}