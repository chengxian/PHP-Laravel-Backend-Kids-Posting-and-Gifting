<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     * namespace Kidgifting\DwollaWrapper\Command;
     */
    protected $listen = [
        'App\Events\USALoanApproved' => [
            'App\Listeners\PopFundingContributionQueueListener',
        ],

        'Kidgifting\DwollaWrapper\Events\FundingSourceAdded' => [
            'Kidgifting\DwollaWrapper\Listeners\FundingSourceAddedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\FundingSourceRemoved' => [
            'Kidgifting\DwollaWrapper\Listeners\FundingSourceRemovedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\FundingSourceUnverified' => [
            'Kidgifting\DwollaWrapper\Listeners\FundingSourceUnverifiedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\FundingSourceVerified' => [
            'Kidgifting\DwollaWrapper\Listeners\FundingSourceVerifiedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\MicrodepositsAdded' => [
            'Kidgifting\DwollaWrapper\Listeners\MicrodepositsAddedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\MicrodepositsFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\MicrodepositsFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\MicrodepositsCompleted' => [
            'Kidgifting\DwollaWrapper\Listeners\MicrodepositsCompletedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\BankTransferCreated' => [
            'Kidgifting\DwollaWrapper\Listeners\BankTransferCreatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\BankTransferCancelled' => [
            'Kidgifting\DwollaWrapper\Listeners\BankTransferCancelledListener',
        ],

        'Kidgifting\DwollaWrapper\Events\BankTransferFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\BankTransferFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\BankTransferCompleted' => [
            'Kidgifting\DwollaWrapper\Listeners\BankTransferCompletedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\TransferCreated' => [
            'Kidgifting\DwollaWrapper\Listeners\TransferCreatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\TransferCancelled' => [
            'Kidgifting\DwollaWrapper\Listeners\TransferCancelledListener',
        ],

        'Kidgifting\DwollaWrapper\Events\TransferFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\TransferFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\TransferReclaimed' => [
            'Kidgifting\DwollaWrapper\Listeners\TransferReclaimedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\TransferCompleted' => [
            'Kidgifting\DwollaWrapper\Listeners\TransferCompletedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\AccountSuspended' => [
            'Kidgifting\DwollaWrapper\Listeners\AccountSuspendedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\AccountActivated' => [
            'Kidgifting\DwollaWrapper\Listeners\AccountActivatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerCreated' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerCreatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerVerificationDocumentNeeded' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerVerificationDocumentNeededListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerVerificationDocumentUploaded' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerVerificationDocumentUploadedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerVerificationDocumentFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerVerificationDocumentFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerVerificationDocumentApproved' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerVerificationDocumentApprovedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerReverificationNeeded' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerReverificationNeededListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerVerified' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerVerifiedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerSuspended' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerSuspendedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerActivated' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerActivatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerFundingSourceAdded' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerFundingSourceAddedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerFundingSourceRemoved' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerFundingSourceRemovedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerFundingSourceUnverified' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerFundingSourceUnverifiedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerFundingSourceVerified' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerFundingSourceVerifiedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerMicrodepositsAdded' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerMicrodepositsAddedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerMicrodepositsFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerMicrodepositsFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerMicrodepositsCompleted' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerMicrodepositsCompletedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerBankTransferCreated' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerBankTransferCreatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerBankTransferCancelled' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerBankTransferCancelledListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerBankTransferFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerBankTransferFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerBankTransferCompleted' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerBankTransferCompletedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerTransferCreated' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerTransferCreatedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerTransferCancelled' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerTransferCancelledListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerTransferFailed' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerTransferFailedListener',
        ],

        'Kidgifting\DwollaWrapper\Events\CustomerTransferCompleted' => [
            'Kidgifting\DwollaWrapper\Listeners\CustomerTransferCompletedListener',
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
