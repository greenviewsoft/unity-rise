<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'type' => 'admin',
            'username' => 'admin',
            'invitation_code' => '100000',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('1234'),
            'phone' => '01780481585',
            'pshow' => '1234',
        ]);


        DB::table('users')->insert([
            'type' => 'user',
            'username' => 'user',
            'invitation_code' => '100001',
            'email' => 'user@gmail.com',
            'password' => bcrypt('1234'),
            'phone' => '01780481584',
            'pshow' => '1234',
            'balance' => '0',

        ]);
    }
}
