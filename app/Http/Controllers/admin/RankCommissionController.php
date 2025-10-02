<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommissionLevel;
use App\Models\RankRequirement;
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
        
        // Get all rank requirements with reward amounts
        $rankRequirements = RankRequirement::where('is_active', true)
            ->orderBy('rank')
            ->get()
            ->keyBy('rank');
        
        // Get commission data for each rank
        foreach ($rankRequirements as $rank => $requirement) {
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
                    'rank_name' => $requirement->rank_name,
                    'total_levels' => $totalLevels,
                    'max_rate' => $maxRate,
                    'min_rate' => $minRate,
                    'reward_amount' => $requirement->reward_amount,
                    'requirement' => $requirement, // Add full requirement object
                    'levels' => $commissionLevels
                ];
            }
        }
        
        return view('admin.rank-commission.index', compact('rankCommissionData'));
    }
    
    /**
     * Show the form for editing the specified rank commission level
     */
    public function edit($id)
    {
        // Get the commission level
        $commissionLevel = ReferralCommissionLevel::findOrFail($id);
        
        // Get rank requirement for this rank
        $rankRequirement = RankRequirement::where('rank', $commissionLevel->rank_id)->first();
        
        // Check if rank requirement exists
        if (!$rankRequirement) {
            return redirect()->route('admin.rankcommission.index')
                ->with('error', 'Rank requirement not found for this commission level. Please check your database.');
        }
        
        return view('admin.rank-commission.edit', compact('commissionLevel', 'rankRequirement'));
    }
    
    /**
     * Update the specified rank commission level
     */
    public function update(Request $request, $id)
    {
        // Validation rules
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'rank_reward' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Find commission level
        $commissionLevel = ReferralCommissionLevel::findOrFail($id);
        
        // Additional validation
        if ($request->commission_rate < 0 || $request->commission_rate > 100) {
            return redirect()->back()
                ->withErrors(['commission_rate' => 'Commission rate must be between 0 and 100'])
                ->withInput();
        }
        
        if ($request->rank_reward < 0) {
            return redirect()->back()
                ->withErrors(['rank_reward' => 'Rank reward must be a positive number'])
                ->withInput();
        }
        
        try {
            // Update commission level (commission_rate and is_active)
            $commissionLevel->update([
                'commission_rate' => $request->commission_rate,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);
            
            // Update rank requirement (reward_amount)
            $rankRequirement = RankRequirement::where('rank', $commissionLevel->rank_id)->first();
            
            if ($rankRequirement) {
                $rankRequirement->update([
                    'reward_amount' => $request->rank_reward
                ]);
            }
            
            return redirect()->route('admin.rankcommission.index')
                ->with('success', 'Commission level and rank reward updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating commission: ' . $e->getMessage())
                ->withInput();
        }
    }
}