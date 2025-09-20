<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(SitesettingTableSeeder::class);
        $this->call(SmtpTableSeeder::class);
        $this->call(LangTableSeeder::class);
        $this->call(InfoTableSeeder::class);
        $this->call(AnnouncementTableSeeder::class);
       
        // \App\Models\User::factory(10)->create();
    }
}
