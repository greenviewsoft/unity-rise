<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Investment;
use App\Models\RankRequirement;
use App\Services\RankUpgradeService;
use Illuminate\Support\Facades\DB;

class InvestmentRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // প্রথমে rank requirements সিড করি
        $this->seedRankRequirements();
        
        // টেস্ট ইউজার তৈরি করি
        $this->seedTestUsers();
        
        // টেস্ট ইনভেস্টমেন্ট তৈরি করি
        $this->seedTestInvestments();
        
        // র‍্যাঙ্ক আপগ্রেড চেক করি
        $this->checkRankUpgrades();
    }
    
    /**
     * Rank requirements সিড করি
     */
    private function seedRankRequirements()
    {
        $this->command->info('Seeding rank requirements...');
        
        // যদি আগে থেকে requirements না থাকে তাহলে সিড করি
        if (RankRequirement::count() == 0) {
            RankRequirement::seedDefaultRequirements();
            $this->command->info('Rank requirements seeded successfully.');
        } else {
            $this->command->info('Rank requirements already exist.');
        }
    }
    
    /**
     * টেস্ট ইউজার তৈরি করি
     */
    private function seedTestUsers()
    {
        $this->command->info('Creating test users...');
        
        // Parent user তৈরি করি
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'Parent User',
                'username' => 'parent_user',
                'type' => 'user',
                'status' => 'active',
                'rank' => 0,
                'balance' => 10000.00,
                'password' => bcrypt('password'),
                'refer_id' => null
            ]
        );
        
        // Child users তৈরি করি যারা parent এর রেফারেল
        $childUsers = [];
        for ($i = 1; $i <= 5; $i++) {
            $childUsers[$i] = User::firstOrCreate(
                ['email' => "child{$i}@test.com"],
                [
                    'name' => "Child User {$i}",
                    'username' => "child_user_{$i}",
                    'type' => 'user',
                    'status' => 'active',
                    'rank' => 0,
                    'balance' => 5000.00,
                    'password' => bcrypt('password'),
                    'refer_id' => $parentUser->id
                ]
            );
        }
        
        // Create deeper levels to meet count level requirements
        // Level 3: Grandchildren
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 2; $j++) {
                User::firstOrCreate(
                    ['email' => "grandchild{$i}_{$j}@test.com"],
                    [
                        'name' => "Grandchild {$i}-{$j}",
                        'username' => "grandchild_{$i}_{$j}",
                        'type' => 'user',
                        'status' => 'active',
                        'rank' => 0,
                        'balance' => 2000.00,
                        'password' => bcrypt('password'),
                        'refer_id' => $childUsers[$i]->id
                    ]
                );
            }
        }
        
        // Level 4: Great-grandchildren
        $grandchild1 = User::where('email', 'grandchild1_1@test.com')->first();
        for ($i = 1; $i <= 2; $i++) {
            User::firstOrCreate(
                ['email' => "greatgrand{$i}@test.com"],
                [
                    'name' => "Great-grandchild {$i}",
                    'username' => "greatgrand_{$i}",
                    'type' => 'user',
                    'status' => 'active',
                    'rank' => 0,
                    'balance' => 1000.00,
                    'password' => bcrypt('password'),
                    'refer_id' => $grandchild1->id
                ]
            );
        }
        
        // Level 5: Great-great-grandchildren
        $greatGrand1 = User::where('email', 'greatgrand1@test.com')->first();
        User::firstOrCreate(
            ['email' => 'greatgreatgrand1@test.com'],
            [
                'name' => 'Great-great-grandchild 1',
                'username' => 'greatgreatgrand_1',
                'type' => 'user',
                'status' => 'active',
                'rank' => 0,
                'balance' => 500.00,
                'password' => bcrypt('password'),
                'refer_id' => $greatGrand1->id
            ]
        );
        
        // Level 6: Great-great-great-grandchild
        $greatGreatGrand1 = User::where('email', 'greatgreatgrand1@test.com')->first();
        User::firstOrCreate(
            ['email' => 'level6user@test.com'],
            [
                'name' => 'Level 6 User',
                'username' => 'level6_user',
                'type' => 'user',
                'status' => 'active',
                'rank' => 0,
                'balance' => 250.00,
                'password' => bcrypt('password'),
                'refer_id' => $greatGreatGrand1->id
            ]
        );
        
        $this->command->info('Test users created successfully.');
    }
    
    /**
     * টেস্ট ইনভেস্টমেন্ট তৈরি করি
     */
    private function seedTestInvestments()
    {
        $this->command->info('Creating test investments...');
        
        $parentUser = User::where('email', 'parent@test.com')->first();
        
        // Parent user এর জন্য investment (larger amount to trigger rank upgrade)
        Investment::firstOrCreate(
            [
                'user_id' => $parentUser->id,
                'amount' => 15000.00
            ],
            [
                'plan_type' => 'premium',
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'daily_profit' => 750.00,
                'total_profit' => 0.00,
                'status' => 'active',
                'profit_days_completed' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        // Child users এর জন্য investments (larger amounts to create significant team volume)
        $childUsers = User::where('refer_id', $parentUser->id)->get();
        foreach ($childUsers as $index => $child) {
            Investment::firstOrCreate(
                [
                    'user_id' => $child->id,
                    'amount' => 3000.00 + ($index * 500)
                ],
                [
                    'plan_type' => 'premium',
                    'start_date' => now(),
                    'end_date' => now()->addDays(30),
                    'daily_profit' => 150.00 + ($index * 25),
                    'total_profit' => 0.00,
                    'status' => 'active',
                    'profit_days_completed' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        // Create investments for grandchildren
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 2; $j++) {
                $grandchild = User::where('email', "grandchild{$i}_{$j}@test.com")->first();
                if ($grandchild) {
                    Investment::firstOrCreate(
                        [
                            'user_id' => $grandchild->id,
                            'amount' => 1000.00 + ($i * 100) + ($j * 50)
                        ],
                        [
                            'plan_type' => 'basic',
                            'start_date' => now(),
                            'end_date' => now()->addDays(30),
                            'daily_profit' => 50.00 + ($i * 5) + ($j * 2),
                            'total_profit' => 0.00,
                            'status' => 'active',
                            'profit_days_completed' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
            }
        }
        
        // Create investments for great-grandchildren
        for ($i = 1; $i <= 2; $i++) {
            $greatGrand = User::where('email', "greatgrand{$i}@test.com")->first();
            if ($greatGrand) {
                Investment::firstOrCreate(
                    [
                        'user_id' => $greatGrand->id,
                        'amount' => 500.00 + ($i * 100)
                    ],
                    [
                        'plan_type' => 'basic',
                        'start_date' => now(),
                        'end_date' => now()->addDays(30),
                        'daily_profit' => 25.00 + ($i * 5),
                        'total_profit' => 0.00,
                        'status' => 'active',
                        'profit_days_completed' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        // Create investments for deeper levels
        $deepUsers = ['greatgreatgrand1@test.com', 'level6user@test.com'];
        foreach ($deepUsers as $index => $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Investment::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'amount' => 300.00 + ($index * 50)
                    ],
                    [
                        'plan_type' => 'basic',
                        'start_date' => now(),
                        'end_date' => now()->addDays(30),
                        'daily_profit' => 15.00 + ($index * 2),
                        'total_profit' => 0.00,
                        'status' => 'active',
                        'profit_days_completed' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        $this->command->info('Test investments created successfully.');
    }
    
    /**
     * র‍্যাঙ্ক আপগ্রেড চেক করি
     */
    private function checkRankUpgrades()
    {
        $this->command->info('Checking rank upgrades...');
        
        $parentUser = User::where('email', 'parent@test.com')->first();
        $rankUpgradeService = new RankUpgradeService();
        
        $this->command->info("Parent user current rank: {$parentUser->rank}");
        
        // Team business volume calculate করি
        $teamVolume = $parentUser->getTeamBusinessVolume();
        $this->command->info("Team business volume: {$teamVolume}");
        
        // Count level calculate করি (team depth ব্যবহার করি)
        $countLevel = $parentUser->getTeamDepth();
        $this->command->info("Count level (team depth): {$countLevel}");
        
        // Personal investment calculate করি
        $personalInvestment = $parentUser->getPersonalInvestment();
        $this->command->info("Personal investment: {$personalInvestment}");
        
        // Current rank eligibility check করি
        $isEligible = $parentUser->checkRankUpgradeEligibility();
        $this->command->info("Eligible for next rank: " . ($isEligible ? 'Yes' : 'No'));
        
        // Highest eligible rank calculate করি
        $eligibleRank = $parentUser->calculateEligibleRank();
        $this->command->info("Highest eligible rank: {$eligibleRank}");
        
        // Rank upgrade attempt করি
        $oldRank = $parentUser->rank;
        
        // If user is eligible, process the rank upgrade
        if ($eligibleRank > $oldRank) {
            $this->command->info("Processing rank upgrade from {$oldRank} to {$eligibleRank}...");
            $newRank = $parentUser->processRankUpgrade();
            $this->command->info("✅ Rank upgraded successfully! Old rank: {$oldRank}, New rank: {$newRank}");
        } else {
            $this->command->info("❌ No rank upgrade available at this time.");
        }
        
        // সব rank requirements দেখাই
        $this->command->info('\nRank Requirements:');
        $requirements = RankRequirement::orderBy('rank')->get();
        foreach ($requirements as $req) {
            $eligible = $teamVolume >= $req->team_business_volume && 
                       $countLevel >= $req->count_level && 
                       $personalInvestment >= $req->personal_investment;
            
            $status = $eligible ? '✅' : '❌';
            $this->command->info(
                "{$status} Rank {$req->rank}: Team Volume: {$req->team_business_volume}, "
                . "Count Level: {$req->count_level}, Personal Investment: {$req->personal_investment}"
            );
        }
        
        // Child users এর rank check করি
        $this->command->info('\nChild Users Rank Check:');
        $childUsers = User::where('refer_id', $parentUser->id)->get();
        foreach ($childUsers as $child) {
            $childTeamVolume = $child->getTeamBusinessVolume();
            $childPersonalInvestment = $child->getPersonalInvestment();
            $childTeamDepth = $child->getTeamDepth();
            $childEligibleRank = $child->calculateEligibleRank();
            $this->command->info(
                "Child {$child->name} (ID: {$child->id}): Current Rank: {$child->rank}, "
                . "Eligible Rank: {$childEligibleRank}, Team Volume: {$childTeamVolume}, "
                . "Personal Investment: {$childPersonalInvestment}, Team Depth: {$childTeamDepth}"
            );
        }
    }
}
