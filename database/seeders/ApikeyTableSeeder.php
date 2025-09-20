<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ApikeyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('apikeys')->insert([
            'base_url' => 'https://tron2024.pro/',
            'apikey' => 'mZllumMQgP8hijnd7WJvo9BmSJsRDRwcUtmKfde6',
        ]);
    }
}
