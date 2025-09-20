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
        // $apiKey = env('BSC_API_KEY', 'QVG2GK41ASNSD21KJTXUAQ4JTRQ4XUQZCX');
        // $this->api = new BscscanApi($apiKey);
        
        // USDT BEP20 Contract Address
        $this->usdtContractAddress = env('USDT_CONTRACT_ADDRESS', '0x55d398326f99059fF775485246999027B3197955');
        
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
     * Transfer USDT from user wallet to admin wallet
     */
    public function transferUsdtToAdmin($privateKey, $amount)
    {
        try {
            // Execute BEP20 transfer
            $result = $this->bep20->transfer($privateKey, $this->adminWalletAddress, $amount);
            
            return $result;
        } catch (Exception $e) {
            Log::error('USDT transfer failed: ' . $e->getMessage());
            throw $e;
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
     * Validate BSC address
     */
    public function isValidAddress($address)
    {
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $address);
    }
}