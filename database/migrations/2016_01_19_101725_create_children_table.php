<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('children', function (Blueprint $table) {
            $table->increments('id');

            // parent user id : refer to users table
            $table->integer('parent_id')->unsigned();
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');

            // first name
            $table->string('first_name', 50);

            // last name
            $table->string('last_name', 50);

            // birthday
            $table->date('birthday');

            // text to describe what the child wants
            $table->mediumText('wants')->nullable();


            // avatar reference id to medias table
            $table->biginteger('avatar_id')->unsigned()->nullable();
            $table->foreign('avatar_id')->references('id')->on('media')->onDelete('set null');

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
        Schema::drop('children');
    }
}
