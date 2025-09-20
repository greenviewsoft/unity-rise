<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class EventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            'title' => 'Event title one',
            'image' => 'public/assets/user/images/pictures/7.jpg'
        ]);

        DB::table('events')->insert([
            'title' => 'Event title one',
            'image' => 'public/assets/user/images/20230823_1414001.mp4',
            'type' => 'video',
        ]);

        DB::table('events')->insert([
            'title' => 'Event title one',
            'image' => 'public/assets/user/images/20230823_1414001.mp4',
            'type' => 'video',
        ]);
    }
}
