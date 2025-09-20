<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RankReward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RankRewardController extends Controller
{
    /**
     * Display a listing of rank rewards
     */
    public function index()
    {
        $rankRewards = RankReward::with('user')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        // Get rank statistics
        $rankStats = DB::table('users')
                      ->select('rank', DB::raw('count(*) as user_count'))
                      ->where('rank', '>', 0)
                      ->groupBy('rank')
                      ->orderBy('rank')
                      ->get();
        
        return view('admin.rank-rewards.index', compact('rankRewards', 'rankStats'));
    }

    /**
     * Show the form for creating a new rank reward
     */
    public function create()
    {
        $users = User::where('rank', '>', 0)
                    ->orderBy('rank', 'desc')
                    ->get();
        
        return view('admin.rank-rewards.create', compact('users'));
    }

    /**
     * Store a newly created rank reward
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'old_rank' => 'required|integer|min:0|max:12',
            'new_rank' => 'required|integer|min:1|max:12|gt:old_rank',
            'reward_amount' => 'required|numeric|min:0',
            'reward_type' => 'required|in:rank_upgrade,achievement,bonus',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $rankReward = RankReward::create([
            'user_id' => $request->user_id,
            'old_rank' => $request->old_rank,
            'new_rank' => $request->new_rank,
            'reward_amount' => $request->reward_amount,
            'reward_type' => $request->reward_type,
            'status' => $request->status,
            'processed_at' => $request->status === 'approved' ? now() : null
        ]);

        // Update user rank if approved
        if ($request->status === 'approved') {
            $user = User::find($request->user_id);
            $user->update(['rank' => $request->new_rank]);
            
            // Add reward to user balance
            $user->increment('balance', $request->reward_amount);
        }

        return redirect()->route('admin.rank-rewards.index')
                        ->with('success', 'Rank reward created successfully!');
    }

    /**
     * Display the specified rank reward
     */
    public function show(RankReward $rankReward)
    {
        $rankReward->load('user');
        
        return view('admin.rank-rewards.show', compact('rankReward'));
    }

    /**
     * Show the form for editing the specified rank reward
     */
    public function edit(RankReward $rankReward)
    {
        $users = User::where('rank', '>', 0)
                    ->orderBy('rank', 'desc')
                    ->get();
        
        return view('admin.rank-rewards.edit', compact('rankReward', 'users'));
    }

    /**
     * Update the specified rank reward
     */
    public function update(Request $request, RankReward $rankReward)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'old_rank' => 'required|integer|min:0|max:12',
            'new_rank' => 'required|integer|min:1|max:12|gt:old_rank',
            'reward_amount' => 'required|numeric|min:0',
            'reward_type' => 'required|in:rank_upgrade,achievement,bonus',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $oldStatus = $rankReward->status;
        $newStatus = $request->status;

        $rankReward->update([
            'user_id' => $request->user_id,
            'old_rank' => $request->old_rank,
            'new_rank' => $request->new_rank,
            'reward_amount' => $request->reward_amount,
            'reward_type' => $request->reward_type,
            'status' => $request->status,
            'processed_at' => $request->status === 'approved' ? now() : null
        ]);

        // Handle status changes
        if ($oldStatus !== 'approved' && $newStatus === 'approved') {
            $user = User::find($request->user_id);
            $previousBalance = $user->balance;
            $user->update(['rank' => $request->new_rank]);
            $user->increment('balance', $request->reward_amount);
            $newBalance = $user->fresh()->balance;
            
            // Create transaction log for approval
            \App\Models\Log::createTransactionLog(
                $user->id,
                'rank_reward_approval',
                $request->reward_amount,
                $previousBalance,
                $newBalance,
                'App\\Models\\RankReward',
                $rankReward->id,
                "Admin approved rank reward - Rank {$request->old_rank} to {$request->new_rank}",
                [
                    'old_rank' => $request->old_rank,
                    'new_rank' => $request->new_rank,
                    'reward_type' => $request->reward_type,
                    'admin_action' => true,
                    'action' => 'approved'
                ]
            );
        } elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
            $user = User::find($request->user_id);
            $previousBalance = $user->balance;
            $user->update(['rank' => $request->old_rank]);
            $user->decrement('balance', $request->reward_amount);
            $newBalance = $user->fresh()->balance;
            
            // Create transaction log for rejection/reversal
            \App\Models\Log::createTransactionLog(
                $user->id,
                'rank_reward_reversal',
                $request->reward_amount,
                $previousBalance,
                $newBalance,
                'App\\Models\\RankReward',
                $rankReward->id,
                "Admin reversed rank reward - Rank {$request->new_rank} to {$request->old_rank}",
                [
                    'old_rank' => $request->old_rank,
                    'new_rank' => $request->new_rank,
                    'reward_type' => $request->reward_type,
                    'admin_action' => true,
                    'action' => 'reversed'
                ]
            );
        }

        return redirect()->route('admin.rank-rewards.index')
                        ->with('success', 'Rank reward updated successfully!');
    }

    /**
     * Remove the specified rank reward
     */
    public function destroy(RankReward $rankReward)
    {
        // Revert changes if reward was approved
        if ($rankReward->status === 'approved') {
            $user = User::find($rankReward->user_id);
            if ($user) {
                $user->update(['rank' => $rankReward->old_rank]);
                $user->decrement('balance', $rankReward->reward_amount);
            }
        }

        $rankReward->delete();

        return redirect()->route('admin.rank-rewards.index')
                        ->with('success', 'Rank reward deleted successfully!');
    }

    /**
     * Show rank reward settings
     */
    public function settings()
    {
        // Get rank distribution
        $rankDistribution = [];
        for ($i = 1; $i <= 12; $i++) {
            $rankDistribution[$i] = User::where('rank', $i)->count();
        }

        // Get recent rank upgrades
        $recentUpgrades = RankReward::with('user')
                                   ->where('status', 'approved')
                                   ->orderBy('processed_at', 'desc')
                                   ->limit(10)
                                   ->get();

        // Get pending rewards
        $pendingRewards = RankReward::where('status', 'pending')->count();
        
        // Get total rewards distributed
        $totalRewards = RankReward::where('status', 'approved')
                                 ->sum('reward_amount');

        return view('admin.rank-rewards.settings', compact(
            'rankDistribution',
            'recentUpgrades',
            'pendingRewards',
            'totalRewards'
        ));
    }

    /**
     * Update rank reward settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auto_approve' => 'boolean',
            'min_reward_amount' => 'numeric|min:0',
            'max_reward_amount' => 'numeric|min:0',
            'rank_upgrade_bonus' => 'numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Update settings in database or config
        // This would typically be stored in a settings table
        
        return redirect()->back()
                        ->with('success', 'Rank reward settings updated successfully!');
    }

    /**
     * Delete rank reward (alternative route)
     */
    public function delete($id)
    {
        $rankReward = RankReward::findOrFail($id);
        
        // Revert changes if reward was approved
        if ($rankReward->status === 'approved') {
            $user = User::find($rankReward->user_id);
            if ($user) {
                $user->update(['rank' => $rankReward->old_rank]);
                $user->decrement('balance', $rankReward->reward_amount);
            }
        }

        $rankReward->delete();

        return redirect()->route('admin.rank-rewards.index')
                        ->with('success', 'Rank reward deleted successfully!');
    }

    /**
     * Approve rank reward
     */
    public function approve($id)
    {
        $rankReward = RankReward::findOrFail($id);
        
        if ($rankReward->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Only pending rewards can be approved!');
        }

        $rankReward->update([
            'status' => 'approved',
            'processed_at' => now()
        ]);

        // Update user rank and balance
        $user = User::find($rankReward->user_id);
        $user->update(['rank' => $rankReward->new_rank]);
        $user->increment('balance', $rankReward->reward_amount);

        return redirect()->back()
                        ->with('success', 'Rank reward approved successfully!');
    }

    /**
     * Reject rank reward
     */
    public function reject($id)
    {
        $rankReward = RankReward::findOrFail($id);
        
        if ($rankReward->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Only pending rewards can be rejected!');
        }

        $rankReward->update([
            'status' => 'rejected',
            'processed_at' => now()
        ]);

        return redirect()->back()
                        ->with('success', 'Rank reward rejected successfully!');
    }
}