<?php

namespace App\Jobs;

use App\FundingContribution;
use App\Grifter;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kidgifting\DwollaWrapper\DwollaWrapperTransferClient;

class SendGift extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var Gift
     */
    private $gift;

    /**
     * SendGift constructor.
     * @param Gift $gift
     */
    public function __construct(FundingContribution $gift)
    {

        $this->gift = $gift;
    }

    /**
     * Execute the job.
     *
     * @param Grifter $grifter
     * @param DwollaWrapperTransferClient $dwollaClient
     * @throws \App\Exceptions\GrifterException
     */
    public function handle(Grifter $grifter, DwollaWrapperTransferClient $dwollaClient)
    {
        $gift = $this->gift;

        try {
            $response = $grifter->startTransfer($gift, $dwollaClient);
        } catch (GrifterException $e) {
            // FIXME handle exceptions
        } catch (Exception $e) {
            // FIXME handle exceptions
        }

        dd($response);

    }
}
