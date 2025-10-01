<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\RankUpgradeService;
use Illuminate\Support\Facades\Log;

class InvestmentRankUpgradeListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $rankUpgradeService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(RankUpgradeService $rankUpgradeService)
    {
        $this->rankUpgradeService = $rankUpgradeService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // Automatic rank upgrades disabled - users must manually claim via Rank Upgrade Center
        Log::info('Investment event received but automatic rank upgrades are disabled');
        return;
        
        /*
        try {
            // Check if the event has an investment
            if (isset($event->investment)) {
                $this->rankUpgradeService->processInvestmentRankCheck($event->investment);
            }
            // Check if the event has a user (for other triggers)
            elseif (isset($event->user)) {
                $this->rankUpgradeService->checkUserRankUpgrade($event->user);
            }
        } catch (\Exception $e) {
            Log::error('Investment rank upgrade listener failed: ' . $e->getMessage());
        }
        */
    }
}
