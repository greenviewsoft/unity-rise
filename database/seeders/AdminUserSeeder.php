<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if admin user already exists
        $adminExists = User::where('type', 'admin')->first();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'type' => 'admin',
                'username' => 'admin',
                'phone' => '1234567890',
                'status' => 'active',
                'balance' => 0,
                'refer_commission' => 0,
            ]);
            
            echo "Admin user created successfully!\n";
        } else {
            echo "Admin user already exists!\n";
        }
    }
}