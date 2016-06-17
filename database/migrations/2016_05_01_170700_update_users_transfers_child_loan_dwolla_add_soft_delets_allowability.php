<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTransfersChildLoanDwollaAddSoftDeletsAllowability extends Migration
{
    private $tables = ['children', 'betacodes', 'comments', 'devices', 'dwolla_accounts',
        'dwolla_customers', 'dwolla_events', 'dwolla_transfers', 'followings', 'funding_contributions',
        'invites', 'media', 'notifications', 'post_likes', 'posts', 'settings', 'usa_balance_history',
        'usa_loan_applications', 'users'];

    private $kfApprovedTables = ['usa_loan_applications', 'funding_contributions'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function ($table) {
                $table->softDeletes();
                $table->boolean('is_enabled')->default(true);
            });
        }

        foreach ($this->kfApprovedTables as $table) {
            Schema::table($table, function ($table) {
                $table->boolean('kf_approved')->default(false);
            });
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // https://github.com/laravel/framework/issues/2979
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function ($table) {
                    $table->dropColumn('deleted_at');
                });
            }

            if (Schema::hasColumn($table, 'is_enabled')) {
                Schema::table($table, function ($table) {
                    $table->dropColumn('is_enabled');
                });
            }
        }

        foreach ($this->kfApprovedTables as $table) {
            if (Schema::hasColumn($table, 'kf_approved')) {
                Schema::table($table, function ($table) {
                    $table->dropColumn('kf_approved');
                });
            }
        }
    }
}
