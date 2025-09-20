<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SettingTrx extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settingtrxes')->insert([
            'receiver_address' => 'TWncFhgNc5vr5UdB7tjt473EBFq3UX6gtq',
            'receiver_privatekey' => 'b271364250cdd8ad533f2cc20f0a58711ce8b50bf7057fb24918c9fb6bf17137',
            'sender_address' => 'TWncFhgNc5vr5UdB7tjt473EBFq3UX6gtq',
            'sender_privatekey' => 'b271364250cdd8ad533f2cc20f0a58711ce8b50bf7057fb24918c9fb6bf17137',
        ]);
    }
}
