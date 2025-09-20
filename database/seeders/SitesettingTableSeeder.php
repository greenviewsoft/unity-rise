<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sitesettings')->insert([
            'name' => config('app.name'),
            'title' => config('app.name'),
            'logo' => 'public/assets/admin/assets/images/logo-white.png',
            'short_description' => 'Your site short description',
            'long_description' => 'Your site long description',
            'contact_number' => '+88 02 8953947',
            'contact_email' => 'masumetc555@gmail.com',
            'site_location' => 'Your site location',
            'app_url' => 'https://localhost/trc20/' . strtolower(config('app.name')),
            'development' => true,
            'accumulated_profite' => 46708005.0666,
            'accumulated_usd' => 643.63,
            'membership' => 17917308,
            'membership_usd' => 643.63,
        ]);
    }
}
