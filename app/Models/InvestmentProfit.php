<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentProfit extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'user_id',
        'amount',
        'profit_date',
        'day_number'
    ];

    protected $casts = [
        'profit_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the investment that owns the profit
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * Get the user that owns the profit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for today's profits
     */
    public function scopeToday($query)
    {
        return $query->whereDate('profit_date', today());
    }

    /**
     * Scope for profits by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('profit_date', [$startDate, $endDate]);
    }
}