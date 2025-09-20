<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvestmentPlanController extends Controller
{
    /**
     * Display a listing of investment plans
     */
    public function index()
    {
        $plans = InvestmentPlan::with('creator')
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
        
        return view('admin.investment-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new investment plan
     */
    public function create()
    {
        return view('admin.investment-plans.create');
    }

    /**
     * Store a newly created investment plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric|min:1',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_profit_percentage' => 'required|numeric|min:0|max:100',
            'duration' => 'required|integer|min:1|max:3650',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Calculate total profit percentage based on daily profit and duration
        $totalProfitPercentage = $request->daily_profit_percentage * $request->duration;

        $plan = InvestmentPlan::create([
            'name' => $request->name,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'daily_profit_percentage' => $request->daily_profit_percentage,
            'total_profit_percentage' => $totalProfitPercentage,
            'duration_days' => $request->duration,
            'commission_levels' => 4, // Default commission levels
            'rank_requirement' => null, // No rank requirement by default
            'description' => $request->description,
            'status' => $request->status === 'active',
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.investment-plans.index')
                        ->with('success', 'Investment plan created successfully!');
    }

    /**
     * Display the specified investment plan
     */
    public function show(InvestmentPlan $investmentPlan)
    {
        $investmentPlan->load('creator', 'investments.user');
        
        return view('admin.investment-plans.show', compact('investmentPlan'));
    }

    /**
     * Show the form for editing the specified investment plan
     */
    public function edit(InvestmentPlan $investmentPlan)
    {
        $plan = $investmentPlan;
        return view('admin.investment-plans.edit', compact('plan'));
    }

    /**
     * Update the specified investment plan
     */
    public function update(Request $request, InvestmentPlan $investmentPlan)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric|min:1',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_profit_percentage' => 'required|numeric|min:0|max:100',
            'duration' => 'required|integer|min:1|max:3650',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Calculate total profit percentage based on daily profit and duration
        $totalProfitPercentage = $request->daily_profit_percentage * $request->duration;

        $investmentPlan->update([
            'name' => $request->name,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'daily_profit_percentage' => $request->daily_profit_percentage,
            'total_profit_percentage' => $totalProfitPercentage,
            'duration_days' => $request->duration,
            'description' => $request->description,
            'status' => $request->status === 'active'
        ]);

        return redirect()->route('admin.investment-plans.index')
                        ->with('success', 'Investment plan updated successfully!');
    }

    /**
     * Remove the specified investment plan
     */
    public function destroy(InvestmentPlan $investmentPlan)
    {
        // Check if plan has active investments
        if ($investmentPlan->investments()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete plan with existing investments!');
        }

        $investmentPlan->delete();

        return redirect()->route('admin.investment-plans.index')
                        ->with('success', 'Investment plan deleted successfully!');
    }

    /**
     * Activate investment plan
     */
    public function activate($id)
    {
        $plan = InvestmentPlan::findOrFail($id);
        $plan->update(['status' => true]);

        return redirect()->back()
                        ->with('success', 'Investment plan activated successfully!');
    }

    /**
     * Deactivate investment plan
     */
    public function deactivate($id)
    {
        $plan = InvestmentPlan::findOrFail($id);
        $plan->update(['status' => false]);

        return redirect()->back()
                        ->with('success', 'Investment plan deactivated successfully!');
    }

    /**
     * Show investment plan settings
     */
    public function settings()
    {
        $activePlans = InvestmentPlan::active()->count();
        $inactivePlans = InvestmentPlan::inactive()->count();
        $totalInvestments = InvestmentPlan::withCount('investments')->get()->sum('investments_count');
        
        return view('admin.investment-plans.settings', compact(
            'activePlans', 
            'inactivePlans', 
            'totalInvestments'
        ));
    }

    /**
     * Delete investment plan (alternative route)
     */
    public function delete($id)
    {
        $plan = InvestmentPlan::findOrFail($id);
        
        // Check if plan has active investments
        if ($plan->investments()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete plan with existing investments!');
        }

        $plan->delete();

        return redirect()->route('admin.investment-plans.index')
                        ->with('success', 'Investment plan deleted successfully!');
    }
}