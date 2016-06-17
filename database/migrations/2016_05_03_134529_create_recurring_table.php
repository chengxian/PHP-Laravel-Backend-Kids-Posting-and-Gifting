<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecurringTable extends Migration
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
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // child id : refer to children table
            $table->integer('child_id')->unsigned();
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');

            // amount
            $table->float('amount')->unsigned();

            // recurring type
            $table->enum('recurring_type', ['onetime', 'daily', 'weekly', 'monthly', 'yearly'])->nullable();

            // contribution starting datetime
            $table->timestamp('start_date');

            $table->unsignedInteger('fundable_id')->nullable();
            $table->foreign('fundable_id')->references('id')->on('fundables')->onDelete('set null');

            $table->dateTime('checked_at')->nullable();

            $table->tinyInteger('day_of_week')->nullable();
            $table->tinyInteger('day_of_month')->nullable();
            $table->tinyInteger('day_of_year')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_enabled')->default(true);
        });

        // https://github.com/laravel/framework/issues/2979
        foreach (['is_recurring', 'recurring_type', 'start_date'] as $key) {
            if (Schema::hasColumn('funding_contributions', $key)) {
                Schema::table('funding_contributions', function (Blueprint $table) use ($key) {
                    $table->dropColumn($key);
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
        Schema::table('recurring_contributions_schedule', function (Blueprint $table) {
            $table->dropIfExists('recurring_contributions_schedule');
        });
    }
}
