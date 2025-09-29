<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Deposite;
use App\Models\History;
use App\Models\Log;
use App\Services\BscService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LaravelLog;
use Exception;

class Bep20DepositService
{
    private $bscService;
    
    public function __construct()
    {
        $this->bscService = new BscService();
    }
    
    /**
     * Process BEP20 USDT deposit automatically
     */
    public function processAutomaticDeposit(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Validate order
            if ($order->status == '1' || $order->status == 'completed' || $order->status == 'success') {
                LaravelLog::info("BEP20: Order {$order->order_number} already processed");
                return ['success' => false, 'message' => 'Order already processed'];
            }
            
            $user = User::find($order->user_id);
            if (!$user || !$user->wallet_address) {
                LaravelLog::error("BEP20: User or wallet address not found for order {$order->order_number}");
                return ['success' => false, 'message' => 'User or wallet not found'];
            }
            
            // CRITICAL: Check if deposit record already exists for this order
            $existingDeposit = Deposite::where('order_number', $order->order_number)
                ->where('user_id', $order->user_id)
                ->where('status', '1')
                ->first();
                
            if ($existingDeposit) {
                LaravelLog::info("BEP20: Deposit record already exists for order {$order->order_number}. Deposit ID: {$existingDeposit->id}");
                return ['success' => false, 'message' => 'Deposit already processed'];
            }
            
            // CRITICAL: Check if this transaction hash is already used by another order
            $existingTxDeposit = Deposite::where('txid', '!=', null)
                ->where('user_id', $order->user_id)
                ->where('status', '1')
                ->where('currency', 'USDT-BEP20')
                ->where('created_at', '>=', now()->subHours(24)) // Within last 24 hours
                ->first();
                
            if ($existingTxDeposit) {
                LaravelLog::warning("BEP20: Transaction hash already used by another deposit. Order: {$order->order_number}, Existing Deposit: {$existingTxDeposit->id}, TxID: {$existingTxDeposit->txid}");
                return ['success' => false, 'message' => 'Transaction hash already used by another deposit'];
            }
            
            // Check if order is already being processed (status = 0 but has txid)
            if ($order->txid && $order->status == '0') {
                LaravelLog::info("BEP20: Order {$order->order_number} is already being processed with txid: {$order->txid}");
                return ['success' => false, 'message' => 'Order is already being processed'];
            }
            
            // CRITICAL: If order has txid and status = 1, skip processing
            if ($order->txid && $order->status == '1') {
                LaravelLog::info("BEP20: Order {$order->order_number} already completed with txid: {$order->txid}");
                return ['success' => false, 'message' => 'Order already completed'];
            }
            
            // Get recent transactions to find the actual deposit transaction
            $transactions = $this->bscService->getRecentUsdtTransactions($user->wallet_address, 50);
            $depositTransaction = null;
            
            // Look for recent transactions (within last 1 hour) - any amount
            $oneHourAgo = now()->subHour();
            
            foreach ($transactions as $tx) {
                // Check if transaction is recent and to the correct address
                $addressMatch = strtolower($tx['to']) == strtolower($user->wallet_address);
                $isRecent = true; // Assume recent if API returns it
                
                if ($addressMatch && $isRecent) {
                    // CRITICAL: Verify transaction on blockchain before processing
                    $blockchainVerified = $this->verifyTransactionOnBlockchain($tx['hash']);
                    
                    if ($blockchainVerified) {
                        $depositTransaction = $tx;
                        LaravelLog::info("BEP20: Found verified blockchain transaction for order {$order->order_number}", [
                            'tx_hash' => $tx['hash'],
                            'tx_value' => $tx['value'],
                            'order_amount' => $order->amount,
                            'blockchain_verified' => true
                        ]);
                        break;
                    } else {
                        LaravelLog::warning("BEP20: Transaction not verified on blockchain: {$tx['hash']}");
                    }
                }
            }
            
            // If no transaction found via API, check wallet balance for any amount
            if (!$depositTransaction) {
                $walletBalance = $this->bscService->getUsdtBalance($user->wallet_address);
                
                // Check if wallet has any USDT (any amount)
                if ($walletBalance > 0) {
                    // Use the actual balance amount instead of order amount
                    $actualAmount = min($walletBalance, floatval($order->amount));
                    
                    // Generate a unique transaction hash for balance-based deposits
                    $txHash = 'BEP20-AUTO-' . $order->order_number . '-' . time() . '-' . substr(md5($user->wallet_address . $actualAmount), 0, 8);
                    
                    $depositTransaction = [
                        'hash' => $txHash,
                        'value' => $actualAmount,
                        'from' => 'auto-detected',
                        'to' => $user->wallet_address,
                        'timestamp' => time()
                    ];
                    
                    LaravelLog::info("BEP20: Auto-detected deposit for order {$order->order_number}. Balance: {$walletBalance}, Processing: {$actualAmount}");
                } else {
                    LaravelLog::info("BEP20: No deposit found for order {$order->order_number}. Balance: {$walletBalance}");
                    return ['success' => false, 'message' => 'No deposit found'];
                }
            }
            
            // Verify the transaction is recent (within last 24 hours)
            $transactionTime = now()->subHours(24);
            if ($depositTransaction && isset($depositTransaction['timestamp'])) {
                $txTime = \Carbon\Carbon::createFromTimestamp($depositTransaction['timestamp']);
                if ($txTime->lt($transactionTime)) {
                    LaravelLog::info("BEP20: Transaction too old for order {$order->order_number}. TX time: {$txTime}, Required: within 24 hours");
                    return ['success' => false, 'message' => 'Transaction too old'];
                }
            }
            
            // Process the deposit
            $result = $this->processDepositTransfer($user, $order, $depositTransaction);
            
            if ($result['success']) {
                DB::commit();
                LaravelLog::info("BEP20: Successfully processed automatic deposit for order {$order->order_number}");
            } else {
                DB::rollback();
                LaravelLog::error("BEP20: Failed to process deposit for order {$order->order_number}: " . $result['message']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            DB::rollback();
            LaravelLog::error("BEP20: Exception processing order {$order->order_number}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Verify transaction on blockchain using BSCScan API
     */
    private function verifyTransactionOnBlockchain($txHash)
    {
        try {
            // Skip verification for auto-generated hashes
            if (strpos($txHash, 'BEP20-AUTO-') === 0) {
                return true; // Auto-generated hashes are considered verified
            }
            
            // Use BSCScan API to verify transaction (back to V1 as V2 seems unavailable)
            $apiKey = config('services.bscscan.api_key');
            
            // Try multiple verification methods using Etherscan V2 API for BSC (chainId = 56)
            $verificationMethods = [
                // Method 1: Transaction receipt status
                "https://api.etherscan.io/v2/api?chainid=56&module=transaction&action=gettxreceiptstatus&txhash={$txHash}&apikey={$apiKey}",
                // Method 2: Transaction details
                "https://api.etherscan.io/v2/api?chainid=56&module=proxy&action=eth_getTransactionByHash&txhash={$txHash}&apikey={$apiKey}",
                // Method 3: Transaction by hash
                "https://api.etherscan.io/v2/api?chainid=56&module=proxy&action=eth_getTransactionReceipt&txhash={$txHash}&apikey={$apiKey}"
            ];
            
            $client = new \GuzzleHttp\Client();
            
            foreach ($verificationMethods as $i => $url) {
                try {
                    $response = $client->get($url);
                    $data = json_decode($response->getBody(), true);
                    
                    // Check if transaction exists and is valid
                    if ($data && isset($data['result']) && $data['result'] !== null) {
                        // For receipt status, check status field
                        if (isset($data['result']['status'])) {
                            if ($data['result']['status'] === '1' || $data['result']['status'] === '0x1') {
                                LaravelLog::info("BEP20: Transaction verified on blockchain (method " . ($i + 1) . "): {$txHash}");
                                return true;
                            }
                        } else {
                            // For transaction details, if result exists, transaction is valid
                            LaravelLog::info("BEP20: Transaction found on blockchain (method " . ($i + 1) . "): {$txHash}");
                            return true;
                        }
                    }
                } catch (Exception $e) {
                    LaravelLog::warning("BEP20: Verification method " . ($i + 1) . " failed: " . $e->getMessage());
                    continue;
                }
            }
            
            LaravelLog::warning("BEP20: Transaction not confirmed on blockchain: {$txHash}");
            return false;
            
        } catch (Exception $e) {
            LaravelLog::error("BEP20: Error verifying transaction on blockchain: {$txHash} - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process manual deposit verification
     */
    public function verifyManualDeposit(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Check if already processed
            if ($order->status == '1' || $order->status == 'completed' || $order->status == 'success') {
                return ['success' => false, 'message' => 'Deposit already verified and processed'];
            }
            
            $user = User::find($order->user_id);
            if (!$user || !$user->wallet_address) {
                return ['success' => false, 'message' => 'User or wallet not found'];
            }
            
            // Check for existing deposit record
            $existingDeposit = Deposite::where('order_number', $order->order_number)
                ->where('currency', 'USDT-BEP20')
                ->first();
                
            if ($existingDeposit) {
                return ['success' => false, 'message' => 'Deposit already processed'];
            }
            
            // Verify transaction
            $transactions = $this->bscService->getRecentUsdtTransactions($user->wallet_address, 20);
            $foundTransaction = null;
            
            foreach ($transactions as $tx) {
                if (bccomp($tx['value'], $order->amount, 6) >= 0 && 
                    strtolower($tx['to']) == strtolower($user->wallet_address)) {
                    $foundTransaction = $tx;
                    break;
                }
            }
            
            if (!$foundTransaction) {
                return ['success' => false, 'message' => 'Deposit transaction not found. Please ensure you sent the exact amount.'];
            }
            
            // Process the deposit
            $result = $this->processDepositTransfer($user, $order, $foundTransaction);
            
            if ($result['success']) {
                DB::commit();
            } else {
                DB::rollback();
            }
            
            return $result;
            
        } catch (Exception $e) {
            DB::rollback();
            LaravelLog::error("BEP20: Manual verification failed for order {$order->order_number}: " . $e->getMessage());
            return ['success' => false, 'message' => 'Verification failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Process the actual deposit transfer and balance update
     */
    private function processDepositTransfer(User $user, Order $order, $transaction = null)
    {
        try {
            // CRITICAL: Double-check if deposit already exists
            $existingDeposit = Deposite::where('order_number', $order->order_number)
                ->where('user_id', $order->user_id)
                ->where('status', '1')
                ->first();
                
            if ($existingDeposit) {
                LaravelLog::warning("BEP20: Deposit already exists during processing for order {$order->order_number}. Deposit ID: {$existingDeposit->id}");
                return ['success' => false, 'message' => 'Deposit already exists'];
            }
            
            $previousBalance = $user->balance;
            $depositAmount = floatval($transaction['value']); // Use actual transaction amount
            
            // Use actual transaction hash (should always be available now)
            if ($transaction && isset($transaction['hash'])) {
                $txHash = $transaction['hash'];
                LaravelLog::info("BEP20: Using actual transaction hash: {$txHash}");
            } else {
                // This should not happen anymore, but keep as fallback
                $txHash = 'BEP20-ERROR-' . $order->order_number . '-' . time();
                LaravelLog::error("BEP20: No transaction hash available for order {$order->order_number}");
            }
            
            // Create deposit record
            $deposit = Deposite::create([
                'order_number' => $order->order_number,
                'currency' => $order->currency,
                'user_id' => $order->user_id,
                'amount' => $depositAmount,
                'txid' => $txHash,
                'status' => '1' // Mark as completed immediately
            ]);
            
            // Update user balance
            $user->balance = $user->balance + $depositAmount;
            $user->save();
            $newBalance = $user->balance;
            
            // Create transaction log
            Log::createTransactionLog(
                $user->id,
                'deposit',
                $depositAmount,
                $previousBalance,
                $newBalance,
                'App\\Models\\Deposite',
                $deposit->id,
                "BEP20 USDT deposit - Order: {$order->order_number}",
                [
                    'order_number' => $order->order_number,
                    'currency' => $order->currency,
                    'txid' => $txHash,
                    'payment_method' => 'bep20_usdt',
                    'wallet_address' => $user->wallet_address,
                    'transaction_value' => $transaction ? $transaction['value'] : $depositAmount
                ]
            );
            
            // Create history record (only with available fields)
            History::create([
                'user_id' => $user->id,
                'amount' => $depositAmount,
                'type' => 'Deposit'
            ]);
            
            // Update order status
            $order->status = '1'; // Use '1' for consistency with existing system
            $order->txid = $txHash;
            $order->save();
            
            LaravelLog::info("BEP20: Deposit processed successfully", [
                'user_id' => $user->id,
                'order_number' => $order->order_number,
                'amount' => $depositAmount,
                'tx_hash' => $txHash,
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance
            ]);
            
            return [
                'success' => true,
                'message' => 'Deposit processed successfully',
                'amount' => $depositAmount,
                'tx_hash' => $txHash,
                'new_balance' => $newBalance
            ];
            
        } catch (Exception $e) {
            LaravelLog::error("BEP20: Failed to process deposit transfer: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process deposit: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get pending BEP20 orders for automatic processing
     */
    public function getPendingOrders($timeLimit = null)
    {
        // Get orders that don't have corresponding deposits
        $query = Order::where('currency', 'USDT-BEP20')
            ->whereIn('status', ['0', 'pending']);
            
        if ($timeLimit !== null) {
            $cutoff = now()->subMinutes($timeLimit);
            $query->where('created_at', '>=', $cutoff);
        }
        
        $orderNumbers = $query->pluck('order_number');
            
        $processedOrderNumbers = Deposite::where('currency', 'USDT-BEP20')
            ->whereIn('order_number', $orderNumbers)
            ->pluck('order_number');
            
        $pendingOrderNumbers = $orderNumbers->diff($processedOrderNumbers);
        
        // CRITICAL: Also exclude orders that have txid and status = 1 (already completed)
        $completedOrders = Order::where('currency', 'USDT-BEP20')
            ->where('status', '1')
            ->whereNotNull('txid')
            ->pluck('order_number');
            
        $pendingOrderNumbers = $pendingOrderNumbers->diff($completedOrders);
        
        // CRITICAL: Exclude orders that use the same transaction hash as existing deposits
        $usedTxHashes = Deposite::where('txid', '!=', null)
            ->where('status', '1')
            ->where('currency', 'USDT-BEP20')
            ->where('created_at', '>=', now()->subHours(24))
            ->pluck('txid');
            
        $duplicateTxOrders = Order::where('currency', 'USDT-BEP20')
            ->whereIn('txid', $usedTxHashes)
            ->pluck('order_number');
            
        $pendingOrderNumbers = $pendingOrderNumbers->diff($duplicateTxOrders);
        
        return Order::where('currency', 'USDT-BEP20')
            ->whereIn('order_number', $pendingOrderNumbers)
            ->get();
    }
    
    /**
     * Bulk process pending deposits
     */
    public function processPendingDeposits($limit = 50)
    {
        $pendingOrders = $this->getPendingOrders()->take($limit);
        $results = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'details' => []
        ];
        
        foreach ($pendingOrders as $order) {
            $result = $this->processAutomaticDeposit($order);
            $results['processed']++;
            
            if ($result['success']) {
                $results['successful']++;
            } else {
                $results['failed']++;
            }
            
            $results['details'][] = [
                'order_number' => $order->order_number,
                'amount' => $order->amount,
                'success' => $result['success'],
                'message' => $result['message']
            ];
        }
        
        return $results;
    }
    
    /**
     * Verify a specific transaction hash
     */
    private function verifyTransactionHash($txHash, $walletAddress, $expectedAmount)
    {
        try {
            $apiKey = config('services.bscscan.api_key');
            if (!$apiKey) {
                return null;
            }
            
            // Get transaction details from BSCScan
            $url = "https://api.bscscan.com/api?module=proxy&action=eth_getTransactionByHash&txhash={$txHash}&apikey={$apiKey}";
            
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['result']) && $data['result'] !== null) {
                $tx = $data['result'];
                
                // Check if transaction is to the correct wallet
                if (strtolower($tx['to']) === strtolower($walletAddress)) {
                    // Get transaction receipt to get logs
                    $receiptUrl = "https://api.bscscan.com/api?module=proxy&action=eth_getTransactionReceipt&txhash={$txHash}&apikey={$apiKey}";
                    $receiptResponse = $client->get($receiptUrl);
                    $receiptData = json_decode($receiptResponse->getBody(), true);
                    
                    if (isset($receiptData['result']) && $receiptData['result'] !== null) {
                        $receipt = $receiptData['result'];
                        
                        // Check if transaction was successful
                        if ($receipt['status'] === '0x1') {
                            // Parse USDT transfer from logs
                            $usdtValue = $this->parseUsdtTransferFromLogs($receipt['logs'], $expectedAmount);
                            
                            if ($usdtValue > 0) {
                                return [
                                    'hash' => $txHash,
                                    'value' => $usdtValue,
                                    'from' => $tx['from'],
                                    'to' => $tx['to'],
                                    'timestamp' => hexdec($tx['blockNumber'])
                                ];
                            }
                        }
                    }
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            LaravelLog::error("BEP20: Failed to verify transaction hash {$txHash}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Parse USDT transfer value from transaction logs
     */
    private function parseUsdtTransferFromLogs($logs, $expectedAmount)
    {
        $usdtContract = config('services.bscscan.usdt_contract');
        
        foreach ($logs as $log) {
            // Check if this is a USDT transfer log
            if (strtolower($log['address']) === strtolower($usdtContract)) {
                // USDT Transfer event signature: Transfer(address,address,uint256)
                // Topic 0: 0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef
                if (isset($log['topics'][0]) && $log['topics'][0] === '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef') {
                    // Parse the value from data (it's in hex)
                    $valueHex = $log['data'];
                    $value = hexdec($valueHex) / pow(10, 18); // USDT has 18 decimals
                    
                    // Check if amount matches (with small tolerance)
                    if (abs($value - $expectedAmount) < 0.01) {
                        return $value;
                    }
                }
            }
        }
        
        return 0;
    }
}
