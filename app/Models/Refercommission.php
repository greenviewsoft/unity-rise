<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refercommission extends Model
{
    use HasFactory;

    protected $table = 'refercommissions';

    protected $fillable = [
        'user_id',
        'level',
        'deposite_amount',
        'commission',
        'refuser'
    ];

    protected $casts = [
        'deposite_amount' => 'decimal:2',
        'commission' => 'decimal:2'
    ];

    /**
     * Get the user who received the commission
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who referred (the one who caused the commission)
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'refuser');
    }
}