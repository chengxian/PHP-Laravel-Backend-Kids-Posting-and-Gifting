<?php

namespace App\Listeners;

use App\Events\USALoanApproved;
use App\Jobs\PopFundingContributionQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kidgifting\USAlliance\Models\LoanApplication;

class PopFundingContributionQueueListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  USALoanApproved  $event
     * @return void
     */
    public function handle(USALoanApproved $event)
    {
        $loan = $event->getLoan();

        $job = (new PopFundingContributionQueue($loan))->onQueue('parentqueuedpaymentpop');
        dispatch($job);
    }
}
