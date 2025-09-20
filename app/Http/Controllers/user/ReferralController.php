<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommission;
use App\Models\User;
use App\Services\ReferralCommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReferralController extends Controller
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
        $user = Auth::user();
        $stats = $this->commissionService->getReferralStats($user->id);
        
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

        $commissions = $query->paginate(20);
        
        return view('user.referral.commission-history', compact('commissions'));
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
}