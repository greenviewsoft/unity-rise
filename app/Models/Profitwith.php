<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profitwith extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'username',
        'amount'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2'
    ];
}
