<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SmtpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('smtpproviders')->insert([
            'smtp_name' => 'masumit',
            'hostname' => 'mail.masumit.com',
            'username' => 'noreply@masumit.com',
            'password' => '#g0!3irWe[Qm',
            'port' => '587',

            'connection' => 'tls',
            'reply_to' => 'masumetc555@gmail.com',
            'from_email' => 'admin@gmail.com',
        ]);
    }
}
