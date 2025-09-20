<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Investment;

class InvestmentCreated
{
    use Dispatchable, SerializesModels;

    public $investment;

    /**
     * Create a new event instance.
     *
     * @param Investment $investment
     * @return void
     */
    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }
}
