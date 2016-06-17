<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundingContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_contributions', function (Blueprint $table) {
            $table->increments('id');

            // user id : refer to users table
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // user id : refer to users table
            $table->integer('to_user_id')->unsigned();
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');

            // child id : refer to children table
            $table->integer('child_id')->unsigned();
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');

//            // funding account id : refer to funding_accounts table
              //moved to 2016_03_17_102805_create_create_funded_polymorph_table
//            $table->integer('fundables_id')->unsigned();
//            $table->foreign('fundables_id')->references('id')->on('fundable')->onDelete('cascade');

            // amount
            $table->float('amount')->unsigned();

            // flag to recure contribution
            $table->boolean('is_recurring')->default(false);

            // recurring type
            $table->enum('recurring_type', ['onetime', 'daily', 'weekly', 'monthly', 'yearly'])->nullable();

            // flag to recure contribution
            $table->boolean('is_gift')->default(false);

            // Gift Message
            $table->string('gift_message')->nullable();

            // contribution starting datetime
            $table->timestamp('start_date');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('funding_contributions');
    }
}
