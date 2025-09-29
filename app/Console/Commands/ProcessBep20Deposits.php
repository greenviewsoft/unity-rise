<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Bep20DepositService;
use Illuminate\Support\Facades\Log;

class ProcessBep20Deposits extends Command
{
    protected $signature = 'deposits:process-bep20 {--limit=50}';
    protected $description = 'Process pending BEP20 USDT deposits automatically';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        Log::info("Starting BEP20 deposit processing - Limit: {$limit}");
        
        try {
            $bep20Service = new Bep20DepositService();
            $results = $bep20Service->processPendingDeposits($limit);
            
            Log::info("BEP20 deposit processing completed", $results);
            
            $this->info("BEP20 Deposit Processing Results:");
            $this->info("Processed: {$results['processed']}");
            $this->info("Successful: {$results['successful']}");
            $this->info("Failed: {$results['failed']}");
            
            if ($results['successful'] > 0) {
                $this->info("✓ Successfully processed {$results['successful']} deposits");
            }
            
            if ($results['failed'] > 0) {
                $this->warn("⚠ {$results['failed']} deposits failed processing");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("BEP20 deposit processing failed: " . $e->getMessage());
            $this->error("Processing failed: " . $e->getMessage());
            return 1;
        }
    }
}
