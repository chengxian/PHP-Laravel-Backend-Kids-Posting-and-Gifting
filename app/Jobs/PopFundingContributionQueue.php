<?php

namespace App\Jobs;

use App\Exceptions\GrifterException;
use App\FundingContributionQueue;
use App\Grifter;
use App\Jobs\Job;
use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kidgifting\DwollaWrapper\DwollaWrapperTransferClient;
use Kidgifting\USAlliance\Models\LoanApplication;

class PopFundingContributionQueue extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var LoanApplication
     */
    private $loan;

    /**
     * Create a new job instance.
     *
     * @param LoanApplication $loan
     */
    public function __construct(LoanApplication $loan)
    {
        $this->loan = $loan;
    }

    /**
     * A Loan has been approved
     * DeQueue waiting Parent transfers.
     *
     * @param Grifter $grifter
     * @param DwollaWrapperTransferClient $dwollaClient
     */
    public function handle(Grifter $grifter, DwollaWrapperTransferClient $dwollaClient)
    {
        $child = $this->loan->child;
        
        // find any queued payments that exist for this loan/child
        $queuedPayments = FundingContributionQueue::whereChildId($child->id);

        $queuedPayments->each(function (FundingContributionQueue $queued) use ($grifter, $dwollaClient) {
            $fundingContribution = $queued->spawnToFundingContribution();

            if ($fundingContribution->isAllowed()) {
                $fundingContribution->save();
                try {
                    $grifter->startTransfer($fundingContribution, $dwollaClient);
                } catch (GrifterException $e) {
                    // FIXME handle exceptions
                } catch (Exception $e) {
                    // FIXME handle exceptions
                }
            } else {
                // FIXME handle exceptions
            }
        });
    }
}
