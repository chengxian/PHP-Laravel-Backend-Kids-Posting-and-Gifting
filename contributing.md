  * [Dev Flow](#dev-flow)
  * [Migrations](#migrations)
    * [Adding a Migration](#adding-a-migration)
    * [Editing a Migration](#editing-a-migration)
    * [Generating a migration](#generating-a-migration)
  * [Models](#models)
    * [Creating a Model](#creating-a-model)
    * [Updating Model docblocks](#updating-model-docblocks)
  * [Events &amp; Listeners](#events--listeners)
  * [Jobs](#jobs)

Created by [gh-md-toc](https://github.com/ekalinin/github-markdown-toc.go)


# Dev Flow

* When you start a new ticket from Pivotal Tracker, create a new branch. For example, if you are working on KF-123 create a new branch /dev/KF-123/some-short-description
* If your feature branch becomes long running (over 24 hours) merge from /dev daily. If it gets close to 3 days, your ticket is probably too big and should be split up
* Make sure your branch has the appropriate autoloader: ```artisan clear-compiled && composer dump-autoload && artisan optimize```
* When a ticket is done, run non-EndToEnd PHPUnit tests locally against that branch.
* IF you are modifying LaraVault, run LaraVaultEndToEnd tests *(while running Vault)*
* IF you are modifying Dwolla, USAlliance, or Emailage code, run their EndToEnd tests
* IF you are modifying USAlliance Balance querying, run the VPN PHPUnit tests *(while connected to VPN)*
* When PHPUnit tests pass, open a pull request against /dev and check that CI passes
* When CI passes assign pull request to the other dev for code review
* When the other dev approves, merge the pull request into dev. Do not squash commits. 
* Delete your feature branch from Github

# Migrations

## Adding a Migration

```bash
artisan make:migration create_table_funding_contributions_queue
```
Then, [update docblocks](#updating-model-docblocks)

## Editing a Migration

* Always add a new migration to update a table. Never update old migrations
* Always add rollbacks for added or edited fields

```bash
artisan make:migration update_fundables_tabld_add_dates
```
Then, [update docblocks](#updating-model-docblocks)

## Generating a migration

Sometimes you need to generate a full migration from an existing table *(for testing as an exable)*

In AppServiceProvider, uncomment these lines. They are left commented out because they sometimes conflict with laracasts/generators and are rarely used

```php
//            $this->app->register('Way\Generators\GeneratorsServiceProvider');
//            $this->app->register('Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider');

```

```bash
artisan migrate:generate funding_contributions
```

Re-comment the lines when you are done

# Models

## Creating a Model
```bash
artisan make:model FundingContributionQueue
```

## Updating Model docblocks

After every migration run this. *(If it looks ugly, delete the whole docblock from the model and run it again)*

```
artisan ide-helper:models --dir="packages/Kidgifting/DwollaWrapper/src/Models/" --dir="packages/Kidgifting/USAlliance/src/Models/" --dir="packages/Kidgifting/LaraVault/src" --dir="packages/Kidgifting/FrozenSettings/src/"
```

# Events & Listeners

```bash
artisan make:listener PopFundingContributionQueueListener --event USALoanApproved
```

# Jobs

```bash
artisan make:job CheckRecurringContribution
```