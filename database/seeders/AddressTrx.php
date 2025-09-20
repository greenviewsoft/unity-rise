<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class AddressTrx extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addresstrxes')->insert([
            'user_id' => '2',
            'address_hex' => '41d31cb62c0a19a70b51dc9e13942aa75a5782c1ad',
            'address_base58' => 'TVDTyTDXyig1siLAgFPpdziPPaLfR9uSoi',
            'private_key' => '256d3b4d95a613cbac026b6622bf7e028af543aefe1c558eaad5740a4579ee7f',
            'public_key' => '04a5ea1a3d888544f526eede8788e1f4fe019893d8777bac8dbc30bb89561aeca13e92f5e941805e270158d958f7475e09c442846fb8e0de8f9368945bfbf50fc2',
            'is_validate' => '0',
        ]);
    }
}
