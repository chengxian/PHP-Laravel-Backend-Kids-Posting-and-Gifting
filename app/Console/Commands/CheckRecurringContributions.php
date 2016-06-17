<?php

namespace App\Console\Commands;

use App\Jobs\CheckRecurringContribution;
use App\RecurringContribution;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class CheckRecurringContributions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kidgifting:checkrecurringcontributions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $today;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->today = Carbon::today();
        $recurrings = RecurringContribution::all();
        $recurrings->each(function ($item) {
            $job = (new CheckRecurringContribution($item, $this->today))->onQueue('recurring');
            dispatch($job);
        });
    }
}
