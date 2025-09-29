<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'method',
        'order_number',
        'amount',
        'currency',
        'wallet_address',
        'status',
        'tx_hash'
    ];
    
    /**
     * Get the user that owns the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the deposit associated with this order
     */
    public function deposit()
    {
        return $this->hasOne(Deposite::class, 'order_number', 'order_number');
    }
    
    /**
     * Check if order is completed
     */
    public function isCompleted()
    {
        return $this->status == '1' || $this->status == 'completed';
    }
    
    /**
     * Check if order is for BEP20 USDT
     */
    public function isBep20Usdt()
    {
        return $this->currency == 'USDT-BEP20';
    }
}
