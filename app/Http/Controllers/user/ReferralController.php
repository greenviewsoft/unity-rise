<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommission;
use App\Models\ReferralCommissionLevel;
use App\Models\User;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReferralController extends Controller
{
    /**
     * Show referral dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $stats = $this->getReferralStats($user->id);
        
        return view('user.referral.index', compact('stats'));
    }

    /**
     * Show commission history
     */
    public function commissionHistory(Request $request)
    {
        $user = Auth::user();
        $query = ReferralCommission::where('referrer_id', $user->id)
            ->with(['referred'])
            ->orderBy('created_at', 'desc');

        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by level if provided
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by rank if provided
        if ($request->has('rank_id') && $request->rank_id) {
            $query->where('rank_id', $request->rank_id);
        }

        $commissions = $query->paginate(20);
        
        // Get all available ranks for filter dropdown
        $ranks = ReferralCommissionLevel::select('rank_id', 'rank_name')
            ->distinct()
            ->orderBy('rank_id')
            ->get();
        
        return view('user.referral.commission-history', compact('commissions', 'ranks'));
    }

    /**
     * Show referral tree
     */
    public function referralTree()
    {
        $user = Auth::user();
        $referrals = $user->referrals()->with(['referrals'])->get();
        
        return view('user.referral.tree', compact('referrals'));
    }

    /**
     * Get commission statistics for AJAX
     */
    public function getCommissionStats(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $stats = [];
        
        switch ($period) {
            case 'day':
                $stats = $this->getDailyStats($user->id);
                break;
            case 'week':
                $stats = $this->getWeeklyStats($user->id);
                break;
            case 'month':
                $stats = $this->getMonthlyStats($user->id);
                break;
            case 'year':
                $stats = $this->getYearlyStats($user->id);
                break;
        }
        
        return response()->json($stats);
    }

    /**
     * Get daily commission statistics
     */
    private function getDailyStats($userId)
    {
        return ReferralCommission::where('referrer_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->selectRaw('SUM(commission_amount) as total, COUNT(*) as count')
            ->first();
    }

    /**
     * Get weekly commission statistics
     */
    private function getWeeklyStats($userId)
    {
        return ReferralCommission::where('referrer_id', $userId)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('SUM(commission_amount) as total, COUNT(*) as count')
            ->first();
    }

    /**
     * Get monthly commission statistics
     */
    private function getMonthlyStats($userId)
    {
        return ReferralCommission::where('referrer_id', $userId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('SUM(commission_amount) as total, COUNT(*) as count')
            ->first();
    }

    /**
     * Get yearly commission statistics
     */
    private function getYearlyStats($userId)
    {
        return ReferralCommission::where('referrer_id', $userId)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('SUM(commission_amount) as total, COUNT(*) as count')
            ->first();
    }

    /**
     * Generate referral link
     */
    public function generateReferralLink()
    {
        $user = Auth::user();
        
        // Generate referral code if not exists
        if (!$user->refer_code) {
            $user->refer_code = $this->generateUniqueReferralCode();
            $user->save();
        }
        
        $referralLink = url('/register?ref=' . $user->refer_code);
        
        return response()->json([
            'success' => true,
            'referral_link' => $referralLink,
            'referral_code' => $user->refer_code
        ]);
    }

    /**
     * Generate unique referral code
     */
    private function generateUniqueReferralCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (User::where('refer_code', $code)->exists());
        
        return $code;
    }

    /**
     * ✅ Get referral statistics for dashboard
     */
    private function getReferralStats($userId)
    {
        $user = User::find($userId);
        
        // Get current rank info
        $currentRankData = ReferralCommissionLevel::where('rank_id', $user->rank ?? 1)
            ->first();

        return [
            'total_referrals' => User::where('refer_id', $userId)->count(),
            
            'active_referrals' => User::where('refer_id', $userId)
                ->whereHas('investments', fn($q) => $q->where('status', 'active'))
                ->count(),
            
            'total_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->sum('commission_amount'),
            
            'this_month_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('commission_amount'),
            
            'today_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->whereDate('created_at', now()->toDateString())
                ->sum('commission_amount'),
            
            'current_rank' => $currentRankData ? $currentRankData->rank_name : 'Rookie',
            
            'current_rank_max_levels' => $currentRankData ? $currentRankData->max_levels : 6,
            
            'team_investment' => $this->getTeamInvestment($userId),
            
            'total_investment_volume' => ReferralCommission::where('referrer_id', $userId)
                ->sum('investment_amount'),
            
            'pending_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'pending')
                ->sum('commission_amount'),
            
            'level_wise_commissions' => $this->getLevelWiseCommissions($userId),
            
            'rank_progress' => $this->getRankProgress($userId)
        ];
    }

    /**
     * ✅ Get team investment (recursive calculation up to 40 levels)
     */
    private function getTeamInvestment($userId, $level = 1, $maxLevel = 40)
    {
        if ($level > $maxLevel) {
            return 0;
        }

        $directReferrals = User::where('refer_id', $userId)->get();
        $totalInvestment = 0;

        foreach ($directReferrals as $referral) {
            // Get user's total investments
            $totalInvestment += Investment::where('user_id', $referral->id)->sum('amount');
            
            // Get downline investments (recursive)
            $totalInvestment += $this->getTeamInvestment($referral->id, $level + 1, $maxLevel);
        }

        return $totalInvestment;
    }

    /**
     * ✅ Get level-wise commission breakdown
     */
    private function getLevelWiseCommissions($userId)
    {
        return ReferralCommission::where('referrer_id', $userId)
            ->where('status', 'approved')
            ->selectRaw('level, COUNT(*) as count, SUM(commission_amount) as total')
            ->groupBy('level')
            ->orderBy('level')
            ->get()
            ->keyBy('level');
    }

    /**
     * ✅ Get rank progress information
     */
    private function getRankProgress($userId)
    {
        $user = User::find($userId);
        $currentRankId = $user->rank ?? 1;
        
        // Get current rank data
        $currentRank = ReferralCommissionLevel::where('rank_id', $currentRankId)
            ->first();
        
        // Get next rank data
        $nextRank = ReferralCommissionLevel::where('rank_id', $currentRankId + 1)
            ->first();
        
        return [
            'current_rank_id' => $currentRankId,
            'current_rank_name' => $currentRank ? $currentRank->rank_name : 'Rookie',
            'current_max_levels' => $currentRank ? $currentRank->max_levels : 6,
            'next_rank_id' => $nextRank ? $nextRank->rank_id : null,
            'next_rank_name' => $nextRank ? $nextRank->rank_name : null,
            'next_max_levels' => $nextRank ? $nextRank->max_levels : null,
            'next_rank_reward' => $nextRank ? $nextRank->rank_reward : null,
        ];
    }

    /**
     * ✅ Show level-wise commission details
     */
    public function levelWiseCommissions()
    {
        $user = Auth::user();
        
        $levelStats = ReferralCommission::where('referrer_id', $user->id)
            ->where('status', 'approved')
            ->selectRaw('level, COUNT(*) as total_commissions, SUM(commission_amount) as total_amount, AVG(commission_amount) as avg_amount')
            ->groupBy('level')
            ->orderBy('level')
            ->get();
        
        // Get user's current rank max levels
        $userRank = $user->rank ?? 1;
        $maxLevels = ReferralCommissionLevel::where('rank_id', $userRank)
            ->value('max_levels') ?? 6;
        
        return view('user.referral.level-wise', compact('levelStats', 'maxLevels'));
    }

    /**
     * ✅ Show team structure (direct referrals with stats)
     */
    public function teamStructure()
    {
        $user = Auth::user();
        
        $directReferrals = User::where('refer_id', $user->id)
            ->withCount(['investments', 'referrals'])
            ->withSum('investments', 'amount')
            ->with(['investments' => function($query) {
                $query->where('status', 'active');
            }])
            ->paginate(20);
        
        $teamStats = [
            'total_members' => $this->countTotalTeamMembers($user->id),
            'total_team_investment' => $this->getTeamInvestment($user->id),
            'direct_referrals' => $directReferrals->total()
        ];
        
        return view('user.referral.team-structure', compact('directReferrals', 'teamStats'));
    }

    /**
     * ✅ Count total team members (all levels)
     */
    private function countTotalTeamMembers($userId, $level = 1, $maxLevel = 40)
    {
        if ($level > $maxLevel) {
            return 0;
        }

        $directCount = User::where('refer_id', $userId)->count();
        $indirectCount = 0;

        $directReferrals = User::where('refer_id', $userId)->get();
        
        foreach ($directReferrals as $referral) {
            $indirectCount += $this->countTotalTeamMembers($referral->id, $level + 1, $maxLevel);
        }

        return $directCount + $indirectCount;
    }

    /**
     * ✅ Get commission summary by rank
     */
    public function commissionSummaryByRank()
    {
        $user = Auth::user();
        
        $rankWiseCommissions = ReferralCommission::where('referrer_id', $user->id)
            ->where('status', 'approved')
            ->join('referral_commission_levels', 'referral_commissions.rank_id', '=', 'referral_commission_levels.rank_id')
            ->selectRaw('referral_commission_levels.rank_name, COUNT(*) as total_count, SUM(referral_commissions.commission_amount) as total_amount')
            ->groupBy('referral_commission_levels.rank_name')
            ->get();
        
        return response()->json($rankWiseCommissions);
    }

    /**
     * ✅ Export commission history (CSV/Excel)
     */
    public function exportCommissions(Request $request)
    {
        $user = Auth::user();
        
        $commissions = ReferralCommission::where('referrer_id', $user->id)
            ->with(['referred'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'commissions_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Date', 'Referred User', 'Level', 'Investment Amount', 'Commission Rate', 'Commission Amount', 'Status']);
            
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->created_at->format('Y-m-d H:i:s'),
                    $commission->referred->username ?? 'N/A',
                    $commission->level,
                    '$' . number_format($commission->investment_amount, 2),
                    $commission->commission_rate . '%',
                    '$' . number_format($commission->commission_amount, 2),
                    ucfirst($commission->status)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}