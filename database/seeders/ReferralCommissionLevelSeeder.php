<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ReferralCommissionLevel;

class ReferralCommissionLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        DB::table('referral_commission_levels')->truncate();

        $commissionLevels = [
            // Rookie (Rank 1) - Max 6 levels
            ['rank_id' => 1, 'rank_name' => 'Rookie', 'level' => 1, 'commission_rate' => 7.00, 'rank_reward' => 50, 'max_levels' => 6],
            ['rank_id' => 1, 'rank_name' => 'Rookie', 'level' => 2, 'commission_rate' => 3.00, 'rank_reward' => 50, 'max_levels' => 6],
            ['rank_id' => 1, 'rank_name' => 'Rookie', 'level' => 3, 'commission_rate' => 2.00, 'rank_reward' => 50, 'max_levels' => 6],
            ['rank_id' => 1, 'rank_name' => 'Rookie', 'level' => 4, 'commission_rate' => 1.50, 'rank_reward' => 50, 'max_levels' => 6],
            ['rank_id' => 1, 'rank_name' => 'Rookie', 'level' => 5, 'commission_rate' => 0.50, 'rank_reward' => 50, 'max_levels' => 6],
            ['rank_id' => 1, 'rank_name' => 'Rookie', 'level' => 6, 'commission_rate' => 0.10, 'rank_reward' => 50, 'max_levels' => 6],

            // Bronze (Rank 2) - Max 9 levels
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 1, 'commission_rate' => 8.00, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 2, 'commission_rate' => 4.00, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 3, 'commission_rate' => 2.00, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 4, 'commission_rate' => 1.00, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 5, 'commission_rate' => 1.00, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 6, 'commission_rate' => 0.50, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 7, 'commission_rate' => 0.50, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 8, 'commission_rate' => 0.10, 'rank_reward' => 100, 'max_levels' => 9],
            ['rank_id' => 2, 'rank_name' => 'Bronze', 'level' => 9, 'commission_rate' => 0.10, 'rank_reward' => 100, 'max_levels' => 9],

            // Silver (Rank 3) - Max 12 levels
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 1, 'commission_rate' => 9.00, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 2, 'commission_rate' => 4.00, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 3, 'commission_rate' => 2.00, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 4, 'commission_rate' => 1.50, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 5, 'commission_rate' => 1.00, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 6, 'commission_rate' => 1.00, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 7, 'commission_rate' => 0.50, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 8, 'commission_rate' => 0.50, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 9, 'commission_rate' => 0.10, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 10, 'commission_rate' => 0.10, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 11, 'commission_rate' => 0.05, 'rank_reward' => 200, 'max_levels' => 12],
            ['rank_id' => 3, 'rank_name' => 'Silver', 'level' => 12, 'commission_rate' => 0.05, 'rank_reward' => 200, 'max_levels' => 12],

            // Gold (Rank 4) - Max 15 levels
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 1, 'commission_rate' => 10.00, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 2, 'commission_rate' => 5.00, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 3, 'commission_rate' => 3.00, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 4, 'commission_rate' => 2.50, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 5, 'commission_rate' => 2.00, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 6, 'commission_rate' => 1.00, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 7, 'commission_rate' => 0.80, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 8, 'commission_rate' => 0.60, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 9, 'commission_rate' => 0.50, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 10, 'commission_rate' => 0.40, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 11, 'commission_rate' => 0.30, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 12, 'commission_rate' => 0.20, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 13, 'commission_rate' => 0.15, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 14, 'commission_rate' => 0.10, 'rank_reward' => 350, 'max_levels' => 15],
            ['rank_id' => 4, 'rank_name' => 'Gold', 'level' => 15, 'commission_rate' => 0.05, 'rank_reward' => 350, 'max_levels' => 15],

            // Diamond (Rank 5) - Max 18 levels
            ['rank_id' => 5, 'rank_name' => 'Diamond', 'level' => 1, 'commission_rate' => 11.00, 'rank_reward' => 500, 'max_levels' => 18],
            ['rank_id' => 5, 'rank_name' => 'Diamond', 'level' => 2, 'commission_rate' => 6.00, 'rank_reward' => 500, 'max_levels' => 18],
            ['rank_id' => 5, 'rank_name' => 'Diamond', 'level' => 3, 'commission_rate' => 3.00, 'rank_reward' => 500, 'max_levels' => 18],
            ['rank_id' => 5, 'rank_name' => 'Diamond', 'level' => 4, 'commission_rate' => 2.50, 'rank_reward' => 500, 'max_levels' => 18],
            ['rank_id' => 5, 'rank_name' => 'Diamond', 'level' => 5, 'commission_rate' => 2.00, 'rank_reward' => 500, 'max_levels' => 18],
        ];

        // Continue with remaining levels for Diamond and other ranks
        for ($level = 6; $level <= 18; $level++) {
            $rate = max(0.05, 1.5 - ($level * 0.08)); // Decreasing rate
            $commissionLevels[] = ['rank_id' => 5, 'rank_name' => 'Diamond', 'level' => $level, 'commission_rate' => $rate, 'rank_reward' => 500, 'max_levels' => 18];
        }

        // Master (Rank 6) - Max 22 levels
        $commissionLevels = array_merge($commissionLevels, [
            ['rank_id' => 6, 'rank_name' => 'Master', 'level' => 1, 'commission_rate' => 12.00, 'rank_reward' => 750, 'max_levels' => 22],
            ['rank_id' => 6, 'rank_name' => 'Master', 'level' => 2, 'commission_rate' => 6.00, 'rank_reward' => 750, 'max_levels' => 22],
            ['rank_id' => 6, 'rank_name' => 'Master', 'level' => 3, 'commission_rate' => 4.00, 'rank_reward' => 750, 'max_levels' => 22],
            ['rank_id' => 6, 'rank_name' => 'Master', 'level' => 4, 'commission_rate' => 2.50, 'rank_reward' => 750, 'max_levels' => 22],
        ]);

        for ($level = 5; $level <= 22; $level++) {
            $rate = max(0.05, 2.0 - ($level * 0.08));
            $commissionLevels[] = ['rank_id' => 6, 'rank_name' => 'Master', 'level' => $level, 'commission_rate' => $rate, 'rank_reward' => 750, 'max_levels' => 22];
        }

        // Continue for all other ranks...
        $ranks = [
            7 => ['name' => 'Grand Master', 'reward' => 1000, 'max_levels' => 26, 'l1' => 13.00, 'l2' => 7.00, 'l3' => 3.00, 'l4' => 2.50, 'l5' => 2.00],
            8 => ['name' => 'Champion', 'reward' => 1500, 'max_levels' => 30, 'l1' => 13.00, 'l2' => 7.00, 'l3' => 3.00, 'l4' => 2.50, 'l5' => 2.00],
            9 => ['name' => 'Legend', 'reward' => 2000, 'max_levels' => 33, 'l1' => 14.00, 'l2' => 8.00, 'l3' => 5.00, 'l4' => 3.00, 'l5' => 2.00],
            10 => ['name' => 'Mythic', 'reward' => 3000, 'max_levels' => 36, 'l1' => 15.00, 'l2' => 8.00, 'l3' => 3.00, 'l4' => 3.00, 'l5' => 2.00],
            11 => ['name' => 'Immortal', 'reward' => 4000, 'max_levels' => 38, 'l1' => 16.00, 'l2' => 8.00, 'l3' => 5.00, 'l4' => 4.00, 'l5' => 2.50],
            12 => ['name' => 'Divine', 'reward' => 5000, 'max_levels' => 40, 'l1' => 17.00, 'l2' => 10.00, 'l3' => 7.00, 'l4' => 5.50, 'l5' => 4.00],
        ];

        foreach ($ranks as $rankId => $rankData) {
            // Add first 5 levels with specific rates
            $commissionLevels[] = ['rank_id' => $rankId, 'rank_name' => $rankData['name'], 'level' => 1, 'commission_rate' => $rankData['l1'], 'rank_reward' => $rankData['reward'], 'max_levels' => $rankData['max_levels']];
            $commissionLevels[] = ['rank_id' => $rankId, 'rank_name' => $rankData['name'], 'level' => 2, 'commission_rate' => $rankData['l2'], 'rank_reward' => $rankData['reward'], 'max_levels' => $rankData['max_levels']];
            $commissionLevels[] = ['rank_id' => $rankId, 'rank_name' => $rankData['name'], 'level' => 3, 'commission_rate' => $rankData['l3'], 'rank_reward' => $rankData['reward'], 'max_levels' => $rankData['max_levels']];
            $commissionLevels[] = ['rank_id' => $rankId, 'rank_name' => $rankData['name'], 'level' => 4, 'commission_rate' => $rankData['l4'], 'rank_reward' => $rankData['reward'], 'max_levels' => $rankData['max_levels']];
            $commissionLevels[] = ['rank_id' => $rankId, 'rank_name' => $rankData['name'], 'level' => 5, 'commission_rate' => $rankData['l5'], 'rank_reward' => $rankData['reward'], 'max_levels' => $rankData['max_levels']];

            // Add remaining levels with decreasing rates
            for ($level = 6; $level <= $rankData['max_levels']; $level++) {
                $rate = max(0.05, 2.5 - ($level * 0.06));
                $commissionLevels[] = ['rank_id' => $rankId, 'rank_name' => $rankData['name'], 'level' => $level, 'commission_rate' => $rate, 'rank_reward' => $rankData['reward'], 'max_levels' => $rankData['max_levels']];
            }
        }

        // Insert all commission levels
        foreach ($commissionLevels as $level) {
            $level['is_active'] = true;
            ReferralCommissionLevel::create($level);
        }

        echo "Referral commission levels seeded successfully!\n";
    }
}