<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\RankUpgradeService;
use Illuminate\Support\Facades\Log;

class CheckRankUpgrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rank:check-upgrades {--user-id= : Check specific user ID} {--limit=100 : Limit number of users to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and process rank upgrades for users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting rank upgrade check...');
        
        $rankUpgradeService = new RankUpgradeService();
        $userId = $this->option('user-id');
        $limit = $this->option('limit');
        
        if ($userId) {
            // Check specific user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
            
            $this->info("Checking rank upgrade for user: {$user->username} (ID: {$user->id})");
            $this->info("Current rank: {$user->rank}");
            
            try {
                $newRank = $rankUpgradeService->checkUserRankUpgrade($user);
                $this->info("Rank after check: {$newRank}");
                
                if ($newRank > $user->rank) {
                    $this->info("✅ User upgraded from rank {$user->rank} to {$newRank}");
                } else {
                    $this->info("ℹ️ No rank upgrade needed");
                }
            } catch (\Exception $e) {
                $this->error("Error processing user {$user->id}: " . $e->getMessage());
                Log::error("Rank upgrade command error for user {$user->id}: " . $e->getMessage());
            }
        } else {
            // Check all active users
            $this->info("Checking rank upgrades for all active users (limit: {$limit})...");
            
            $users = User::where('status', 'active')
                        ->orderBy('updated_at', 'desc')
                        ->limit($limit)
                        ->get();
            
            $this->info("Found {$users->count()} users to check");
            
            $upgradedCount = 0;
            $errorCount = 0;
            
            foreach ($users as $user) {
                try {
                    $oldRank = $user->rank ?? 0;
                    $newRank = $rankUpgradeService->checkUserRankUpgrade($user);
                    
                    if ($newRank > $oldRank) {
                        $upgradedCount++;
                        $this->info("✅ {$user->username} upgraded from rank {$oldRank} to {$newRank}");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("❌ Error processing user {$user->username}: " . $e->getMessage());
                    Log::error("Rank upgrade command error for user {$user->id}: " . $e->getMessage());
                }
            }
            
            $this->info("\n📊 Summary:");
            $this->info("Users checked: {$users->count()}");
            $this->info("Users upgraded: {$upgradedCount}");
            $this->info("Errors: {$errorCount}");
        }
        
        $this->info('Rank upgrade check completed!');
        return 0;
    }
}