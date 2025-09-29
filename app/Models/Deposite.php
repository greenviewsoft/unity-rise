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
        'txid'
    ];
    
    /**
     * Get the user that owns the deposit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the order associated with this deposit
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}
