<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingBep20 extends Model
{
    use HasFactory;

    protected $table = 'settingbep20s';

    protected $fillable = [
        'receiver_status',
        'receiver_address',
        'receiver_private_key',
        'gas_limit',
        'sender_status',
        'sender_address',
        'sender_private_key',
        'usdt_to_usd_rate',
        'min_withdraw',
        'withdraw_fee',
        'network_name',
        'contract_address',
        'rpc_url'
    ];

    protected $casts = [
        'usdt_to_usd_rate' => 'decimal:5',
        'gas_limit' => 'integer',
        'min_withdraw' => 'integer',
        'withdraw_fee' => 'integer',
        'receiver_status' => 'boolean',
        'sender_status' => 'boolean'
    ];

    protected $attributes = [
        'receiver_status' => true,
        'sender_status' => true,
        'gas_limit' => 21000,
        'min_withdraw' => 10,
        'withdraw_fee' => 2,
        'usdt_to_usd_rate' => 1.0,
        'network_name' => 'BSC',
        'contract_address' => '0x55d398326f99059fF775485246999027B3197955', // USDT BEP20 contract
        'rpc_url' => 'https://bsc-dataseed1.binance.org/'
    ];
}