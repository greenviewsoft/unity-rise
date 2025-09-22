<?php

namespace App\Listeners;

use App\Events\InvestmentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ClearTeamBusinessVolumeCache
{
    /**
     * Handle the event.
     *
     * @param  InvestmentCreated  $event
     * @return void
     */
    public function handle(InvestmentCreated $event)
    {
        $investment = $event->investment;
        $user = $investment->user;
        
        if ($user) {
            // Clear cache for the user and all their upline
            $user->clearTeamBusinessVolumeCache();
        }
    }
}