<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundedPolymorphTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('funded', function (Blueprint $table) {
//            $table->increments('id');
//            $table->integer('fundable_id')->unsigned()->index();
//            $table->string('fundable_type')->index();
//
//            $table->integer('funding_contributions_id')->unsigned()->index();
//            $table->foreign('funding_contributions_id')->references('id')->on('users')->onDelete('cascade');
//
//            $table->index(['fundable_id', 'fundable_type', 'funding_contributions_id']);
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funded');
    }
}
