<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDummyRecurringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring_contributions_schedule', function (Blueprint $table) {
            $table->increments('id');
            
            // user id : refer to users table
            $table->integer('user_id')->unsigned();

            // child id : refer to children table
            $table->integer('child_id')->unsigned();

            // amount
            $table->float('amount')->unsigned();

            // recurring type
            $table->enum('recurring_type', ['onetime', 'daily', 'weekly', 'monthly', 'yearly'])->nullable();

            // contribution starting datetime
            $table->timestamp('start_date');

            $table->unsignedInteger('fundable_id')->nullable();

            $table->dateTime('checked_at')->nullable();

            $table->tinyInteger('day_of_week')->nullable();
            $table->tinyInteger('day_of_month')->nullable();
            $table->tinyInteger('day_of_year')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_enabled')->default(true);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurring_contributions_schedule', function (Blueprint $table) {
            $table->dropIfExists('recurring_contributions_schedule');
        });
    }
}
