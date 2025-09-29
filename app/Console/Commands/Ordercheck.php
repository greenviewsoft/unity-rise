<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Ordercheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This one will check order';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Old TRX/USDT-TRC20 deposit processing command deprecated
        // Use 'deposits:process-bep20' for BEP20 USDT deposit processing
        $this->warn('⚠ This command has been DEPRECATED.');
        $this->info('Use "php artisan deposits:process-bep20" for BEP20 USDT processing.');
        $this->info('Use "php artisan monitor:bep20-deposits" for monitoring.');
        
        return 0;
    }



}
