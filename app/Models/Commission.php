<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'refer_com1',
        'refer_com2',
        'refer_com3',
        'refer_com4',
        'daily_com',
        'bonus',
        'is_active'
    ];

    protected $casts = [
        'refer_com1' => 'decimal:2',
        'refer_com2' => 'decimal:2',
        'refer_com3' => 'decimal:2',
        'refer_com4' => 'decimal:2',
        'daily_com' => 'decimal:2',
        'bonus' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get commission rate for specific level
     * @param int $level Commission level (1-4)
     * @return float Commission rate
     */
    public function getCommissionRate($level)
    {
        $field = 'refer_com' . $level;
        return $this->$field ?? 0;
    }

    /**
     * Check if commission system is active
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }
}