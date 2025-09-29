<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Deposite;
use App\Models\Addresstrx;
use App\Models\Settingtrx;
use App\Models\Apikey;
use Illuminate\Support\Facades\Log;

class CleanupTrxDeposits extends Command
{
    protected $signature = 'cleanup:trx-deposits {--dry-run} {--remove-orders} {--remove-models}';
    protected $description = 'Clean up old TRX/USDT-TRC20 deposit data and references';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $removeOrders = $this->option('remove-orders');
        $removeModels = $this->option('remove-models');
        
        $this->info('=== TRX DEPOSIT CLEANUP ===');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        
        // Count TRX/USDT-TRC20 related data
        $this->showCurrentData();
        
        if ($removeOrders) {
            $this->cleanupOrders($dryRun);
        }
        
        if ($removeModels) {
            $this->cleanupModels($dryRun);
        }
        
        $this->info("\n=== CLEANUP COMPLETE ===");
        
        if (!$removeOrders && !$removeModels) {
            $this->info("Use --remove-orders to clean up old TRX/USDT-TRC20 orders");
            $this->info("Use --remove-models to deprecate TRX-related models");
            $this->info("Use --dry-run to preview changes without making them");
        }
        
        return 0;
    }
    
    private function showCurrentData()
    {
        $this->info("\n1. CURRENT TRX/USDT-TRC20 DATA:");
        
        // Count TRX orders
        $trxOrders = Order::where('currency', 'TRX')->count();
        $this->info("TRX orders: {$trxOrders}");
        
        // Count USDT-TRC20 orders
        $usdtTrc20Orders = Order::where('currency', 'USDT-TRC20')->count();
        $this->info("USDT-TRC20 orders: {$usdtTrc20Orders}");
        
        // Count TRX deposits
        $trxDeposits = Deposite::where('currency', 'TRX')->count();
        $this->info("TRX deposits: {$trxDeposits}");
        
        // Count USDT-TRC20 deposits
        $usdtTrc20Deposits = Deposite::where('currency', 'USDT-TRC20')->count();
        $this->info("USDT-TRC20 deposits: {$usdtTrc20Deposits}");
        
        // Count Addresstrx records
        $addresstrxCount = Addresstrx::count();
        $this->info("TRX addresses: {$addresstrxCount}");
        
        // Count Settingtrx records
        $settingtrxCount = Settingtrx::count();
        $this->info("TRX settings: {$settingtrxCount}");
        
        // Count Apikey records
        $apikeyCount = Apikey::count();
        $this->info("API keys: {$apikeyCount}");
    }
    
    private function cleanupOrders($dryRun)
    {
        $this->info("\n2. CLEANING UP ORDERS:");
        
        // Find old TRX/USDT-TRC20 orders
        $oldOrders = Order::whereIn('currency', ['TRX', 'USDT-TRC20'])->get();
        
        if ($oldOrders->count() > 0) {
            $this->warn("Found {$oldOrders->count()} old TRX/USDT-TRC20 orders");
            
            foreach ($oldOrders as $order) {
                $this->info("Order {$order->order_number}: {$order->currency} - \${$order->amount} - {$order->status}");
                
                if (!$dryRun) {
                    // Update status to indicate deprecated
                    $order->status = 'deprecated';
                    $order->save();
                    
                    Log::info("Deprecated old order: {$order->order_number} ({$order->currency})");
                }
            }
            
            if (!$dryRun) {
                $this->info("✓ Updated {$oldOrders->count()} orders to 'deprecated' status");
            }
        } else {
            $this->info("✓ No old TRX/USDT-TRC20 orders found");
        }
        
        // Find related deposits
        $oldDeposits = Deposite::whereIn('currency', ['TRX', 'USDT-TRC20'])->get();
        
        if ($oldDeposits->count() > 0) {
            $this->warn("Found {$oldDeposits->count()} old TRX/USDT-TRC20 deposits");
            
            if (!$dryRun) {
                foreach ($oldDeposits as $deposit) {
                    // Keep deposit records for history but mark them
                    $deposit->txid = 'DEPRECATED-' . $deposit->txid;
                    $deposit->save();
                }
                
                $this->info("✓ Marked {$oldDeposits->count()} deposits as deprecated");
            }
        } else {
            $this->info("✓ No old TRX/USDT-TRC20 deposits found");
        }
    }
    
    private function cleanupModels($dryRun)
    {
        $this->info("\n3. CLEANING UP MODELS:");
        
        if (!$dryRun) {
            // Note: We don't actually delete model files as they may be referenced
            // Instead, we just document what should be removed manually
            $this->warn("The following files should be manually removed or deprecated:");
            $this->warn("- app/Models/Addresstrx.php");
            $this->warn("- app/Models/Settingtrx.php");
            $this->warn("- app/Models/Apikey.php");
            $this->warn("- app/Http/Controllers/admin/SettingtrxController.php");
            $this->warn("- app/Console/Commands/Autoreceive.php");
            
            $this->info("Database tables that can be dropped:");
            $this->info("- addresstrxes");
            $this->info("- settingtrxes");
            $this->info("- apikeys");
            
        } else {
            $this->info("Would mark TRX-related models as deprecated");
        }
    }
}
