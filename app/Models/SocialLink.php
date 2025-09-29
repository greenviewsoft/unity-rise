<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'icon',
        'url',
        'color',
        'is_active',
        'sort_order'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];
    
    /**
     * Get active social links ordered by sort_order
     */
    public static function getActiveLinks()
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->get();
    }
}
