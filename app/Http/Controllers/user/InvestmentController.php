<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\InvestmentProfit;
use App\Models\User;
use App\Services\ReferralCommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Store a new investment
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

        // Distribute referral commissions
        $commissionService = new ReferralCommissionService();
        $commissionService->distributeCommissions($investment);

        DB::commit();

        return back()->with('success', 'Investment of $' . number_format($amount, 2) . ' in ' . $plan->name . ' has been successfully created!');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Investment creation failed: '.$e->getMessage());
        
        if(config('app.debug')){
            return back()->with('error', 'Investment failed: '.$e->getMessage());
        }
        return back()->with('error', 'Investment failed. Please try again.');
    }
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
            \Log::error('Daily profit processing failed for investment ID: ' . $investment->id . ' - ' . $e->getMessage());
        }
    }

    // Commission processing is now handled by ReferralCommissionService

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