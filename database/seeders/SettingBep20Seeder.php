<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SettingBep20;

class SettingBep20Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingBep20::create([
            'receiver_status' => true,
            'receiver_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6', // Example BEP20 address
            'receiver_private_key' => null, // Should be set securely in production
            'gas_limit' => 21000,
            'sender_status' => true,
            'sender_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6', // Example BEP20 address
            'sender_private_key' => null, // Should be set securely in production
            'usdt_to_usd_rate' => 1.0,
            'min_withdraw' => 10,
            'withdraw_fee' => 2,
            'network_name' => 'BSC',
            'contract_address' => '0x55d398326f99059fF775485246999027B3197955', // USDT BEP20 contract
            'rpc_url' => 'https://bsc-dataseed1.binance.org/'
        ]);
    }
}