<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Langtran extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', // Add 'key' here
        'lang', // Add 'lang' here
    ];
}
