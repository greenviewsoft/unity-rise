<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Investment;
use App\Models\InvestmentProfit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DistributeDailyProfit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:distribute-profit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute daily profit to active investments (Mon-Fri only)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();
        
        // Check if today is Monday to Friday (1-5)
        if ($today->dayOfWeek < 1 || $today->dayOfWeek > 5) {
            $this->info('Profit distribution only runs Monday to Friday. Today is ' . $today->format('l'));
            return 0;
        }

        $this->info('Starting daily profit distribution for ' . $today->format('Y-m-d'));

        try {
            DB::beginTransaction();

            // Get all active investments that are eligible for profit
            $activeInvestments = Investment::eligibleForProfitToday()->get();

            $totalDistributed = 0;
            $investmentsProcessed = 0;

            foreach ($activeInvestments as $investment) {
                // Check if profit already distributed today
                $existingProfit = InvestmentProfit::where('investment_id', $investment->id)
                    ->where('profit_date', $today->format('Y-m-d'))
                    ->first();

                if ($existingProfit) {
                    continue; // Skip if already processed today
                }

                // Calculate daily profit
                $dailyProfit = $investment->daily_profit;
                $dayNumber = $investment->profit_days_completed + 1;

                // Get user and previous balance
                $user = User::find($investment->user_id);
                $previousBalance = $user->balance;
                
                // Create profit record
                $investmentProfit = InvestmentProfit::create([
                    'investment_id' => $investment->id,
                    'user_id' => $investment->user_id,
                    'amount' => $dailyProfit,
                    'profit_date' => $today->format('Y-m-d'),
                    'day_number' => $dayNumber
                ]);

                // Update user balance
                $user->increment('balance', $dailyProfit);
                $newBalance = $user->fresh()->balance;
                
                // Create transaction log
                \App\Models\Log::createTransactionLog(
                    $user->id,
                    'investment_profit',
                    $dailyProfit,
                    $previousBalance,
                    $newBalance,
                    'App\\Models\\InvestmentProfit',
                    $investmentProfit->id,
                    "Daily investment profit - Day {$dayNumber}",
                    [
                        'investment_id' => $investment->id,
                        'day_number' => $dayNumber,
                        'profit_date' => $today->format('Y-m-d'),
                        'plan_type' => $investment->plan_type,
                        'investment_amount' => $investment->amount
                    ]
                );

                // Update investment progress
                $investment->increment('profit_days_completed');
                $investment->update(['last_profit_date' => $today->format('Y-m-d')]);

                // Check if investment is completed
                $totalDays = $investment->plan_id && $investment->plan ? $investment->plan->duration_days : 50;
                if ($investment->profit_days_completed >= $totalDays) {
                    $investment->update(['status' => Investment::STATUS_COMPLETED]);
                    $this->info("Investment #{$investment->id} completed for user #{$user->id}");
                }

                $totalDistributed += $dailyProfit;
                $investmentsProcessed++;

                $this->info("Distributed ${dailyProfit} to user #{$user->id} for investment #{$investment->id}");
            }

            DB::commit();

            $this->info("Profit distribution completed!");
            $this->info("Total investments processed: {$investmentsProcessed}");
            $this->info("Total amount distributed: ${totalDistributed}");

            // Log the distribution
            Log::info('Daily profit distribution completed', [
                'date' => $today->format('Y-m-d'),
                'investments_processed' => $investmentsProcessed,
                'total_distributed' => $totalDistributed
            ]);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error during profit distribution: ' . $e->getMessage());
            Log::error('Daily profit distribution failed', [
                'date' => $today->format('Y-m-d'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}