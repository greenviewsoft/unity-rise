<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommissionLevel;
use Illuminate\Http\Request;

class RankCommissionController extends Controller
{
    /**
     * Display rank commission details
     */
    public function index()
    {
        // Get all rank commission data
        $rankCommissionData = [];
        
        // Define rank names
        $rankNames = [
            1 => 'Rookie',
            2 => 'Bronze', 
            3 => 'Silver',
            4 => 'Gold',
            5 => 'Platinum',
            6 => 'Diamond',
            7 => 'Master',
            8 => 'Grand Master',
            9 => 'Champion',
            10 => 'Legend',
            11 => 'Mythic',
            12 => 'Immortal'
        ];
        
        // Get commission data for each rank
        for ($rank = 1; $rank <= 12; $rank++) {
            $commissionLevels = ReferralCommissionLevel::where('rank_id', $rank)
                ->where('is_active', true)
                ->orderBy('level')
                ->get();
            
            if ($commissionLevels->count() > 0) {
                $maxRate = $commissionLevels->max('commission_rate');
                $minRate = $commissionLevels->min('commission_rate');
                $totalLevels = $commissionLevels->count();
                
                $rankCommissionData[$rank] = [
                    'rank_id' => $rank,
                    'rank_name' => $rankNames[$rank],
                    'total_levels' => $totalLevels,
                    'max_rate' => $maxRate,
                    'min_rate' => $minRate,
                    'levels' => $commissionLevels
                ];
            }
        }
        
        return view('admin.rank-commission.index', compact('rankCommissionData', 'rankNames'));
    }
    
    /**
     * Show the form for editing the specified rank commission level
     */
    public function edit($id)
    {
        $commissionLevel = ReferralCommissionLevel::findOrFail($id);
        
        // Define rank names
        $rankNames = [
            1 => 'Rookie',
            2 => 'Bronze', 
            3 => 'Silver',
            4 => 'Gold',
            5 => 'Platinum',
            6 => 'Diamond',
            7 => 'Master',
            8 => 'Grand Master',
            9 => 'Champion',
            10 => 'Legend',
            11 => 'Mythic',
            12 => 'Immortal'
        ];
        
        return view('admin.rank-commission.edit', compact('commissionLevel', 'rankNames'));
    }
    
    /**
     * Update the specified rank commission level
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'rank_reward' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);
        
        $commissionLevel = ReferralCommissionLevel::findOrFail($id);
        
        // Additional validation
        if ($request->commission_rate < 0 || $request->commission_rate > 100) {
            return redirect()->back()
                           ->withErrors(['commission_rate' => 'Commission rate must be between 0 and 100'])
                           ->withInput();
        }
        
        if ($request->rank_reward && $request->rank_reward < 0) {
            return redirect()->back()
                           ->withErrors(['rank_reward' => 'Rank reward must be positive'])
                           ->withInput();
        }
        
        $commissionLevel->update([
            'commission_rate' => $request->commission_rate,
            'rank_reward' => $request->rank_reward ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.rankcommission.index')
                        ->with('success', 'Commission level updated successfully!');
    }
}