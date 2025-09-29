<?php

namespace App\Services;

use Web3p\EthereumUtil\Util;
use Binance\BEP20;
use Binance\Bnb;
use Binance\NodeApi;
use Binance\BscscanApi;
use Illuminate\Support\Facades\Log;
use Exception;

class BscService
{
    private $api;
    private $bep20;
    private $bnb;
    private $usdtContractAddress;
    private $adminWalletAddress;
    private $adminPrivateKey;
    
    public function __construct()
    {
        // Use NodeApi for BSC RPC Nodes (recommended for production)
        $uri = env('BSC_RPC_URL', 'https://bsc-dataseed1.binance.org'); // Mainnet
        // For testnet: 'https://data-seed-prebsc-1-s1.binance.org:8545/'
        $network = env('BSC_NETWORK', 'mainnet');
        
        if ($network === 'testnet') {
            $uri = 'https://data-seed-prebsc-1-s1.binance.org:8545/';
            $this->api = new NodeApi($uri, null, null, 'testnet');
        } else {
            $this->api = new NodeApi($uri);
        }
        
        // Alternative: Use BscscanApi (uncomment if preferred)
        // $apiKey = config('services.bscscan.api_key');
        // $this->api = new BscscanApi($apiKey);
        
        // USDT BEP20 Contract Address
        $this->usdtContractAddress = config('services.bscscan.usdt_contract', '0x55d398326f99059fF775485246999027B3197955');
        
        // USDT BEP20 contract configuration
        $bep20Config = [
            'contract_address' => $this->usdtContractAddress,
            'decimals' => 18
        ];
        
        $this->bep20 = new BEP20($this->api, $bep20Config);
        $this->bnb = new Bnb($this->api);
        
        // Admin wallet credentials (should be in .env)
        $this->adminWalletAddress = env('BSC_ADMIN_WALLET_ADDRESS');
        $this->adminPrivateKey = env('BSC_ADMIN_PRIVATE_KEY');
    }
    
    /**
     * Generate a random private key (32 bytes = 256 bit)
     */
    private function generatePrivateKey()
    {
        $privateKey = bin2hex(random_bytes(32)); // 32 bytes = 256 bit
        return $privateKey;
    }

    /**
     * Convert private key to BSC/Ethereum address
     */
    private function privateKeyToAddress($privateKey)
    {
        $util = new Util();
        $publicKey = $util->privateKeyToPublicKey($privateKey);
        $address = $util->publicKeyToAddress($publicKey);

        // Check if address already has 0x prefix
        if (strpos($address, '0x') === 0) {
            return $address;
        }
        
        return '0x' . $address;
    }

    /**
     * Generate a new BEP20 wallet using Web3p\EthereumUtil\Util
     */
    public function generateWallet()
    {
        try {
            // Generate random private key
            $privateKey = $this->generatePrivateKey();
            
            // Convert private key to address
            $address = $this->privateKeyToAddress($privateKey);
            
            return [
                'address' => $address,
                'private_key' => $privateKey
            ];
        } catch (Exception $e) {
            Log::error('Wallet generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get USDT balance for an address
     */
    public function getUsdtBalance($address)
    {
        try {
            $balance = $this->bep20->balance($address);
            return floatval($balance);
        } catch (Exception $e) {
            Log::error('Failed to get USDT balance: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get BNB balance for an address
     */
    public function getBnbBalance($address)
    {
        try {
            $balance = $this->bnb->bnbBalance($address);
            return floatval($balance);
        } catch (Exception $e) {
            Log::error('Failed to get BNB balance: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Check if wallet has sufficient BNB for gas fees
     */
    public function hasSufficientGas($address, $gasRequired = 0.001)
    {
        try {
            $bnbBalance = $this->getBnbBalance($address);
            return $bnbBalance >= $gasRequired;
        } catch (Exception $e) {
            Log::error('Failed to check gas balance: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Transfer BNB from admin wallet to user wallet for gas fees
     */
    public function sendGasFee($toAddress, $amount = 0.002)
    {
        try {
            if (empty($this->adminPrivateKey) || empty($this->adminWalletAddress)) {
                return [
                    'success' => false,
                    'error' => 'Admin wallet credentials not configured'
                ];
            }
            
            // Check admin wallet BNB balance
            $adminBnbBalance = $this->getBnbBalance($this->adminWalletAddress);
            if ($adminBnbBalance < $amount) {
                return [
                    'success' => false,
                    'error' => 'Insufficient BNB in admin wallet for gas fee'
                ];
            }
            
            // Transfer BNB for gas
            $result = $this->bnb->transfer($this->adminPrivateKey, $toAddress, $amount);
            
            if ($result && isset($result['txHash'])) {
                Log::info("Gas fee sent: {$amount} BNB to {$toAddress}, TX: {$result['txHash']}");
                return [
                    'success' => true,
                    'tx_hash' => $result['txHash'],
                    'amount' => $amount
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'BNB transfer failed - no transaction hash returned'
                ];
            }
        } catch (Exception $e) {
            Log::error('Gas fee transfer failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Transfer USDT from user wallet to admin wallet with gas fee check
     */
    public function transferUsdtToAdmin($privateKey, $amount)
    {
        try {
            // Check if admin wallet is configured
            if (empty($this->adminWalletAddress)) {
                return [
                    'success' => false,
                    'error' => 'Admin wallet address not configured'
                ];
            }
            
            // Get user wallet address from private key
            $userAddress = $this->privateKeyToAddress($privateKey);
            
            // Check if user has sufficient BNB for gas
            if (!$this->hasSufficientGas($userAddress)) {
                Log::info("User {$userAddress} has insufficient gas, sending BNB...");
                
                // Send gas fee from admin wallet
                $gasResult = $this->sendGasFee($userAddress);
                if (!$gasResult['success']) {
                    return [
                        'success' => false,
                        'error' => 'Failed to send gas fee: ' . $gasResult['error']
                    ];
                }
                
                // Wait a moment for gas transaction to confirm
                sleep(3);
                
                // Re-check gas balance
                if (!$this->hasSufficientGas($userAddress)) {
                    return [
                        'success' => false,
                        'error' => 'Gas fee transaction not confirmed yet, please try again'
                    ];
                }
            }
            
            // Execute BEP20 transfer
            $result = $this->bep20->transfer($privateKey, $this->adminWalletAddress, $amount);
            
            if ($result && isset($result['txHash'])) {
                return [
                    'success' => true,
                    'tx_hash' => $result['txHash']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Transfer failed - no transaction hash returned'
                ];
            }
        } catch (Exception $e) {
            Log::error('USDT transfer failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if transaction is confirmed
     */
    public function isTransactionConfirmed($txHash)
    {
        try {
            $status = $this->bep20->receiptStatus($txHash);
            return $status === '0x1' || $status === 1;
        } catch (Exception $e) {
            Log::error('Failed to check transaction status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetails($txHash)
    {
        try {
            $tx = $this->bep20->getTransactionByHash($txHash);
            $receipt = $this->bep20->getTransactionReceipt($txHash);
            
            return [
                'transaction' => $tx ?? null,
                'receipt' => $receipt ?? null
            ];
        } catch (Exception $e) {
            Log::error('Failed to get transaction details: ' . $e->getMessage());
            return null;
        }
    }
    
/**
 * Get recent BEP20 USDT transactions for an address
 * Returns array of transactions: ['hash', 'from', 'to', 'value']
 */
public function getRecentUsdtTransactions($address, $limit = 10)
{
    try {
        // Bscscan API ব্যবহার করলে সহজ হয়
        $apiKey = config('services.bscscan.api_key'); 
        if (!$apiKey) {
            throw new Exception('BSC API key not configured');
        }

        // Use Etherscan V2 API for BSC (chainId = 56)
        $url = "https://api.etherscan.io/v2/api?chainid=56&module=account&action=tokentx&contractaddress={$this->usdtContractAddress}&address={$address}&startblock=0&endblock=999999999&page=1&offset={$limit}&sort=desc&apikey={$apiKey}";

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);
        $data = json_decode($response->getBody(), true);

        if ($data['status'] == "1" && isset($data['result'])) {
            $txs = [];
            foreach ($data['result'] as $tx) {
                $txs[] = [
                    'hash' => $tx['hash'],
                    'from' => strtolower($tx['from']),
                    'to' => strtolower($tx['to']),
                    'value' => floatval($tx['value']) / pow(10, 18) // convert from wei to USDT
                ];
            }
            return $txs;
        }

        return [];
    } catch (Exception $e) {
        \Log::error('Failed to fetch recent USDT transactions: ' . $e->getMessage());
        return [];
    }
}



    /**
     * Validate BSC address
     */
    public function isValidAddress($address)
    {
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $address);
    }
}