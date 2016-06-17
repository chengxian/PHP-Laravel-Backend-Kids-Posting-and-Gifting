<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFundingContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_contributions', function (Blueprint $table) {
            $table->unsignedInteger('fundable_id')->nullable();
            $table->foreign('fundable_id')->references('id')->on('fundables')->onDelete('set null');
            $table->string('status')->default('queued');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_contributions', function (Blueprint $table) {
            $table->dropForeign('funding_contributions_fundable_id_foreign');
            $table->dropColumn('fundable_id');
        });
    }
}
