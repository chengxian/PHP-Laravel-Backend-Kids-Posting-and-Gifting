<?php

namespace App\Console\Commands;

use App\Child;
use App\Fundable;
use App\FundingContribution;
use App\Grifter;
use App\Jobs\SendGift;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kidgifting\DwollaWrapper\DwollaWrapperTransferClient;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class TestGiftsSend extends Command
{

    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kidgifting:testgiftssend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @param Grifter $grifter
     */
    public function handle(Grifter $grifter)
    {
        $user = User::whereIsParent(false)->first();
        $child = Child::all()->first();
        $parent = $child->parent;
        $fundable = Fundable::whereFundableType('Kidgifting\DwollaWrapper\Models\DwollaSourceAccount')->first();

        $gift = $grifter->makeFundingContribution($user, $child, $fundable, 100.00);

        if ($gift->isAllowed()) {
            $gift->save();
            $job = (new SendGift($gift))->onQueue('gifts');
            dispatch($job);
        } else if($gift->isParentAllowedToQueue()) {
            $queue = $gift->spawnToFundingContributionQueue();
            $queue->save();
            $gift->delete(); // May fail, didn't test
        } else {
            /*
             * A FF is trying to gift to a child
             * that doesn't have an account yet
             * The gift box should not be showing in the app
             * Shoult not be allowed
             */
            throw new Exception("Cannot gift:" . $gift->getDisallowedMessagesAsString());
        }
    }
}
