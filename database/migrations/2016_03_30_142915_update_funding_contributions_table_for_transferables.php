<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFundingContributionsTableForTransferables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_contributions', function (Blueprint $table) {
            $table->dropForeign('funding_contributions_to_user_id_foreign');
            $table->dropColumn('to_user_id'); // should point to child
        });

        // https://github.com/laravel/framework/issues/2979
        Schema::table('funding_contributions', function (Blueprint $table) {
            $table->integer('transfers_id')->unsigned()->nullable();
            $table->string('transfers_type')->nullable();
        });

        // https://github.com/laravel/framework/issues/2979
        foreach (['transfers_id', 'transfers_type'] as $key) {
            if (Schema::hasColumn('funding_contributions', $key)) {
                Schema::table('funding_contributions', function (Blueprint $table) use ($key) {
                    $table->index($key);
                });
            }
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
        foreach (['transfers_id', 'transfers_type'] as $key) {
            if (Schema::hasColumn('funding_contributions', $key)) {
                Schema::table('funding_contributions', function (Blueprint $table) use ($key) {
                    $table->dropColumn($key);
                });
            }
        }
    }
}
