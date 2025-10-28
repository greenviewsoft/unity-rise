<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\InvestmentProfit;
use App\Models\ReferralCommission;
use App\Models\ReferralCommissionLevel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\InvestmentSuccessNotification;
use App\Notifications\ReferralCommissionNotification;
use Illuminate\Support\Facades\Schema;

class InvestmentController extends Controller
{
    /**
     * Show the investment form
     */
    public function index()
    {
        $investmentPlans = InvestmentPlan::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.investment', compact('investmentPlans'));
    }

    /**
     * Store a new investment and distribute referral commissions
     */
    public function invest(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'amount' => 'required|numeric|min:1',
            'plan_type' => 'required|string'
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        
        // Get the selected investment plan
        $plan = InvestmentPlan::findOrFail($request->plan_id);
        
        // Validate plan is active
        if ($plan->status != 1) {
            return back()->with('error', 'Selected investment plan is not available.');
        }
        
        // Validate amount is within plan limits
        if ($amount < $plan->min_amount || $amount > $plan->max_amount) {
            return back()->with('error', 'Investment amount must be between $' . number_format($plan->min_amount) . ' and $' . number_format($plan->max_amount) . '.');
        }

        // Check if user has sufficient balance
        if ($user->balance < $amount) {
            return back()->with('error', 'Insufficient balance for this investment.');
        }

        try {
            DB::beginTransaction();

            // Deduct amount from user balance
            $user->decrement('balance', $amount);
            
            // Calculate daily profit based on plan
            $dailyProfitRate = $plan->daily_profit_percentage / 100;
            $dailyProfit = $amount * $dailyProfitRate;

            // Create investment record
            $investment = Investment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'plan_type' => $request->plan_type,
                'plan_id' => $plan->id,
                'start_date' => Carbon::now(),
                'daily_profit' => $dailyProfit,
                'total_profit' => 0,
                'status' => Investment::STATUS_ACTIVE,
                'profit_days_completed' => 0
            ]);

            // ✅ Distribute referral commissions (NO SERVICE - direct logic)
            $this->distributeReferralCommissions($investment);

            // Send email notification to user
            try {
                $user->notify(new InvestmentSuccessNotification($investment, $plan));
            } catch (\Exception $e) {
                Log::error('Failed to send investment success email: ' . $e->getMessage());
            }

            DB::commit();

            return back()->with('success', 'Investment of $' . number_format($amount, 2) . ' in ' . $plan->name . ' has been successfully created!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Investment creation failed: ' . $e->getMessage());
            
            if(config('app.debug')){
                return back()->with('error', 'Investment failed: ' . $e->getMessage());
            }
            return back()->with('error', 'Investment failed. Please try again.');
        }
    }

/**
 * ✅ FIXED: Distribute referral commissions up to rank's max_levels
 */
private function distributeReferralCommissions(Investment $investment)
{
    $investor = $investment->user;
    $investmentAmount = $investment->amount;

    Log::info("=== COMMISSION DISTRIBUTION START ===");
    Log::info("Investment ID: {$investment->id}, Amount: \${$investmentAmount}, Investor: {$investor->id}");

    if (!$investor->refer_id) {
        Log::info("No referrer found for user {$investor->id}");
        return;
    }

    $currentUser = $investor;
    $level = 1;
    $commissionsDistributed = [];

    while ($currentUser->refer_id && $level <= 40) {
        
        $referrer = User::find($currentUser->refer_id);
        
        if (!$referrer) {
            Log::info("Level {$level}: Referrer not found - BREAKING chain");
            break; // ✅ Break instead of continue
        }

        $referrerRankId = $referrer->rank ?? 1;
        
        // Get rank data for max_levels check
        $rankData = ReferralCommissionLevel::where('rank_id', $referrerRankId)
            ->where('is_active', 1)
            ->first();

        if (!$rankData) {
            Log::warning("Level {$level}: No rank data for Rank ID {$referrerRankId} - BREAKING chain");
            break; // ✅ Break instead of continue
        }

        // Check if level exceeds max_levels
        if ($level > $rankData->max_levels) {
            Log::info("Level {$level}: User {$referrer->id} ({$rankData->rank_name}) exceeds max_levels ({$rankData->max_levels}) - SKIPPING");
            $currentUser = $referrer;
            $level++;
            continue;
        }

        // Get commission rate for this exact rank + level
        $commissionData = ReferralCommissionLevel::where('rank_id', $referrerRankId)
            ->where('level', $level)
            ->where('is_active', 1)
            ->first();

        if (!$commissionData) {
            Log::warning("Level {$level}: No commission data for Rank {$referrerRankId} at level {$level} - SKIPPING");
            $currentUser = $referrer;
            $level++;
            continue;
        }

        $commissionRate = $commissionData->commission_rate;
        $commissionAmount = ($investmentAmount * $commissionRate) / 100;

        // Create commission record
        $commission = ReferralCommission::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $investor->id,
            'investment_id' => $investment->id,
            'investment_amount' => $investmentAmount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
            'level' => $level,
            'rank' => $referrerRankId,
            'rank_id' => $referrerRankId,
            'status' => 'approved',
            'commission_date' => now()
        ]);

        // Update referrer balance if commission > 0
        if ($commissionAmount > 0) {
            $referrer->increment('refer_commission', $commissionAmount);
            $referrer->increment('balance', $commissionAmount);

            if (Schema::hasColumn('users', 'total_referral_earnings')) {
                $referrer->increment('total_referral_earnings', $commissionAmount);
            }

            // Send email notification to referrer
            try {
                $referrer->notify(new ReferralCommissionNotification($commission, $investor, $level));
            } catch (\Exception $e) {
                Log::error('Failed to send referral commission email: ' . $e->getMessage());
            }

            $commissionsDistributed[] = [
                'level' => $level,
                'user_id' => $referrer->id,
                'rank' => $rankData->rank_name,
                'rate' => $commissionRate,
                'amount' => $commissionAmount
            ];

            Log::info("Level {$level}: User {$referrer->id} ({$rankData->rank_name}) - Rate: {$commissionRate}% - Amount: \${$commissionAmount} ✅");
        }

        // Move to next level
        $currentUser = $referrer;
        $level++;
    }

    Log::info("=== COMMISSION DISTRIBUTION END ===");
    Log::info("Total Levels Processed: " . ($level - 1));
    Log::info("Commissions Paid: " . count($commissionsDistributed));
    Log::info("Total Amount Distributed: $" . array_sum(array_column($commissionsDistributed, 'amount')));
}




    /**
     * Calculate and distribute daily profits
     */
    public function calculateDailyProfits()
    {
        $today = Carbon::now();
        
        // Only process on weekdays (Mon-Fri)
        if (!$today->isWeekday()) {
            return;
        }

        $eligibleInvestments = Investment::eligibleForProfitToday()->get();

        foreach ($eligibleInvestments as $investment) {
            $this->processDailyProfit($investment);
        }
    }

    /**
     * Process daily profit for a single investment
     */
    private function processDailyProfit(Investment $investment)
    {
        try {
            DB::beginTransaction();

            $dailyProfit = $investment->calculateDailyProfit();
            $today = Carbon::now();

            // Create profit record
            InvestmentProfit::create([
                'investment_id' => $investment->id,
                'user_id' => $investment->user_id,
                'amount' => $dailyProfit,
                'profit_date' => $today->format('Y-m-d'),
                'day_number' => $investment->profit_days_completed + 1
            ]);

            // Add profit to user balance
            $investment->user->increment('balance', $dailyProfit);

            // Update investment record
            $investment->increment('profit_days_completed');
            $investment->increment('total_profit', $dailyProfit);
            $investment->update(['last_profit_date' => $today]);

            // Check if investment is completed
            $totalDays = $investment->plan_id && $investment->plan ? $investment->plan->duration_days : 50;
            if ($investment->profit_days_completed >= $totalDays) {
                $investment->markAsCompleted();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Daily profit processing failed for investment ID: ' . $investment->id . ' - ' . $e->getMessage());
        }
    }

    /**
     * Get user's investment history
     */
    public function history()
    {
        $user = Auth::user();
        
        // Get investments with detailed relationships
        $investments = $user->investments()
            ->with(['profits', 'plan'])
            ->latest()
            ->paginate(10);
        
        // Calculate comprehensive statistics
        $totalInvestments = $user->investments()->count();
        $activeInvestments = $user->investments()->where('status', 'active')->count();
        $completedInvestments = $user->investments()->where('status', 'completed')->count();
        $totalInvested = $user->investments()->sum('amount');
        $totalProfitEarned = $user->investments()->sum('total_profit');
        $totalActiveAmount = $user->investments()->where('status', 'active')->sum('amount');
        
        // Get active investments with remaining time calculations
        $activeInvestmentsList = $user->investments()
            ->where('status', 'active')
            ->with(['profits', 'plan'])
            ->get()
            ->map(function($investment) {
                $startDate = Carbon::parse($investment->start_date);
                $now = Carbon::now();
                $activeDays = $startDate->diffInDays($now);
                
                // Calculate remaining days based on plan duration
                $planDuration = $investment->plan ? $investment->plan->duration_days : 50;
                $remainingDays = max(0, $planDuration - $investment->profit_days_completed);
                
                $investment->active_days = $activeDays;
                $investment->remaining_days = $remainingDays;
                $investment->plan_duration = $planDuration;
                $investment->progress_percentage = $planDuration > 0 ? ($investment->profit_days_completed / $planDuration) * 100 : 0;
                
                return $investment;
            });
        
        $statistics = [
            'total_investments' => $totalInvestments,
            'active_investments' => $activeInvestments,
            'completed_investments' => $completedInvestments,
            'total_invested' => $totalInvested,
            'total_profit_earned' => $totalProfitEarned,
            'total_active_amount' => $totalActiveAmount,
            'active_investments_list' => $activeInvestmentsList
        ];
        
        return view('user.investments.history', compact('investments', 'statistics'));
    }

    /**
     * Get user's active investments
     */
    public function active()
    {
        $investments = Auth::user()->investments()->active()->with('profits')->latest()->get();
        return view('user.investments.active', compact('investments'));
    }

    /**
     * Get user's daily profit history
     */
    public function profitHistory(Request $request)
    {
        $user = Auth::user();
        
        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Validate dates
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');
        
        // Get daily profit history
        $profitHistory = InvestmentProfit::where('user_id', $user->id)
            ->whereBetween('profit_date', [$startDate, $endDate])
            ->with(['investment.plan'])
            ->orderBy('profit_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Calculate statistics
        $totalProfits = InvestmentProfit::where('user_id', $user->id)
            ->whereBetween('profit_date', [$startDate, $endDate])
            ->sum('amount');
        
        $averageDailyProfit = InvestmentProfit::where('user_id', $user->id)
            ->whereBetween('profit_date', [$startDate, $endDate])
            ->avg('amount');
        
        $totalDays = InvestmentProfit::where('user_id', $user->id)
            ->whereBetween('profit_date', [$startDate, $endDate])
            ->distinct('profit_date')
            ->count('profit_date');
        
        $maxDailyProfit = InvestmentProfit::where('user_id', $user->id)
            ->whereBetween('profit_date', [$startDate, $endDate])
            ->max('amount');
        
        
        // Get profit by investment plan
        $profitByPlan = InvestmentProfit::where('investment_profits.user_id', $user->id)
            ->whereBetween('investment_profits.profit_date', [$startDate, $endDate])
            ->join('investments', 'investment_profits.investment_id', '=', 'investments.id')
            ->leftJoin('investment_plans', 'investments.plan_id', '=', 'investment_plans.id')
            ->selectRaw('COALESCE(investment_plans.name, investments.plan_type) as plan_name, SUM(investment_profits.amount) as total_profit, COUNT(*) as profit_count')
            ->groupBy('plan_name')
            ->orderBy('total_profit', 'desc')
            ->get();
        
        // Get recent profit trends (last 7 days)
        $recentTrends = InvestmentProfit::where('investment_profits.user_id', $user->id)
            ->where('investment_profits.profit_date', '>=', now()->subDays(7))
            ->selectRaw('DATE(investment_profits.profit_date) as date, SUM(investment_profits.amount) as total_profit')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        $statistics = [
            'total_profits' => $totalProfits,
            'average_daily_profit' => $averageDailyProfit,
            'total_days' => $totalDays,
            'max_daily_profit' => $maxDailyProfit,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'profit_by_plan' => $profitByPlan,
            'recent_trends' => $recentTrends
        ];
        
        return view('user.investments.profit-history', compact('profitHistory', 'statistics'));
    }

    /**
     * Export profit history to CSV
     */
    public function exportProfitHistory(Request $request)
    {
        $user = Auth::user();
        
        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Validate dates
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');
        
        // Get profit history data
        $profitHistory = InvestmentProfit::where('investment_profits.user_id', $user->id)
            ->whereBetween('investment_profits.profit_date', [$startDate, $endDate])
            ->with(['investment.plan'])
            ->orderBy('investment_profits.profit_date', 'desc')
            ->orderBy('investment_profits.created_at', 'desc')
            ->get();
        
        $filename = 'profit_history_' . $user->id . '_' . $startDate . '_to_' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($profitHistory) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Date', 'Investment ID', 'Plan Name', 'Day Number', 'Profit Amount', 'Created At']);
            
            foreach ($profitHistory as $profit) {
                fputcsv($file, [
                    $profit->profit_date,
                    $profit->investment_id,
                    $profit->investment->plan ? $profit->investment->plan->name : $profit->investment->plan_type,
                    $profit->day_number,
                    '$' . number_format($profit->amount, 2),
                    $profit->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}