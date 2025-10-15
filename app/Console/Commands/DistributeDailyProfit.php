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
    protected $signature = 'investment:distribute-profit {--date=} {--force}';

    protected $description = 'Distribute profit 24 hours after investment/last profit (Mon-Fri only, skips weekends)';

    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $force = $this->option('force');
        
        // ✅ Check if today is weekday (Mon-Fri) unless forced
        if (!$date->isWeekday() && !$force) {
            $this->warn("⏸️  Profit distribution skipped - Today is {$date->format('l')} (Weekend)");
            $this->info("💡 Profits resume on Monday. Use --force to test anyway.");
            Log::info('Profit distribution skipped - Weekend', [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('l')
            ]);
            return 0;
        }

        if ($force && !$date->isWeekday()) {
            $this->warn("⚠️  Force mode: Running on {$date->format('l')} (Weekend)");
        }

        $this->info("🚀 Starting profit distribution for {$date->format('Y-m-d l H:i')}");
        $this->info("⏰ Distributing profits 24 hours after investment/last profit\n");

        try {
            DB::beginTransaction();

            // ✅ Get investments where 24+ hours have passed since start OR last profit
            $activeInvestments = Investment::where('status', Investment::STATUS_ACTIVE)
                ->where(function($query) use ($date) {
                    // Case 1: No profits yet - 24h since investment creation
                    $query->whereDoesntHave('profits')
                          ->whereRaw('TIMESTAMPDIFF(HOUR, start_date, ?) >= 24', [$date->toDateTimeString()])
                    // Case 2: Has profits - 24h since last profit
                    ->orWhereHas('profits', function($q) use ($date) {
                        $q->whereRaw(
                            'TIMESTAMPDIFF(HOUR, 
                                (SELECT MAX(created_at) FROM investment_profits WHERE investment_id = investments.id),
                                ?
                            ) >= 24',
                            [$date->toDateTimeString()]
                        );
                    });
                })
                ->with(['user', 'plan'])
                ->get();

            if ($activeInvestments->isEmpty()) {
                $this->info("✅ No investments ready for profit distribution at this time.");
                DB::commit();
                return 0;
            }

            $this->info("Found {$activeInvestments->count()} investments ready for profit\n");

            $totalDistributed = 0;
            $investmentsProcessed = 0;
            $investmentsCompleted = 0;
            $skippedNotReady = 0;

            $progressBar = $this->output->createProgressBar($activeInvestments->count());
            $progressBar->start();

            foreach ($activeInvestments as $investment) {
                try {
                    // ✅ Get last profit time OR investment start time
                    $lastProfit = InvestmentProfit::where('investment_id', $investment->id)
                        ->latest('created_at')
                        ->first();
                    
                    $referenceTime = $lastProfit 
                        ? Carbon::parse($lastProfit->created_at) 
                        : Carbon::parse($investment->start_date);

                    // ✅ Calculate exact hours passed
                    $hoursPassed = $referenceTime->diffInHours($date);

                    // ✅ Must be at least 24 hours
                    if ($hoursPassed < 24) {
                        $hoursRemaining = 24 - $hoursPassed;
                        Log::info("Investment {$investment->id}: Not ready yet - {$hoursRemaining} hours remaining");
                        $skippedNotReady++;
                        $progressBar->advance();
                        continue;
                    }

                    // ✅ Prevent duplicate - check if already processed today
                    $alreadyProcessedToday = InvestmentProfit::where('investment_id', $investment->id)
                        ->whereDate('created_at', $date->format('Y-m-d'))
                        ->exists();
                    
                    if ($alreadyProcessedToday) {
                        $progressBar->advance();
                        continue;
                    }

                    $dailyProfit = $investment->daily_profit;
                    $dayNumber = $investment->profit_days_completed + 1;
                    $user = $investment->user;
                    $previousBalance = $user->balance;
                    
                    // ✅ Create profit record with current timestamp
                    $investmentProfit = InvestmentProfit::create([
                        'investment_id' => $investment->id,
                        'user_id' => $investment->user_id,
                        'amount' => $dailyProfit,
                        'profit_date' => $date->format('Y-m-d'),
                        'day_number' => $dayNumber
                    ]);

                    // Update user balance
                    $user->increment('balance', $dailyProfit);
                    $newBalance = $user->fresh()->balance;
                    
                    // Create transaction log
                    if (class_exists('\App\Models\Log')) {
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
                                'profit_date' => $date->format('Y-m-d'),
                                'profit_time' => $date->format('H:i:s'),
                                'hours_since_last' => $hoursPassed,
                                'plan_type' => $investment->plan_type,
                                'investment_amount' => $investment->amount
                            ]
                        );
                    }

                    // Update investment
                    $investment->increment('profit_days_completed');
                    $investment->increment('total_profit', $dailyProfit);
                    $investment->update(['last_profit_date' => $date->format('Y-m-d')]);

                    // Check if completed
                    $totalDays = $investment->plan ? $investment->plan->duration_days : 50;
                    if ($investment->profit_days_completed >= $totalDays) {
                        $investment->update([
                            'status' => Investment::STATUS_COMPLETED,
                            'end_date' => $date
                        ]);
                        // Return capital to user
                        $user->increment('balance', $investment->amount);
                        $investmentsCompleted++;
                        
                        Log::info("Investment {$investment->id} completed - Capital returned: \${$investment->amount}");
                    }

                    $totalDistributed += $dailyProfit;
                    $investmentsProcessed++;

                    Log::info("Profit distributed", [
                        'investment_id' => $investment->id,
                        'user_id' => $user->id,
                        'amount' => $dailyProfit,
                        'day_number' => $dayNumber,
                        'hours_since_last' => $hoursPassed
                    ]);

                } catch (\Exception $e) {
                    Log::error('Profit distribution failed for investment', [
                        'investment_id' => $investment->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            DB::commit();

            // Summary
            $this->info("✅ Profit distribution completed!");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Date & Time', $date->format('Y-m-d l H:i:s')],
                    ['Day Type', $date->isWeekday() ? '💼 Weekday (Mon-Fri)' : '🎉 Weekend (Forced)'],
                    ['Investments Found', $activeInvestments->count()],
                    ['Successfully Processed', $investmentsProcessed],
                    ['Not Ready Yet (< 24h)', $skippedNotReady],
                    ['Completed Investments', $investmentsCompleted],
                    ['Total Distributed', '$' . number_format($totalDistributed, 2)],
                ]
            );

            if ($investmentsProcessed > 0) {
                $this->info("\n💰 Next profits will be distributed 24 hours from now (if weekday)");
            }

            Log::info('Daily profit distribution completed', [
                'date' => $date->format('Y-m-d H:i:s'),
                'day_of_week' => $date->format('l'),
                'is_weekend' => !$date->isWeekday(),
                'forced' => $force,
                'investments_processed' => $investmentsProcessed,
                'total_distributed' => $totalDistributed
            ]);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during profit distribution: ' . $e->getMessage());
            Log::error('Daily profit distribution failed', [
                'date' => $date->format('Y-m-d H:i:s'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}