<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\Deposite;
use App\Services\Bep20DepositService;
use App\Services\BscService;

class TestBep20Deposits extends Command
{
    protected $signature = 'test:bep20-deposits {--process-pending} {--check-order=} {--user-id=}';
    protected $description = 'Test and monitor BEP20 USDT deposit system';

    public function handle()
    {
        $this->info('=== BEP20 USDT DEPOSIT SYSTEM TEST ===');
        
        if ($this->option('check-order')) {
            return $this->checkSpecificOrder($this->option('check-order'));
        }
        
        if ($this->option('user-id')) {
            return $this->checkUserWallet($this->option('user-id'));
        }
        
        if ($this->option('process-pending')) {
            return $this->processPendingDeposits();
        }
        
        // Default: Show system status
        $this->showSystemStatus();
        
        return 0;
    }
    
    private function showSystemStatus()
    {
        $this->info("\n1. SYSTEM OVERVIEW:");
        
        // Pending orders
        $pendingOrders = Order::where('currency', 'USDT-BEP20')
            ->where('status', '0')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
            
        $this->info("Pending BEP20 orders (24h): {$pendingOrders}");
        
        // Completed deposits
        $completedDeposits = Deposite::where('currency', 'USDT-BEP20')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
            
        $this->info("Completed deposits (24h): {$completedDeposits}");
        
        // Users with BEP20 wallets
        $usersWithWallets = User::whereNotNull('wallet_address')->count();
        $this->info("Users with BEP20 wallets: {$usersWithWallets}");
        
        // Recent orders
        $this->info("\n2. RECENT ORDERS:");
        $recentOrders = Order::where('currency', 'USDT-BEP20')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        if ($recentOrders->count() > 0) {
            foreach ($recentOrders as $order) {
                $status = $order->status == '1' ? 'Completed' : 'Pending';
                $this->info("Order {$order->order_number}: \${$order->amount} - {$status}");
            }
        } else {
            $this->info("No recent BEP20 orders found");
        }
        
        // System health
        $this->info("\n3. SYSTEM HEALTH:");
        try {
            $bscService = new BscService();
            $this->info("✓ BSC Service: Operational");
            
            $bep20Service = new Bep20DepositService();
            $this->info("✓ BEP20 Deposit Service: Operational");
            
        } catch (\Exception $e) {
            $this->error("✗ Service Error: " . $e->getMessage());
        }
        
        $this->info("\n=== OPTIONS ===");
        $this->info("--process-pending    Process all pending deposits");
        $this->info("--check-order=X      Check specific order");
        $this->info("--user-id=X         Check user's wallet status");
    }
    
    private function checkSpecificOrder($orderNumber)
    {
        $this->info("Checking order: {$orderNumber}");
        
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            $this->error("Order not found");
            return 1;
        }
        
        $this->info("Order details:");
        $this->info("- Amount: \${$order->amount}");
        $this->info("- Currency: {$order->currency}");
        $this->info("- Status: {$order->status}");
        $this->info("- User ID: {$order->user_id}");
        $this->info("- Wallet: {$order->wallet_address}");
        
        if ($order->currency != 'USDT-BEP20') {
            $this->warn("This is not a BEP20 order");
            return 0;
        }
        
        // Check deposit record
        $deposit = $order->deposit;
        if ($deposit) {
            $this->info("✓ Deposit record exists: \${$deposit->amount}");
        } else {
            $this->warn("⚠ No deposit record found");
        }
        
        // Test processing
        if ($order->status == '0') {
            $this->info("\nTesting deposit processing...");
            $bep20Service = new Bep20DepositService();
            $result = $bep20Service->processAutomaticDeposit($order);
            
            if ($result['success']) {
                $this->info("✓ Processing successful: " . $result['message']);
            } else {
                $this->warn("⚠ Processing failed: " . $result['message']);
            }
        }
        
        return 0;
    }
    
    private function checkUserWallet($userId)
    {
        $this->info("Checking user wallet: {$userId}");
        
        $user = User::find($userId);
        if (!$user) {
            $this->error("User not found");
            return 1;
        }
        
        $this->info("User: {$user->username}");
        $this->info("Balance: \${$user->balance}");
        
        if (!$user->wallet_address) {
            $this->warn("⚠ User has no BEP20 wallet");
            return 0;
        }
        
        $this->info("Wallet: {$user->wallet_address}");
        
        try {
            $bscService = new BscService();
            $usdtBalance = $bscService->getUsdtBalance($user->wallet_address);
            $bnbBalance = $bscService->getBnbBalance($user->wallet_address);
            
            $this->info("USDT Balance: {$usdtBalance}");
            $this->info("BNB Balance: {$bnbBalance}");
            
            // Check recent transactions
            $transactions = $bscService->getRecentUsdtTransactions($user->wallet_address, 10);
            $this->info("Recent transactions: " . count($transactions));
            
            foreach ($transactions as $i => $tx) {
                $this->info("TX " . ($i + 1) . ": {$tx['value']} USDT");
            }
            
        } catch (\Exception $e) {
            $this->error("Wallet check failed: " . $e->getMessage());
        }
        
        return 0;
    }
    
    private function processPendingDeposits()
    {
        $this->info("Processing pending BEP20 deposits...");
        
        $bep20Service = new Bep20DepositService();
        $results = $bep20Service->processPendingDeposits(20);
        
        $this->info("Results:");
        $this->info("- Processed: {$results['processed']}");
        $this->info("- Successful: {$results['successful']}");
        $this->info("- Failed: {$results['failed']}");
        
        if (!empty($results['details'])) {
            $this->info("\nDetails:");
            foreach ($results['details'] as $detail) {
                $status = $detail['success'] ? '✓' : '✗';
                $this->info("{$status} {$detail['order_number']}: \${$detail['amount']} - {$detail['message']}");
            }
        }
        
        return 0;
    }
}
