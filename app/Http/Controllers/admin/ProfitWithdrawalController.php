<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profitwith;
use Illuminate\Http\Request;

class ProfitWithdrawalController extends Controller
{
    /**
     * Display a listing of profit withdrawals
     */
    public function index()
    {
        $profitWithdrawals = Profitwith::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.profit-withdrawal.index', compact('profitWithdrawals'));
    }

    /**
     * Show the form for creating a new profit withdrawal
     */
    public function create()
    {
        return view('admin.profit-withdrawal.create');
    }

    /**
     * Store a newly created profit withdrawal
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        Profitwith::create([
            'username' => $request->username,
            'amount' => $request->amount
        ]);

        return redirect()->route('admin.profit-withdrawal.index')
                        ->with('success', 'Profit withdrawal entry created successfully!');
    }

    /**
     * Show the form for editing the specified profit withdrawal
     */
    public function edit($id)
    {
        $profitWithdrawal = Profitwith::findOrFail($id);
        
        return view('admin.profit-withdrawal.edit', compact('profitWithdrawal'));
    }

    /**
     * Update the specified profit withdrawal
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $profitWithdrawal = Profitwith::findOrFail($id);
        
        $profitWithdrawal->update([
            'username' => $request->username,
            'amount' => $request->amount
        ]);

        return redirect()->route('admin.profit-withdrawal.index')
                        ->with('success', 'Profit withdrawal entry updated successfully!');
    }

    /**
     * Remove the specified profit withdrawal
     */
    public function destroy($id)
    {
        $profitWithdrawal = Profitwith::findOrFail($id);
        $profitWithdrawal->delete();

        return redirect()->route('admin.profit-withdrawal.index')
                        ->with('success', 'Profit withdrawal entry deleted successfully!');
    }
}