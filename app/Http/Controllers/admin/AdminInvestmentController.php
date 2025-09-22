<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInvestmentController extends Controller
{
    /**
     * Display all user investment history records
     */
    public function index(Request $request)
    {
        $query = Investment::with(['user', 'plan', 'profits'])
                          ->orderBy('created_at', 'desc');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }
        
        // Search by username or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        $investments = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total_investments' => Investment::count(),
            'active_investments' => Investment::where('status', Investment::STATUS_ACTIVE)->count(),
            'completed_investments' => Investment::where('status', Investment::STATUS_COMPLETED)->count(),
            'cancelled_investments' => Investment::where('status', Investment::STATUS_CANCELLED)->count(),
            'total_amount' => Investment::sum('amount'),
            'active_amount' => Investment::where('status', Investment::STATUS_ACTIVE)->sum('amount'),
            'total_withdraws' => Withdraw::where('status', 1)->sum('amount'),
            'total_withdraws_count' => Withdraw::where('status', 1)->count()
        ];
        
        return view('admin.investments.index', compact('investments', 'stats'));
    }
    
    /**
     * Show investment details
     */
    public function show($id)
    {
        $investment = Investment::with(['user', 'plan', 'profits'])->findOrFail($id);
        
        return view('admin.investments.show', compact('investment'));
    }
    
    /**
     * Cancel an investment
     */
    public function cancel($id)
    {
        $investment = Investment::findOrFail($id);
        
        if ($investment->status !== Investment::STATUS_ACTIVE) {
            return back()->with('error', 'Only active investments can be cancelled.');
        }
        
        DB::beginTransaction();
        try {
            // Return the investment amount to user
            $investment->user->increment('balance', $investment->amount);
            
            // Update investment status
            $investment->update([
                'status' => Investment::STATUS_CANCELLED,
                'end_date' => now()
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Investment cancelled successfully and amount returned to user.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel investment. Please try again.');
        }
    }
    
    /**
     * Complete an investment manually
     */
    public function complete($id)
    {
        $investment = Investment::findOrFail($id);
        
        if ($investment->status !== Investment::STATUS_ACTIVE) {
            return back()->with('error', 'Only active investments can be completed.');
        }
        
        DB::beginTransaction();
        try {
            // Mark as completed (this will return capital to user via model method)
            $investment->markAsCompleted();
            
            DB::commit();
            
            return back()->with('success', 'Investment completed successfully and capital returned to user.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to complete investment. Please try again.');
        }
    }
}