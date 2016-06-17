<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\RecurringContribution;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckRecurringContribution extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var RecurringContribution
     */
    private $recurringContribution;
    /**
     * @var Carbon
     */
    private $today;

    /**
     * Create a new job instance.
     *
     * @param RecurringContribution $recurringContribution
     * @param Carbon $today
     */
    public function __construct(RecurringContribution $recurringContribution, Carbon $today)
    {
        //
        $this->recurringContribution = $recurringContribution;
        $this->today = $today;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recurring = $this->recurringContribution;
        $shouldSpawn = $recurring->shouldSpawn($this->today);
        if ($shouldSpawn) {
            $contribution = $recurring->spawnToFundingContribution();

            // FIXME save fee and contribution amounts
            // FIXME send dwolla transfer
            $contribution->save();
        }

        $recurring->checked_at = Carbon::now();
        $recurring->save();
    }
}
