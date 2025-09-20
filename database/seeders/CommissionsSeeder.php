<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ReferralCommissionLevel;

class CommissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all commission rates from ReferralCommissionLevel
        $commissionLevels = ReferralCommissionLevel::where('is_active', true)
            ->orderBy('rank_id')
            ->orderBy('level')
            ->get();

        // Group by rank_id
        $rankCommissions = $commissionLevels->groupBy('rank_id');

        $data = [];

        // Create commission records for each rank
        foreach ($rankCommissions as $rankId => $levels) {
            $commissionData = [
                'daily_com' => 0.00,
                'bonus' => 0.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Set commission rates for each level (refer_com1 to refer_com40)
            foreach ($levels as $level) {
                $fieldName = 'refer_com' . $level->level;
                $commissionData[$fieldName] = $level->commission_rate;
            }

            // Fill remaining levels with 0 if not defined
            for ($i = 1; $i <= 40; $i++) {
                $fieldName = 'refer_com' . $i;
                if (!isset($commissionData[$fieldName])) {
                    $commissionData[$fieldName] = 0.00;
                }
            }

            $data[] = $commissionData;
        }

        // Clear existing data and insert new
        DB::table('commissions')->truncate();
        DB::table('commissions')->insert($data);
        
        $this->command->info('Commissions table seeded successfully with ' . count($data) . ' records!');
    }
}
