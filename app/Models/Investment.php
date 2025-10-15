<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Events\InvestmentCreated;

class Investment extends Model
{
    use HasFactory;

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($investment) {
            event(new InvestmentCreated($investment));
        });
        
        static::updated(function ($investment) {
            // Clear cache when investment status or amount changes
            if ($investment->isDirty(['status', 'amount'])) {
                $user = $investment->user;
                if ($user) {
                    $user->clearTeamBusinessVolumeCache();
                }
            }
        });
        
        static::deleted(function ($investment) {
            // Clear cache when investment is deleted
            $user = $investment->user;
            if ($user) {
                $user->clearTeamBusinessVolumeCache();
            }
        });
    }

    protected $fillable = [
        'user_id',
        'amount',
        'plan_type',
        'plan_id',
        'start_date',
        'end_date',
        'daily_profit',
        'total_profit',
        'status',
        'profit_days_completed',
        'last_profit_date'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_profit_date' => 'datetime',
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_profit' => 'decimal:2'
    ];

    // Investment statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Plan types
    const PLAN_STARTER = 'starter';

    /**
     * Get the user that owns the investment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get investment profits
     */
    public function profits()
    {
        return $this->hasMany(InvestmentProfit::class);
    }

    /**
     * Get the investment plan
     */
    public function plan()
    {
        return $this->belongsTo(InvestmentPlan::class, 'plan_id');
    }

    /**
     * Calculate daily profit based on plan
     */
    public function calculateDailyProfit()
    {
        // If plan_id exists, use plan's daily profit percentage
        if ($this->plan_id && $this->plan) {
            return ($this->amount * $this->plan->daily_profit_percentage) / 100;
        }
        
        // Fallback to hardcoded values for backward compatibility
        switch ($this->plan_type) {
            case self::PLAN_STARTER:
                return $this->amount * 0.008; // 0.8%
            default:
                return 0;
        }
    }

    /**
     * ✅ Check if today is a profit day (Mon-Fri only, no weekends)
     */
    public function isProfitDay($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        // ✅ Check if it's Monday to Friday (weekdays only)
        return $date->isWeekday();
    }

    /**
     * Get remaining days for investment
     */
    public function getRemainingDaysAttribute()
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return 0;
        }

        // If plan_id exists, use plan's duration
        if ($this->plan_id && $this->plan) {
            $totalDays = $this->plan->duration_days;
        } else {
            // Fallback to hardcoded values for backward compatibility
            $totalDays = $this->plan_type === self::PLAN_STARTER ? 50 : 0;
        }
        
        return max(0, $totalDays - $this->profit_days_completed);
    }

    /**
     * Get total profit days for investment (for backward compatibility)
     */
    public function getTotalProfitDaysAttribute()
    {
        // If plan_id exists, use plan's duration
        if ($this->plan_id && $this->plan) {
            return $this->plan->duration_days;
        }
        
        // Fallback to hardcoded values for backward compatibility
        return $this->plan_type === self::PLAN_STARTER ? 50 : 0;
    }

    /**
     * ✅ Check if investment is eligible for profit today (Mon-Fri only)
     */
    public function canReceiveProfitToday()
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->remaining_days <= 0) {
            return false;
        }

        // ✅ Must be a weekday (Monday-Friday)
        if (!$this->isProfitDay()) {
            return false;
        }

        // Check if profit already received today
        $today = Carbon::now()->format('Y-m-d');
        $lastProfitDate = $this->last_profit_date ? $this->last_profit_date->format('Y-m-d') : null;
        
        return $lastProfitDate !== $today;
    }

    /**
     * Mark investment as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_date' => Carbon::now()
        ]);

        // Return capital to user
        $this->user->increment('balance', $this->amount);
    }

    /**
     * Scope for active investments
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * ✅ Scope for investments eligible for profit today (weekdays only)
     */
    public function scopeEligibleForProfitToday($query)
    {
        return $query->active()
            ->where('profit_days_completed', '<', function($subQuery) {
                $subQuery->selectRaw('CASE 
                    WHEN plan_id IS NOT NULL THEN (
                        SELECT duration_days FROM investment_plans WHERE id = investments.plan_id
                    )
                    WHEN plan_type = "starter" THEN 50 
                    ELSE 0 
                END');
            })
            ->where(function($q) {
                $q->whereNull('last_profit_date')
                  ->orWhere('last_profit_date', '<', Carbon::now()->format('Y-m-d'));
            });
    }
}