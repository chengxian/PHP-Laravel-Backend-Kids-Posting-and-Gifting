<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFundingContributionsTableFeesCharity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_contributions', function (Blueprint $table) {
            // Kidgifting Fee

            $table->float('fee_percent')->nullable();
            $table->float('fee_amount')->nullable();

            // Parent charity contribution %
            $table->float('contribution_percent')->nullable();
            $table->float('contribution_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // https://github.com/laravel/framework/issues/2979
        $keys = ['fee_percent', 'fee_amount', 'contribution_percent', 'contribution_amount'];
        foreach ($keys as $key) {
            if (Schema::hasColumn('funding_contributions', $key)) {
                Schema::table('funding_contributions', function (Blueprint $table) use ($key) {
                    $table->dropColumn($key);
                });
            }
        }
    }
}
