<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'previous_balance',
        'new_balance',
        'metadata',
        'reference_id',
        'reference_type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * Get the user that owns the transaction log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the polymorphic relation to the reference model
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get formatted previous balance with currency symbol
     */
    public function getFormattedPreviousBalanceAttribute()
    {
        return '$' . number_format($this->previous_balance, 2);
    }

    /**
     * Get formatted new balance with currency symbol
     */
    public function getFormattedNewBalanceAttribute()
    {
        return '$' . number_format($this->new_balance, 2);
    }
}
