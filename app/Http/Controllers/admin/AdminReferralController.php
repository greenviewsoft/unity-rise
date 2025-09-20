<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommission;
use App\Models\RankReward;
use App\Models\User;
use App\Services\ReferralCommissionService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReferralController extends Controller
{
    protected $commissionService;

    public function __construct()
    {
        $this->commissionService = new ReferralCommissionService();
    }

    /**
     * Show referral dashboard
     */
    public function index()
    {
        $stats = $this->getOverallStats();
        $topEarners = $this->getTopEarners();
        $recentCommissions = $this->getRecentCommissions();
        
        return view('admin.referral.index', compact('stats', 'topEarners', 'recentCommissions'));
    }

    /**
     * Show commission reports
     */
    public function commissionReports(Request $request)
    {
        $query = ReferralCommission::with(['referrer', 'referred'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        if ($request->has('rank') && $request->rank) {
            $query->where('rank', $request->rank);
        }

        if ($request->has('referrer_id') && $request->referrer_id) {
            $query->where('referrer_id', $request->referrer_id);
        }

        $commissions = $query->paginate(50);
        
        // Get summary stats for filtered data
        $summaryStats = $this->getFilteredStats($request);
        
        return view('admin.referral.commission-reports', compact('commissions', 'summaryStats'));
    }

    /**
     * Show rank rewards reports
     */
    public function rankRewards(Request $request)
    {
        $query = RankReward::with(['user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('rank') && $request->rank) {
            $query->where('new_rank', $request->rank);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $rankRewards = $query->paginate(50);
        
        return view('admin.referral.rank-rewards', compact('rankRewards'));
    }

    /**
     * Show user referral details
     */
    public function userDetails($userId)
    {
        $user = User::with(['referrer', 'referrals', 'referralCommissions', 'rankRewards'])
            ->findOrFail($userId);
        
        $stats = $this->commissionService->getReferralStats($userId);
        
        return view('admin.referral.user-details', compact('user', 'stats'));
    }

    /**
     * Export commission data
     */
    public function exportCommissions(Request $request)
    {
        $query = ReferralCommission::with(['referrer', 'referred']);

        // Apply same filters as reports
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $commissions = $query->get();
        
        $filename = 'commission_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Referrer Name',
                'Referrer Email',
                'Referred Name', 
                'Referred Email',
                'Investment Amount',
                'Commission Amount',
                'Level',
                'Rank',
                'Date'
            ]);
            
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->referrer->name ?? 'N/A',
                    $commission->referrer->email ?? 'N/A',
                    $commission->referred->name ?? 'N/A',
                    $commission->referred->email ?? 'N/A',
                    $commission->investment_amount,
                    $commission->commission_amount,
                    $commission->level,
                    $commission->rank,
                    $commission->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get overall statistics
     */
    private function getOverallStats()
    {
        return [
            'total_commissions' => ReferralCommission::sum('commission_amount'),
            'total_commissions_count' => ReferralCommission::count(),
            'total_rank_rewards' => RankReward::sum('reward_amount'),
            'total_rank_rewards_count' => RankReward::count(),
            'today_commissions' => ReferralCommission::whereDate('created_at', Carbon::today())->sum('commission_amount'),
            'today_commissions_count' => ReferralCommission::whereDate('created_at', Carbon::today())->count(),
            'this_month_commissions' => ReferralCommission::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('commission_amount'),
            'active_referrers' => User::whereHas('referralCommissions')->count(),
            'total_referrals' => User::whereNotNull('refer_id')->count()
        ];
    }

    /**
     * Get top earners
     */
    private function getTopEarners($limit = 10)
    {
        return User::select('users.*')
            ->selectRaw('SUM(referral_commissions.commission_amount) as total_earned')
            ->leftJoin('referral_commissions', 'users.id', '=', 'referral_commissions.referrer_id')
            ->groupBy('users.id')
            ->orderBy('total_earned', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent commissions
     */
    private function getRecentCommissions($limit = 10)
    {
        return ReferralCommission::with(['referrer', 'referred'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get filtered statistics
     */
    private function getFilteredStats(Request $request)
    {
        $query = ReferralCommission::query();

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        if ($request->has('rank') && $request->rank) {
            $query->where('rank', $request->rank);
        }

        return [
            'total_amount' => $query->sum('commission_amount'),
            'total_count' => $query->count(),
            'average_commission' => $query->avg('commission_amount'),
            'unique_referrers' => $query->distinct('referrer_id')->count('referrer_id')
        ];
    }
}