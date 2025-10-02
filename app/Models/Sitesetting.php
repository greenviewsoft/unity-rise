<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitesetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'title', 'logo', 'short_description', 'long_description', 
        'contact_number', 'contact_email', 'site_location', 'support_url',
        'accumulated_profite', 'accumulated_usd', 'membership', 'membership_usd',
        'development', 'app_url', 'rule_content', 'about_content', 'promotion_content', 'promotion_image',
        'about_image', 'rule_image'
    ];
}
