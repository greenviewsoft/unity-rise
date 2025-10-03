<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\InvestmentProfit;
use App\Models\User;
use App\Models\ReferralCommission;
use App\Models\ReferralCommissionLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
 * ✅ Distribute referral commissions up to 40 levels
 * Skip levels that exceed user's rank max_levels
 */
private function distributeReferralCommissions(Investment $investment)
{
    $investor = $investment->user;
    $investmentAmount = $investment->amount;

    Log::info("Starting commission distribution for Investment ID: {$investment->id}, Amount: {$investmentAmount}");

    // Check if investor has a referrer
    if (!$investor->refer_id) {
        Log::info("No referrer found for user {$investor->id}");
        return;
    }

    $currentUser = $investor;
    $level = 1;

    // Loop through up to 40 levels
    while ($currentUser->refer_id && $level <= 40) {
        
        // Get the upline/referrer
        $referrer = User::find($currentUser->refer_id);
        
        if (!$referrer) {
            Log::info("Referrer not found at level {$level}");
            break;
        }

        // Get referrer's current rank (default to 1 if null)
        $referrerRankId = $referrer->rank ?? 1;
        
        // Get commission data for this rank and level
        $commissionData = ReferralCommissionLevel::where('rank_id', $referrerRankId)
            ->where('level', $level)
            ->where('is_active', 1)
            ->first();

        if (!$commissionData) {
            Log::info("No commission rule for Rank ID {$referrerRankId}, Level {$level} - Skipping");
            
            // Move to next upline
            $currentUser = $referrer;
            $level++;
            continue;
        }

        // Check if level is within referrer's max_levels
        if ($level > $commissionData->max_levels) {
            Log::info("Level {$level} exceeds max_levels ({$commissionData->max_levels}) for Rank {$commissionData->rank_name} - Skipping");
            
            // Move to next upline
            $currentUser = $referrer;
            $level++;
            continue;
        }

        // Calculate commission
        $commissionAmount = ($investmentAmount * $commissionData->commission_rate) / 100;

        if ($commissionAmount <= 0) {
            Log::info("Commission rate is 0 for Level {$level} - Skipping");
            
            // Move to next upline
            $currentUser = $referrer;
            $level++;
            continue;
        }

        // Create commission record
        ReferralCommission::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $investor->id,
            'investment_amount' => $investmentAmount,
            'commission_amount' => $commissionAmount,
            'level' => $level,
            'rank' => $referrerRankId,  // Store rank ID (1-12)
            'commission_date' => now()
        ]);

        // Update referrer's balance and commission
        $referrer->increment('refer_commission', $commissionAmount);
        $referrer->increment('balance', $commissionAmount);

        // Optional: Update total_referral_earnings if column exists
        if (Schema::hasColumn('users', 'total_referral_earnings')) {
            $referrer->increment('total_referral_earnings', $commissionAmount);
        }

        Log::info("Commission paid to User {$referrer->id} (Rank: {$commissionData->rank_name}, Level {$level}): \${$commissionAmount}");

        // Move to next upline
        $currentUser = $referrer;
        $level++;
    }

    Log::info("Commission distribution completed for Investment ID: {$investment->id}");
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
}