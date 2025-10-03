<?php

namespace App\Services;

use App\Models\ReferralCommission;
use App\Models\ReferralCommissionLevel;
use App\Models\User;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ReferralCommissionService
{
    /**
     * Distribute commissions for an investment
     */
    public function distributeCommissions(Investment $investment)
    {
        $investor = $investment->user;
        $investmentAmount = $investment->amount;

        Log::info("Starting commission distribution for investment ID: {$investment->id}, Amount: {$investmentAmount}");

        if (!$investor->refer_id) {
            Log::info("No referrer found for user {$investor->id}");
            return;
        }

        DB::beginTransaction();
        try {
            $currentUser = $investor;
            $level = 1;

            // Max 40 levels for safety
            while ($currentUser->refer_id && $level <= 40) {
                $referrer = User::find($currentUser->refer_id);
                if (!$referrer) break;

                $referrerRank = $referrer->rank ?? 1;

                // Get max levels for referrer's rank
                $maxLevels = $this->getMaxLevelsForRank($referrerRank);

                // Only pay commission if level <= maxLevels
                if ($level <= $maxLevels) {
                    $commissionData = ReferralCommissionLevel::where('rank_id', $referrerRank)
                        ->where('level', $level)
                        ->where('is_active', 1)
                        ->first();

                    if ($commissionData && $commissionData->commission_rate > 0) {
                        $commissionAmount = ($investmentAmount * $commissionData->commission_rate) / 100;

                        $this->createCommissionRecord($referrer, $investor, $investment, $commissionAmount, $level, $commissionData);

                        Log::info("Commission paid to user {$referrer->id} (Rank {$referrerRank}, Level {$level}) Amount: {$commissionAmount}");
                    }
                }

                $currentUser = $referrer;
                $level++;
            }

            DB::commit();
            Log::info("Commission distribution completed for investment ID: {$investment->id}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Commission distribution failed: {$e->getMessage()}");
        }
    }

    /**
     * Get maximum levels for a specific rank
     */
    private function getMaxLevelsForRank($rankId)
    {
        $rank = \App\Models\RankRequirement::where('rank', $rankId)
            ->where('is_active', true)
            ->first();

        return $rank ? $rank->count_level : 6; // Default to 6 if not set
    }

    /**
     * Create commission record and update balances
     */
    private function createCommissionRecord($referrer, $investor, $investment, $commissionAmount, $level, $commissionData)
    {
        $commission = ReferralCommission::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $investor->id,
            'investment_amount' => $investment->amount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionData->commission_rate,
            'level' => $level,
            'rank_id' => $commissionData->rank_id,
            'commission_date' => now(),
            'status' => 'approved'
        ]);

        // Add to user's balances
        $referrer->increment('refer_commission', $commissionAmount);
        $referrer->increment('balance', $commissionAmount);

        if (Schema::hasColumn('users', 'total_referral_earnings')) {
            $referrer->increment('total_referral_earnings', $commissionAmount);
        }

        return $commission;
    }

    /**
     * Recursive team investment total
     */
    public function getTeamInvestment($userId, $level = 1, $maxLevel = 40)
    {
        if ($level > $maxLevel) return 0;

        $directReferrals = User::where('refer_id', $userId)->get();
        $totalInvestment = 0;

        foreach ($directReferrals as $referral) {
            $totalInvestment += $referral->investments()->sum('amount');
            $totalInvestment += $this->getTeamInvestment($referral->id, $level + 1, $maxLevel);
        }

        return $totalInvestment;
    }

    /**
     * Get referral statistics for dashboard
     */
    public function getReferralStats($userId)
    {
        $user = User::find($userId);
        $currentRank = ReferralCommissionLevel::where('rank_id', $user->rank ?? 1)->first();

        return [
            'total_referrals' => User::where('refer_id', $userId)->count(),
            'active_referrals' => User::where('refer_id', $userId)
                ->whereHas('investments', fn($q) => $q->where('status', 'active'))
                ->count(),
            'total_commissions' => ReferralCommission::where('referrer_id', $userId)->where('status', 'approved')->sum('commission_amount'),
            'this_month_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->sum('commission_amount'),
            'today_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'approved')
                ->whereDate('created_at', now()->toDateString())
                ->sum('commission_amount'),
            'current_rank' => $currentRank ? $currentRank->rank_name : 'Rookie',
            'team_investment' => $this->getTeamInvestment($userId),
            'total_investment_volume' => ReferralCommission::where('referrer_id', $userId)->sum('investment_amount'),
            'pending_commissions' => ReferralCommission::where('referrer_id', $userId)
                ->where('status', 'pending')
                ->sum('commission_amount')
        ];
    }
}
