<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settingtrx extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_status',
        'receiver_address',
        'receiver_privatekey',
        'energy',
        'sender_status',
        'sender_address',
        'sender_privatekey',
        'conversion',
        'min_withdraw',
        'withdraw_vat'
    ];

    protected $casts = [
        'conversion' => 'decimal:5',
        'energy' => 'integer',
        'min_withdraw' => 'integer',
        'withdraw_vat' => 'integer'
    ];
}