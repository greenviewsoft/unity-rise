<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Deposite;
use App\Models\User;
use App\Services\Bep20DepositService;
use App\Services\BscService;
use Illuminate\Support\Facades\Log;

class MonitorBep20Deposits extends Command
{
    protected $signature = 'monitor:bep20-deposits {--auto-process} {--notify-stuck}';
    protected $description = 'Monitor BEP20 USDT deposit system and optionally auto-process';

    public function handle()
    {
        $this->info('=== BEP20 DEPOSIT MONITORING ===');
        
        try {
            // System health check
            $this->checkSystemHealth();
            
            // Pending orders analysis
            $this->analyzePendingOrders();
            
            // Recent activity
            $this->showRecentActivity();
            
            // Auto-process if requested
            if ($this->option('auto-process')) {
                $this->autoProcessDeposits();
            }
            
            // Check for stuck deposits
            if ($this->option('notify-stuck')) {
                $this->checkStuckDeposits();
            }
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("BEP20 Monitoring failed: " . $e->getMessage());
            $this->error("Monitoring failed: " . $e->getMessage());
            return 1;
        }
    }
    
    private function checkSystemHealth()
    {
        $this->info("\n1. SYSTEM HEALTH CHECK:");
        
        try {
            $bscService = new BscService();
            $this->info("✓ BSC Service: Operational");
            
            $bep20Service = new Bep20DepositService();
            $this->info("✓ BEP20 Deposit Service: Operational");
            
            // Check database connectivity
            $orderCount = Order::count();
            $this->info("✓ Database: Connected ({$orderCount} total orders)");
            
        } catch (\Exception $e) {
            $this->error("✗ System Health Issue: " . $e->getMessage());
        }
    }
    
    private function analyzePendingOrders()
    {
        $this->info("\n2. PENDING ORDERS ANALYSIS:");
        
        $pendingOrders = Order::where('currency', 'USDT-BEP20')
            ->whereIn('status', ['0', 'pending'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $this->info("Total pending BEP20 orders: " . $pendingOrders->count());
        
        if ($pendingOrders->count() > 0) {
            $this->info("\nAge distribution:");
            
            $ageGroups = [
                '0-1h' => 0,
                '1-6h' => 0,
                '6-24h' => 0,
                '1-7d' => 0,
                '>7d' => 0
            ];
            
            foreach ($pendingOrders as $order) {
                $ageMinutes = now()->diffInMinutes($order->created_at);
                
                if ($ageMinutes <= 60) {
                    $ageGroups['0-1h']++;
                } elseif ($ageMinutes <= 360) {
                    $ageGroups['1-6h']++;
                } elseif ($ageMinutes <= 1440) {
                    $ageGroups['6-24h']++;
                } elseif ($ageMinutes <= 10080) {
                    $ageGroups['1-7d']++;
                } else {
                    $ageGroups['>7d']++;
                }
            }
            
            foreach ($ageGroups as $group => $count) {
                if ($count > 0) {
                    $this->info("  {$group}: {$count} orders");
                }
            }
            
            // Show newest orders
            $this->info("\nNewest orders:");
            foreach ($pendingOrders->take(3) as $order) {
                $age = $order->created_at->diffForHumans();
                $this->info("  {$order->order_number}: \${$order->amount} ({$age})");
            }
        }
    }
    
    private function showRecentActivity()
    {
        $this->info("\n3. RECENT ACTIVITY (24H):");
        
        $yesterday = now()->subDay();
        
        // Successful deposits
        $recentDeposits = Deposite::where('currency', 'USDT-BEP20')
            ->where('created_at', '>=', $yesterday)
            ->count();
            
        $recentAmount = Deposite::where('currency', 'USDT-BEP20')
            ->where('created_at', '>=', $yesterday)
            ->sum('amount');
            
        $this->info("Successful deposits: {$recentDeposits}");
        $this->info("Total amount: \${$recentAmount}");
        
        // New orders
        $newOrders = Order::where('currency', 'USDT-BEP20')
            ->where('created_at', '>=', $yesterday)
            ->count();
            
        $this->info("New orders: {$newOrders}");
        
        // Success rate
        if ($newOrders > 0) {
            $successRate = round(($recentDeposits / $newOrders) * 100, 1);
            $this->info("Success rate: {$successRate}%");
        }
    }
    
    private function autoProcessDeposits()
    {
        $this->info("\n4. AUTO-PROCESSING DEPOSITS:");
        
        $bep20Service = new Bep20DepositService();
        $results = $bep20Service->processPendingDeposits(50);
        
        $this->info("Processing results:");
        $this->info("- Processed: {$results['processed']}");
        $this->info("- Successful: {$results['successful']}");
        $this->info("- Failed: {$results['failed']}");
        
        if ($results['successful'] > 0) {
            $this->info("✓ {$results['successful']} deposits processed successfully");
            
            // Log successful processing
            Log::info("BEP20 Auto-processing successful", [
                'processed' => $results['processed'],
                'successful' => $results['successful'],
                'failed' => $results['failed']
            ]);
        }
        
        if ($results['failed'] > 0) {
            $this->warn("⚠ {$results['failed']} deposits failed to process");
        }
    }
    
    private function checkStuckDeposits()
    {
        $this->info("\n5. STUCK DEPOSIT CHECK:");
        
        // Orders older than 24 hours
        $stuckOrders = Order::where('currency', 'USDT-BEP20')
            ->whereIn('status', ['0', 'pending'])
            ->where('created_at', '<', now()->subDay())
            ->get();
            
        if ($stuckOrders->count() > 0) {
            $this->warn("Found {$stuckOrders->count()} potentially stuck deposits:");
            
            foreach ($stuckOrders as $order) {
                $age = $order->created_at->diffForHumans();
                $this->warn("  {$order->order_number}: \${$order->amount} ({$age})");
                
                // Check wallet balance for stuck order
                try {
                    $user = User::find($order->user_id);
                    if ($user && $user->wallet_address) {
                        $bscService = new BscService();
                        $balance = $bscService->getUsdtBalance($user->wallet_address);
                        
                        if ($balance >= $order->amount) {
                            $this->error("    ⚠ ALERT: Wallet has sufficient balance but order not processed!");
                            Log::warning("Stuck BEP20 deposit detected", [
                                'order_number' => $order->order_number,
                                'amount' => $order->amount,
                                'wallet_balance' => $balance,
                                'age_hours' => now()->diffInHours($order->created_at)
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore wallet check errors
                }
            }
        } else {
            $this->info("✓ No stuck deposits found");
        }
    }
}
