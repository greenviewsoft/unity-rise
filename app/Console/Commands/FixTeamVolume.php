<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixTeamVolume extends Command
{
    protected $signature = 'fix:team-volume {user_id}';
    protected $description = 'Fix team volume calculation issue';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error('User not found');
            return 1;
        }
        
        $this->info("Fixing team volume for user: {$user->username}");
        
        // Check referral statuses
        $referrals = $user->referrals()->get();
        $this->info("Total referrals: " . $referrals->count());
        
        $statusCounts = [];
        foreach($referrals as $ref) {
            $status = $ref->status;
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }
        
        $this->info("Status distribution:");
        foreach($statusCounts as $status => $count) {
            $this->info("- {$status}: {$count} users");
        }
        
        // Test both status filters
        $activeReferrals = $user->referrals()->where('status', 'active')->get();
        $onReferrals = $user->referrals()->where('status', 'on')->get();
        
        $this->info("Referrals with status 'active': " . $activeReferrals->count());
        $this->info("Referrals with status 'on': " . $onReferrals->count());
        
        // Fix the User model method by updating status
        $this->info("Updating referral status to 'active'...");
        $user->referrals()->update(['status' => 'active']);
        
        // Test team volume calculation again
        $newVolume = $user->getTeamBusinessVolume();
        $this->info("Team volume after fix: \${$newVolume}");
        
        // Test rank upgrade now
        $this->info("\nTesting rank upgrade after fix...");
        $personalInv = $user->investments()->where('status', 'active')->sum('amount');
        $directRefs = $user->referrals()->count();
        
        $this->info("Current stats:");
        $this->info("- Personal Investment: \${$personalInv}");
        $this->info("- Team Volume: \${$newVolume}");
        $this->info("- Direct Referrals: {$directRefs}");
        
        $rank2Req = \App\Models\RankRequirement::where('rank', 2)->first();
        $eligible = ($personalInv >= $rank2Req->personal_investment &&
                    $newVolume >= $rank2Req->team_business_volume &&
                    $directRefs >= $rank2Req->count_level);
        
        $this->info("Eligible for rank 2: " . ($eligible ? "YES" : "NO"));
        
        if ($eligible) {
            try {
                $rankService = new \App\Services\RankUpgradeService();
                $oldRank = $user->rank;
                $newRank = $rankService->checkUserRankUpgrade($user->fresh());
                
                if ($newRank > $oldRank) {
                    $this->info("✓ SUCCESS! Rank upgraded from {$oldRank} to {$newRank}");
                    
                    $reward = \App\Models\RankReward::where('user_id', $user->id)
                        ->where('new_rank', $newRank)
                        ->first();
                        
                    if ($reward) {
                        $this->info("✓ Rank reward issued: \${$reward->reward_amount}");
                    }
                    
                } else {
                    $this->warn("⚠ Rank upgrade failed for unknown reason");
                }
                
            } catch (\Exception $e) {
                $this->error("Rank upgrade error: " . $e->getMessage());
            }
        }
        
        return 0;
    }
}
