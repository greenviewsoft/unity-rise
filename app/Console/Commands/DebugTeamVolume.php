<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DebugTeamVolume extends Command
{
    protected $signature = 'debug:team-volume {user_id}';
    protected $description = 'Debug team volume calculation';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error('User not found');
            return 1;
        }
        
        $this->info("Debugging team volume for user: {$user->username} (ID: {$user->id})");
        
        // Check direct referrals
        $directReferrals = $user->referrals()->get();
        $this->info("Direct referrals count: " . $directReferrals->count());
        
        $totalVolume = 0;
        foreach ($directReferrals as $index => $referral) {
            $referralInvestment = $referral->investments()
                ->where('status', 'active')
                ->sum('amount');
            
            $this->info("Referral " . ($index + 1) . ": {$referral->username} - Investment: \${$referralInvestment}");
            $totalVolume += $referralInvestment;
        }
        
        $this->info("Manual calculation total: \${$totalVolume}");
        
        // Test the model method
        $modelVolume = $user->getTeamBusinessVolume();
        $this->info("Model method result: \${$modelVolume}");
        
        // Check if there's a caching issue
        $this->info("\nChecking cache...");
        $cacheKey = 'team_volume_' . $user->id;
        if (\Cache::has($cacheKey)) {
            $cachedValue = \Cache::get($cacheKey);
            $this->info("Cached value: \${$cachedValue}");
            
            // Clear cache and try again
            \Cache::forget($cacheKey);
            $this->info("Cache cleared, recalculating...");
            
            $newVolume = $user->getTeamBusinessVolume();
            $this->info("After cache clear: \${$newVolume}");
        } else {
            $this->info("No cached value found");
        }
        
        return 0;
    }
}
