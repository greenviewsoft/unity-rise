<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RankRequirement;

class RankRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        DB::table('rank_requirements')->truncate();

        $rankRequirements = [
            [
                'rank' => 1,
                'rank_name' => 'Rookie',
                'team_business_volume' => 1000,
                'personal_investment' => 100,
                'reward_amount' => 50,
                'count_level' => 6,
                'is_active' => true
            ],
            [
                'rank' => 2,
                'rank_name' => 'Bronze',
                'team_business_volume' => 2500,
                'personal_investment' => 500,
                'reward_amount' => 100,
                'count_level' => 9,
                'is_active' => true
            ],
            [
                'rank' => 3,
                'rank_name' => 'Silver',
                'team_business_volume' => 5000,
                'personal_investment' => 1000,
                'reward_amount' => 200,
                'count_level' => 12,
                'is_active' => true
            ],
            [
                'rank' => 4,
                'rank_name' => 'Gold',
                'team_business_volume' => 10000,
                'personal_investment' => 2000,
                'reward_amount' => 350,
                'count_level' => 15,
                'is_active' => true
            ],
            [
                'rank' => 5,
                'rank_name' => 'Diamond',
                'team_business_volume' => 20000,
                'personal_investment' => 3000,
                'reward_amount' => 500,
                'count_level' => 18,
                'is_active' => true
            ],
            [
                'rank' => 6,
                'rank_name' => 'Master',
                'team_business_volume' => 35000,
                'personal_investment' => 5000,
                'reward_amount' => 750,
                'count_level' => 22,
                'is_active' => true
            ],
            [
                'rank' => 7,
                'rank_name' => 'Grand Master',
                'team_business_volume' => 50000,
                'personal_investment' => 7500,
                'reward_amount' => 1000,
                'count_level' => 26,
                'is_active' => true
            ],
            [
                'rank' => 8,
                'rank_name' => 'Champion',
                'team_business_volume' => 75000,
                'personal_investment' => 10000,
                'reward_amount' => 1500,
                'count_level' => 30,
                'is_active' => true
            ],
            [
                'rank' => 9,
                'rank_name' => 'Legend',
                'team_business_volume' => 100000,
                'personal_investment' => 15000,
                'reward_amount' => 2000,
                'count_level' => 33,
                'is_active' => true
            ],
            [
                'rank' => 10,
                'rank_name' => 'Mythic',
                'team_business_volume' => 150000,
                'personal_investment' => 20000,
                'reward_amount' => 3000,
                'count_level' => 36,
                'is_active' => true
            ],
            [
                'rank' => 11,
                'rank_name' => 'Immortal',
                'team_business_volume' => 200000,
                'personal_investment' => 30000,
                'reward_amount' => 4000,
                'count_level' => 38,
                'is_active' => true
            ],
            [
                'rank' => 12,
                'rank_name' => 'Divine',
                'team_business_volume' => 300000,
                'personal_investment' => 50000,
                'reward_amount' => 5000,
                'count_level' => 40,
                'is_active' => true
            ]
        ];

        foreach ($rankRequirements as $requirement) {
            RankRequirement::create($requirement);
        }

        echo "Rank requirements seeded successfully!\n";
    }
}
