<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposite extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_number',
        'currency',
        'user_id',
        'amount',
        'txid',
        'status',
        'deposit_type',
        'screenshot',
        'transaction_hash',
        'user_notes',
        'admin_notes',
        'approved_at',
        'approved_by'
    ];
    
    protected $casts = [
        'approved_at' => 'datetime',
        'amount' => 'decimal:2'
    ];
    
    /**
     * Get the user that owns the deposit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the admin who approved this deposit
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the order associated with this deposit
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
    
    /**
     * Check if this is a manual deposit
     */
    public function isManual()
    {
        return $this->deposit_type === 'manual';
    }
    
    /**
     * Check if deposit is pending approval
     */
    public function isPending()
    {
        return $this->status == 0;
    }
    
    /**
     * Check if deposit is approved
     */
    public function isApproved()
    {
        return $this->status == 1;
    }
}
